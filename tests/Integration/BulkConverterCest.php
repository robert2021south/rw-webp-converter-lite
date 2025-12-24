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
        update_option('rwwcl_settings', [
            'auto_optimize' => 0,
            'overwrite_webp' => 1,
            'keep_original' => 1,
            'skip_small' => 0,
        ]);

        delete_transient('rwwcl_bulk_progress');
        delete_transient('rwwcl_total_images');
        delete_transient('rwwcl_last_converted');
    }

    public function bulk_convert_should_return_progress(IntegrationTester $I): void
    {
        // 测试图片列表
        $images = [
            '300x300_20250618_083505.png',
            '715f1c6064b670b7bde8c3324116bead.jpeg',
            'coutu1.png',
            'coutu2.png',
            'Image_2025-08-13_125222_631.jpg',
            'Image_2025-08-13_125236_895.jpg',
            'Image_2025-08-13_125248_055.jpg',
            'Image_2025-08-13_125402_488.jpg',
            'Image_2025-08-13_131604_701.jpg',
        ];

        $ids = [];

        // 为测试创建唯一上传目录，避免和历史数据冲突
        $uploadBaseDir = wp_upload_dir()['basedir'] . '/test_' . time();
        wp_mkdir_p($uploadBaseDir);

        // 插入附件
        foreach ($images as $image) {
            $sourceFile = codecept_data_dir("images/{$image}");
            $destFile = $uploadBaseDir . '/' . $image;
            copy($sourceFile, $destFile);

            $ids[] = wp_insert_attachment([
                'post_mime_type' => wp_check_filetype($destFile)['type'],
                'post_status'    => 'inherit',
            ], $destFile);
        }

        // 模拟 AJAX 请求
        $_POST = [
            'action' => 'rwwcl_bulk_convert',
            'nonce'  => wp_create_nonce('rwwcl_bulk_nonce'),
        ];

        // 清空 BulkConverter 的队列或缓存，避免重复处理
        $converter = BulkConverter::get_instance();
        $I->comment('队列长度：' . count($converter->get_queue()));
        if (method_exists($converter, 'reset_queue')) {
            $converter->reset_queue();
        }

        // 循环处理，直到全部图片转换完成
        $response = null;
        do {
            ob_start();
            try {
                $converter->handle_request();
            } catch (TerminateException $e) {
                $json = ob_get_clean();
                $response = json_decode($json, true);
                continue;
            }

            // 兜底：防止没有异常但有输出
            ob_end_clean();

        } while (empty($response['data']['finished']) || !$response['data']['finished']);

        // 断言结果
        $I->assertTrue($response['success']);
        $I->assertTrue($response['data']['finished']);
        $I->assertEquals(count($images), $response['data']['converted']);

        // 确认 WebP 文件存在
        foreach ($ids as $id) {
            $I->assertFileExists(
                Statistics::get_instance()->get_webp_path($id)
            );
        }
    }

}
