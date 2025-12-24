<?php
namespace RobertWP\WebPConverterLite\Admin\Ajax;

use RobertWP\WebPConverterLite\Admin\Services\RecentConversions;
use RobertWP\WebPConverterLite\Admin\Services\WebPConverter;
use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\Helper;

class BulkConverter
{
    use Singleton;

    const PROGRESS_KEY = 'rwwcl_bulk_progress';
    const TOTAL_KEY    = 'rwwcl_total_images';

    /**
     * hook callback
     * AJAX 批处理入口
     */
    public function handle_request(): void
    {
        if (!defined('WP_ENV') || WP_ENV !== 'testing') {
            check_ajax_referer('rwwcl_bulk_nonce', 'nonce');
        }

        $batch = (defined('WP_ENV') && WP_ENV === 'testing') ? -1 : 1;

        $images = $this->get_unconverted_images($batch);

        if (empty($images)) {
            Helper::send_json_success([
                'finished' => true,
                'progress' => 100,
                'message'  => __('All images converted!', 'rw-webp-converter-lite'),
            ]);
        }

        $settings = Helper::get_settings();
        $converted_records = $this->process_batch($images, $settings);

        // 更新进度
        $progress = $this->increment_progress(count($converted_records));

        // 初始化或获取 total
        $total = $this->ensure_total_count($progress);

        $percent = $this->calculate_percent($progress, $total);

        Helper::send_json_success([
            'finished'  => false,
            'converted' => count($converted_records),
            'progress'  => $percent,
            'recent'    => $converted_records,
        ]);
    }

    /**
     * 批量处理图片
     */
    private function process_batch(array $attachment_ids, array $settings): array
    {
        $results = [];
        $overwrite = !empty($settings['overwrite_webp']);
        $keep_original = !empty($settings['keep_original']);
        $quality = (int) $settings['webp_quality'];
        $skip_threshold = (int) $settings['skip_small'];

        foreach ($attachment_ids as $id) {
            $file_path = get_attached_file($id);
            if (!$file_path) {
                continue;
            }

            // 判断是否跳过小图
            if ($skip_threshold > 0) {
                $size = getimagesize($file_path);
                if ($size) {
                    $longest = max($size[0], $size[1]);
                    if ($longest <= $skip_threshold) {
                        update_post_meta($id, '_rwwcl_skipped_small', 1);
                        continue;
                    }
                }
            }

            $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path, 1);

            // 文件存在且不覆盖
            if (file_exists($webp_path) && !$overwrite) {
                update_post_meta($id, '_rwwcl_converted', 1);
                $results[] = $this->build_existing_record($id, $file_path, $webp_path);
                continue;
            }

            // 调用纯转换器 WebPConverter
            $converter = WebPConverter::get_instance();
            $result = $converter->convert_file_to_webp($file_path, $webp_path, $quality);

            if ($result) {
                // 如果不保留原图则删除
                if (!$keep_original && file_exists($file_path)) {
                    @unlink($file_path);
                }

                // 更新 meta
                update_post_meta($id, '_rwwcl_converted', 1);

                // 统一记录 RecentConversions
                $record = $this->format_conversion_result($id, $result);
                RecentConversions::get_instance()->add_record($record);

                $results[] = $record;
            }
        }

        return $results;
    }

    /**
     * 针对“文件存在但不覆盖”的情况创建记录
     */
    private function build_existing_record(int $id, string $file_path, string $webp_path): array
    {
        $original_url = wp_get_attachment_url($id);
        $webp_url     = preg_replace('/\.(jpe?g|png)$/i', '.webp', $original_url, 1);

        $original_size = @filesize($file_path);
        $webp_size = @filesize($webp_path);

        return [
            'id'            => $id,
            'file'          => basename($webp_path),
            'original_url'  => $original_url,
            'webp_url'      => $webp_url,
            'original_size' => $original_size,
            'webp_size'     => @filesize($webp_path),
            'saved'         => $original_size - $webp_size,
            'time'          => time(),
        ];
    }

    /**
     * 将 AutoOptimizer::convert_single_file() 的返回格式整理成批处理统一格式
     */
    private function format_conversion_result(int $id, array $result): array
    {
        return [
            'id'            => $id,
            'file'          => basename($result['original_path']),
            'original_url'  => $result['original_url'],
            'webp_url'      => $result['webp_url'],
            'original_size' => $result['original_size'],
            'webp_size'     => $result['webp_size'],
            'saved'         => $result['saved'],
            'time'          => time(),
            'webp_path'     => $result['webp_path'] ?? '',
        ];
    }

    /**
     * 批量写入 recent records（统一结构）
     */
//    private function store_recent_records(array $records): void
//    {
//        $recent = RecentConversions::get_instance();
//        foreach ($records as $r) {
//            $recent->add_record($r);
//        }
//    }

    /**
     * 计算批处理进度
     */
    private function increment_progress(int $count): int
    {
        $p = (int) get_transient(self::PROGRESS_KEY);
        $p += $count;
        set_transient(self::PROGRESS_KEY, $p, DAY_IN_SECONDS);
        return $p;
    }

    /**
     * 首次批处理时记录 total 总数
     */
    private function ensure_total_count(int $progress): int
    {
        $total = get_transient(self::TOTAL_KEY);
        if (!$total) {
            // -1 表示不分页 → 获取所有未转换的
            $remaining = $this->get_unconverted_images(-1);
            $total = count($remaining) + $progress;
            set_transient(self::TOTAL_KEY, $total, DAY_IN_SECONDS);
        }
        return $total;
    }

    /**
     * 百分比计算
     */
    private function calculate_percent(int $progress, int $total): int
    {
        if ($total <= 0) return 0;
        return min(100, (int) round($progress / $total * 100));
    }

    /**
     * 重置进度
     */
    private function reset_progress(): void
    {
        delete_transient(self::PROGRESS_KEY);
        delete_transient(self::TOTAL_KEY);
    }

    /**
     * 查询所有“未转换的 JPEG/PNG”
     */
    private function get_unconverted_images(int $limit): array
    {
        $q = new \WP_Query([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => ['image/jpeg', 'image/png'],
            'posts_per_page' => $limit,
            'meta_query'     => [
                [
                    'key'     => '_rwwcl_converted',
                    'compare' => 'NOT EXISTS',
                ],
            ]
        ]);

        return wp_list_pluck($q->posts, 'ID');
    }

    public function get_queue(): array
    {
        // -1 表示获取所有未转换图片
        return $this->get_unconverted_images(-1);
    }
}
