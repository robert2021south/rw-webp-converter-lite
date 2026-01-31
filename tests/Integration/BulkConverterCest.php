<?php
namespace Tests\Integration;

use RobertWP\WebPConverterLite\Admin\Ajax\BulkConverter;
use RobertWP\WebPConverterLite\Admin\Services\Statistics;
use RobertWP\WebPConverterLite\Utils\TerminateException;
use Tests\Support\IntegrationTester;


class BulkConverterCest
{
    public function _before(IntegrationTester $I): void
    {
        add_filter('wp_die_ajax_handler', function () {
            return function ($message = '', $title = '', $args = []) {
                throw new \RuntimeException('ajax_exit');
            };
        });

        update_option('rwwcl_settings', [
            'auto_optimize'            => 0,
            'webp_quality'             => 80,
            'keep_original'            => 1,
            'overwrite_webp'           => 0,
            'skip_small'               => 300,
            'delete_data_on_uninstall' => 0,
        ]);

        BulkConverter::get_instance()->reset_progress();

    }

    public function bulk_convert_should_return_progress(IntegrationTester $I): void
    {
        // 测试图片列表
        $images = [
//            'coutu1.png',
//            'coutu2.png',
            'Shakespeare1920x1080.png',
            '300x300_20250618_083505.png',
            'Hawkins-energy-level.jpg',
//            'Image_2025-08-13_125222_631.jpg',
//            'Image_2025-08-13_125236_895.jpg',
//            'Image_2025-08-13_125248_055.jpg',
//            'Image_2025-08-13_125402_488.jpg',
//            'Image_2025-08-13_131604_701.jpg',
//            '715f1c6064b670b7bde8c3324116bead.jpeg',
        ];

        $ids = [];

        $upload_dir = wp_upload_dir();
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // 插入附件
        foreach ($images as $image) {
            $sourceFile = codecept_data_dir("images/{$image}");

            $destFile = $upload_dir['path'] . '/' . $image;
            copy($sourceFile, $destFile);

            $ids[] = wp_insert_attachment([
                'post_mime_type' => wp_check_filetype($destFile)['type'],
                'post_status'    => 'inherit',
            ], $destFile);

            \wp_generate_attachment_metadata($ids[array_key_last($ids)], $destFile);

        }

        // 模拟 AJAX 请求
        $_POST = [
            'action' => 'rwwcl_bulk_convert',
            'nonce'  => wp_create_nonce('rwwcl_bulk_nonce'),
        ];

        // 清空 BulkConverter 的队列或缓存，避免重复处理
        $converter = BulkConverter::get_instance();

        // 循环处理，直到全部图片转换完成
        $maxLoops = 9;
        $loops = 0;
        $response = [];
        do {
            $loops++;
            ob_start();
            try {
                 $converter->handle_request();
            } catch (TerminateException $e) {
                $json = ob_get_clean();
                $response = json_decode($json, true);
            }

            if ($loops > $maxLoops) {
                $I->fail('Bulk conversion did not finish in expected loops');
            }
        } while (empty($response['data']['finished']));

        // 断言结果
        $I->assertTrue($response['success']);
        $I->assertEquals(100, $response['data']['progress']);

        // 确认 WebP 文件存在
        foreach ($ids as $id) {

            $is_converted = get_post_meta($id, '_rwwcl_converted', true);
            $is_skipped   = get_post_meta($id, '_rwwcl_skipped_small', true);

            if ($is_converted) {
                $I->assertFileExists(
                    Statistics::get_instance()->get_webp_path($id),
                    "Converted image {$id} should have webp file"
                );
            }

            if ($is_skipped) {
                $I->assertFileDoesNotExist(
                    Statistics::get_instance()->get_webp_path($id),
                    "Skipped small image {$id} should NOT have webp file"
                );
            }
        }

    }

}
