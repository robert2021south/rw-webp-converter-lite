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
        // 开启自动优化
        update_option('rwwcl_settings', [
            'auto_optimize'  => 1,
            'webp_quality'   => 80,
            'keep_original'  => 1,
            'overwrite_webp' => 1,
            'skip_small'     => 0,
        ]);

        delete_transient('rwwcl_last_converted');       // 设置隔离的上传目录

    }

    public function upload_image_should_generate_webp(IntegrationTester $I): void
    {
        // 1. 准备源图片
        $source = codecept_data_dir('images/Image_2025-08-13_125222_631.jpg');

        // 获取上传目录信息
        $uploadDir = wp_upload_dir(); // 会返回 filter 之后的 uploads-test/年/月

        // 确保目录存在
        if (!is_dir($uploadDir['path'])) {
            mkdir($uploadDir['path'], 0777, true);
        }

        // 目标路径
        $dest = $uploadDir['path'] . '/' . basename($source);

        copy($source, $dest);

        // 2. 插入附件
        $attachment = [
            'guid'           => content_url('uploads-test/2025/12/' . basename($source)),
            'post_mime_type' => 'image/jpeg',
            'post_title'     => 'Test Image',
            'post_content'   => '',
            'post_status'    => 'inherit'
        ];
        $attachment_id = wp_insert_attachment($attachment, $dest);

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $metadata = wp_generate_attachment_metadata($attachment_id, $dest);

        // 3. Fake WebPConverter，避免依赖真实 GD/Imagick
        $fakeConverter = $this->fakeWebPConverter();
        WebPConverter::get_instance()->convert_file_to_webp = $fakeConverter;

        // 4. 手动触发 handle_upload
        $ao = AutoOptimizer::get_instance();
        add_filter('wp_generate_attachment_metadata', [$ao, 'handle_upload'], 10, 2);
        $metadata = apply_filters('wp_generate_attachment_metadata', $metadata, $attachment_id);

        wp_update_attachment_metadata($attachment_id, $metadata);

        // 5. 断言 meta 已更新
        $I->assertSame(1, (int) get_post_meta($attachment_id, '_rwwcl_converted', true));

        //
        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $dest);
        $I->assertFileExists($webp);

        // 6. 断言 RecentConversions 有记录
        $records = RecentConversions::get_instance()->get_records();
        $I->assertNotEmpty($records);
        $I->assertSame($attachment_id, $records[0]['id']);
    }

    /**
     * 返回一个 fake converter，直接复制文件作为“webp”
     */
    protected function fakeWebPConverter(): callable
    {
        return function ($input, $output, $quality = 80) {
            $dir = dirname($output);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            copy($input, $output); // 伪造 webp 文件

            return [
                'file'          => basename($input),
                'original_path' => $input,
                'webp_path'     => $output,
                'original_url'  => str_replace(WP_CONTENT_DIR, content_url('wp-content'), $input),
                'webp_url'      => str_replace(WP_CONTENT_DIR, content_url('wp-content'), $output),
                'original_size' => filesize($input),
                'webp_size'     => filesize($output),
                'saved'         => 0,
                'time'          => time(),
            ];
        };
    }
}
