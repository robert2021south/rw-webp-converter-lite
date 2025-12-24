<?php

namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;

class Statistics
{
    use Singleton;

    /**
     * 统一替换扩展名为 .webp
     */
    private function to_webp(string $path): string
    {
        return preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $path,1);
    }

    /**
     * 全局统计
     */
    public function get_global_stats(): array
    {
        $attachments = $this->get_all_image_attachments();

        $total = count($attachments);
        $converted = 0;
        $skipped_small = 0;
        $original_total = 0;
        $original_total_converted  = 0;
        $webp_total = 0;

        foreach ($attachments as $id) {
            $stats = $this->get_image_stats($id);

            $original_total += $stats['original_size'];

            if ($stats['webp_exists']) {
                $converted++;
                //
                $original_total_converted += (int) $stats['original_size'];
                $webp_total += $stats['webp_size'];
            }

            if (get_post_meta($id, '_rwwcl_skipped_small', true)) {
                $skipped_small++;
            }
        }

        $remaining_convertible = max(0, $total - $converted - $skipped_small);

        return [
            'total_images'        => $total,
            'converted_images'    => $converted,
            'skipped_small_images'  => $skipped_small,
            'remaining_convertible'  => $remaining_convertible,
            'conversion_rate'     => $total > 0 ? round(($converted / $total) * 100, 1) : 0,
            'total_original_size' => $original_total,
            'total_webp_size'     => $webp_total,
            'space_saved'         => ( $converted > 0 && $original_total_converted > 0 ) ? max($original_total_converted - $webp_total, 0) : 0
        ];


    }


    /**
     * 图片统计列表
     */
    public function get_converted_images(int $limit = 50, int $offset = 0): array
    {
        $attachments = $this->get_all_image_attachments($limit, $offset);
        $result = [];

        foreach ($attachments as $id) {
            $stats = $this->get_image_stats($id);

            $result[] = [
                'id'            => $id,
                'original_url'  => wp_get_attachment_url($id),
                'webp_url'      => $stats['webp_url'],
                'original_size' => $stats['original_size'],
                'webp_size'     => $stats['webp_size'],
                'saved'         => $stats['original_size'] - $stats['webp_size'],
                'webp_exists'   => $stats['webp_exists'],
            ];
        }

        return $result;
    }


    /**
     * 某图片是否已转换
     */
    public function is_converted(int $id): bool
    {
        $webp = $this->get_webp_path($id);
        return $webp && file_exists($webp);
    }


    /**
     * 某图片统计信息
     */
    public function get_image_stats(int $id): array
    {
        $file = get_attached_file($id);

        if (!$file || !file_exists($file)) {
            return [
                'original_size' => 0,
                'webp_size'     => 0,
                'webp_exists'   => false,
                'webp_url'      => '',
            ];
        }

        $webp = $this->get_webp_path($id);
        $has_webp = $webp && file_exists($webp);

        return [
            'original_size' => filesize($file),
            'webp_size'     => $has_webp ? filesize($webp) : 0,
            'webp_exists'   => $has_webp,
            'webp_url'      => $has_webp ? $this->get_webp_url($id) : '',
        ];
    }


    /**
     * WebP 路径
     */
    public function get_webp_path(int $id): string
    {
        $path = get_attached_file($id);
        return $path ? $this->to_webp($path) : '';
    }


    /**
     * WebP URL
     */
    public function get_webp_url(int $id): string
    {
        $url = wp_get_attachment_url($id);
        return $url ? $this->to_webp($url) : '';
    }


    /**
     * 获取全部图片
     */
    private function get_all_image_attachments(int $limit = -1, int $offset = 0): array
    {
        return get_posts([
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'numberposts'    => $limit,
            'offset'         => $offset,
            'fields'         => 'ids',
            'post_mime_type' => ['image/jpeg', 'image/jpg', 'image/png'],
        ]);
    }


    /**
     * 重新计算已转换数量和节省空间
     */
    public function recalculate(): void
    {
        $attachments = $this->get_all_image_attachments();

        $converted = 0;
        $saved = 0;

        foreach ($attachments as $id) {
            $webp = $this->get_webp_path($id);

            if ($webp && file_exists($webp)) {
                $converted++;
                $saved += filesize(get_attached_file($id)) - filesize($webp);
            }
        }

        set_transient('rwwcl_total_converted', $converted, DAY_IN_SECONDS);
        set_transient('rwwcl_total_saved_bytes', $saved, DAY_IN_SECONDS);
    }
}
