<?php
namespace RobertWP\WebPConverterLite\Admin\Pages\Compressor;

use RobertWP\WebPConverterLite\Traits\Singleton;

class BatchCompressor {
    use Singleton;

    public function handle_batch_optimize_ajax(): void
    {
        check_ajax_referer('rwwcl_nonce', 'nonce');

        $files = get_transient('rwwcl_scan_results');

        if (empty($files)) {
            wp_send_json_error(['message' => __('No files to optimize.', 'rw-webp-converter-lite')]);
        }

        $optimized = 0;
        $compressor = Compressor::get_instance();

        foreach ($files as $file) {
            // 获取附件ID
            $attachment_id = attachment_url_to_postid(wp_get_upload_dir()['baseurl'] . str_replace(wp_get_upload_dir()['basedir'], '', $file));

            $result = $compressor->compress($file, $attachment_id);
            if ($result['success']) {
                $optimized++;
            }
        }

        delete_transient('rwwcl_scan_results');

        wp_send_json_success([
            'optimized' => $optimized
        ]);
    }

}