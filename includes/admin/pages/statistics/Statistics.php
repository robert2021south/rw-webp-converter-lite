<?php

namespace RobertWP\ImageOptimizerLite\Admin\Pages\Statistics;

use RobertWP\ImageOptimizerLite\Traits\Singleton;

class Statistics {
    use Singleton;

    /**
     * 返回统计摘要：
     * - total_before: 优化前总大小（格式化字符串）
     * - total_after:  优化后总大小（格式化字符串）
     * - saved:        节省空间（格式化字符串）
     * - percent:      节省百分比（数字）
     * - optimized_count: 已优化图片数量（整数）
     *
     * 统计逻辑说明：
     * - 如果附件具有 postmeta '_rwio_original_size'（压缩前大小），则视为已优化；
     *   original_size 会在 Compressor 压缩时写入（见示例）。
     * - 优化后大小使用当前文件系统大小（get_attached_file -> filesize）。
     */
    public function get_summary(): array
    {
        global $wpdb;

        $args = [
            'post_type' => 'attachment',
            'post_mime_type' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'posts_per_page' => -1,
            'post_status' => 'inherit',
            'fields' => 'ids',
        ];

        $attachment_ids = get_posts($args);

        $total_before_bytes = 0;
        $total_after_bytes  = 0;
        $optimized_count = 0;

        foreach ($attachment_ids as $att_id) {
            $file = get_attached_file($att_id);
            if (! $file || ! file_exists($file)) {
                continue;
            }

            $current_size = @filesize($file);
            if ($current_size === false) {
                $current_size = 0;
            }

            // 从 postmeta 获取原始大小（在压缩时写入），key 可按需更改
            $original_size = (int) get_post_meta($att_id, '_rwiol_original_size', true);

            if ($original_size > 0) {
                // 已有记录，认为曾被优化
                $optimized_count++;
                $total_before_bytes += $original_size;
                $total_after_bytes  += $current_size;
            } else {
                // 未记录为已优化：把当前大小计为"优化前"，优化后视为当前大小（即节省0）
                $total_before_bytes += $current_size;
                $total_after_bytes  += $current_size;
            }
        }

        $saved_bytes = max(0, $total_before_bytes - $total_after_bytes);
        $percent = $total_before_bytes ? round(($saved_bytes / $total_before_bytes) * 100, 2) : 0;

        return [
            'total_before' => size_format($total_before_bytes),
            'total_after' => size_format($total_after_bytes),
            'saved' => size_format($saved_bytes),
            'percent' => $percent,
            'optimized_count' => (int) $optimized_count,
        ];
    }
}