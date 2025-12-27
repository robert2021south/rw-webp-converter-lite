<?php
namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;

/**
 * RecentConversions: 管理最近转换记录的 transient
 */
class RecentConversions {
    use Singleton;

    private string $transient_key = 'rwwcl_last_converted';
    private int $max_records = 20;
    private int|float $ttl = DAY_IN_SECONDS;

    public function add_record(array $record): void {
        $records = get_transient($this->transient_key) ?: [];

        // 将最新记录插入最前
        array_unshift($records, $record);

        // 保留最近 20 条
        $records = array_slice($records, 0, $this->max_records);

        // 刷新 transient
        set_transient($this->transient_key, $records, $this->ttl);
    }

    public function get_records(): array {
        return get_transient($this->transient_key) ?: [];
    }

    public function remove_records_for_attachment(int $attachment_id): void
    {
        $records = $this->get_records();
        $new = [];
        foreach ($records as $r) {
            if (!isset($r['id']) || intval($r['id']) !== $attachment_id) {
                $new[] = $r;
            } else {
                // 删除文件（record 可能包含 webp_path）
                if (!empty($r['webp_path']) && file_exists($r['webp_path'])) {
                    wp_delete_file($r['webp_path']);
                }
                // 删除同名尺寸的 webp（base-*.webp）
                if (!empty($r['webp_path'])) {
                    $base = pathinfo($r['webp_path'], PATHINFO_FILENAME);
                    $dir  = dirname($r['webp_path']);
                    $glob = glob($dir . '/' . $base . '-*.webp');
                    if ($glob) {
                        foreach ($glob as $f) {
                            wp_delete_file($f);
                        }
                    }
                }
            }
        }
        set_transient($this->transient_key, $new, $this->ttl);
    }

    public function remove_record_by_webp_path(string $webp_path): void
    {
        if (!$webp_path) {
            return;
        }

        $records = $this->get_records();
        $new = [];

        foreach ($records as $r) {
            if (!empty($r['webp_path']) && $r['webp_path'] === $webp_path) {
                // 删除文件
                if (file_exists($r['webp_path'])) {
                    wp_delete_file($r['webp_path']);
                }
                // 删除同名尺寸的 webp（base-*.webp）
                $base = pathinfo($r['webp_path'], PATHINFO_FILENAME);
                $dir  = dirname($r['webp_path']);
                $glob = glob($dir . '/' . $base . '-*.webp');
                if ($glob) {
                    foreach ($glob as $f) {
                        wp_delete_file($f);
                    }
                }
                // 不加入 $new，相当于删除这条记录
            } else {
                $new[] = $r;
            }
        }

        set_transient($this->transient_key, $new, $this->ttl);
    }

    public function clear(): void
    {
        delete_transient($this->transient_key);
    }

}