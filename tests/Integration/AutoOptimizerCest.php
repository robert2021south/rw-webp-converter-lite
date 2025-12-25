<?php
namespace Tests\Integration;

use RobertWP\WebPConverterLite\Admin\Services\AutoOptimizer;
use RobertWP\WebPConverterLite\Admin\Services\RecentConversions;
use RobertWP\WebPConverterLite\Admin\Services\WebPConverter;
use Tests\Support\IntegrationTester;

class AutoOptimizerCest
{
    public function _before(IntegrationTester $I): void
    {
        remove_all_actions( 'delete_attachment' ); // 清除默认 WP 注册钩子

        // 每个测试运行前清除 WordPress 对象缓存
        wp_cache_flush();

        update_option('rwwcl_settings', [
            'auto_optimize'  => 1,
            'webp_quality'   => 80,
            'keep_original'  => 1,
            'overwrite_webp' => 1,
            'skip_small'     => 300,
        ]);

        delete_transient('rwwcl_last_converted');
        RecentConversions::get_instance()->clear(); // 👈 强烈建议

        // 上传目录隔离
        $this->uploadsTestDir = WP_CONTENT_DIR . '/uploads-test';

        if (!is_dir($this->uploadsTestDir)) {
            mkdir($this->uploadsTestDir, 0777, true);
        }

        // 注册 upload_dir 过滤器，确保所有上传到 uploads-test
        add_filter('upload_dir', function ($dirs) {
            $base = $this->uploadsTestDir;

            $dirs['basedir'] = $base;
            $dirs['baseurl'] = content_url('/uploads-test');

            $dirs['path'] = $base . $dirs['subdir'];
            $dirs['url']  = $dirs['baseurl'] . $dirs['subdir'];

            if (!is_dir($dirs['path'])) {
                mkdir($dirs['path'], 0777, true);
            }

            return $dirs;
        });


    }

    public function upload_image_should_generate_webp(IntegrationTester $I): void
    {
        [$attachment_id, $webp_path] = $this->uploadAndConvertImage($I);

        $I->assertSame(
            1,
            (int) get_post_meta($attachment_id, '_rwwcl_converted', true)
        );

        $I->assertFileExists($webp_path);

        $records = RecentConversions::get_instance()->get_records();
        $I->assertNotEmpty($records);
        $I->assertSame($attachment_id, $records[0]['id']);
    }

    public function delete_attachment_should_cleanup_webp(IntegrationTester $I): void
    {
        // 1️⃣ 上传并转换图片，返回 attachment ID
        $arr = $this->uploadAndConvertImage($I);
        $attachment_id = $arr[0];

        // 2️⃣ 获取 RecentConversions 记录
        $records = RecentConversions::get_instance()->get_records();
        $I->assertNotEmpty($records);

        $webp_path = trim($records[0]['webp_path']); // 唯一可信路径

        // ⚡ 3️⃣ 替代 wp_delete_attachment()，只触发 delete_attachment hook
        $ao = AutoOptimizer::get_instance();
        add_action('delete_attachment', [$ao, 'handle_deleted_attachment']);
        do_action('delete_attachment', $attachment_id);

        // 4️⃣ 防止 file_exists 缓存
        clearstatcache();

        // 5️⃣ 验证 WebP 文件已被删除
        $I->assertFileDoesNotExist(realpath($webp_path) ?: $webp_path, 'WebP file should have been deleted');

        // 6️⃣ 验证 RecentConversions 已清空
        $records2 = RecentConversions::get_instance()->get_records();
        $I->assertEmpty($records2, 'RecentConversions should be empty after deletion');
    }

    /**
     * =========================
     * Helpers
     * =========================
     */

    /**
     * 上传图片并触发 WebP 转换
     *
     * @return array{0:int,1:string} [$attachment_id, $webp_path]
     */
    protected function uploadAndConvertImage(IntegrationTester $I): array
    {
        $source = codecept_data_dir('images/Hawkins-energy-level.jpg');
        $uploadDir = wp_upload_dir();

        if (!is_dir($uploadDir['path'])) {
            mkdir($uploadDir['path'], 0777, true);
        }

        $dest = $uploadDir['path'] . '/' . basename($source);
        copy($source, $dest);

        $attachment_id = wp_insert_attachment([
            'guid'           => $uploadDir['url'] . '/' . basename($source),
            'post_mime_type' => 'image/jpeg',
            'post_title'     => 'Test Image',
            'post_status'    => 'inherit',
        ], $dest);

        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Fake WebP converter
        WebPConverter::get_instance()->convert_file_to_webp =
            $this->fakeWebPConverter();

        // 触发生成 metadata
        $metadata = wp_generate_attachment_metadata($attachment_id, $dest);

        $ao = AutoOptimizer::get_instance();
        add_filter(
            'wp_generate_attachment_metadata',
            [$ao, 'handle_upload'],
            10,
            2
        );

        $metadata = apply_filters(
            'wp_generate_attachment_metadata',
            $metadata,
            $attachment_id
        );

        wp_update_attachment_metadata($attachment_id, $metadata);

        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $dest);

        return [$attachment_id, $webp_path];
    }

    /**
     * Fake WebP converter（避免依赖 GD / Imagick）
     */
    protected function fakeWebPConverter(): callable
    {
        return function ($input, $output, $quality = 80) {
            $dir = dirname($output);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            copy($input, $output);

            return [
                'file'          => basename($input),
                'original_path' => $input,
                'webp_path'     => $output,
                'original_url'  => str_replace(
                    WP_CONTENT_DIR,
                    content_url('wp-content'),
                    $input
                ),
                'webp_url'      => str_replace(
                    WP_CONTENT_DIR,
                    content_url('wp-content'),
                    $output
                ),
                'original_size' => filesize($input),
                'webp_size'     => filesize($output),
                'saved'         => 0,
                'time'          => time(),
            ];
        };
    }
}
