<?php
namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;

/**
 * RecentConversions: Manages recently converted records via transient
 */
class RecentConversions {
    use Singleton;

    private string $transient_key = 'rwwcl_last_converted';
    private int $max_records = 20;
    private int|float $ttl = DAY_IN_SECONDS;

    public function add_record(array $record): void {
        $records = get_transient($this->transient_key) ?: [];

        // Insert the latest record at the beginning
        array_unshift($records, $record);

        // Keep the most recent 20 records
        $records = array_slice($records, 0, $this->max_records);

        // Refresh the transient
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
                // Delete file (the record may contain webp_path)
                if (!empty($r['webp_path']) && file_exists($r['webp_path'])) {
                    wp_delete_file($r['webp_path']);
                }
                // Delete webp files of matching sizes (base-.webp)
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
                // Delete file
                if (file_exists($r['webp_path'])) {
                    wp_delete_file($r['webp_path']);
                }
                // Delete webp files of matching sizes (base-.webp)
                $base = pathinfo($r['webp_path'], PATHINFO_FILENAME);
                $dir  = dirname($r['webp_path']);
                $glob = glob($dir . '/' . $base . '-*.webp');
                if ($glob) {
                    foreach ($glob as $f) {
                        wp_delete_file($f);
                    }
                }
                // Not adding to $new, equivalent to removing this record
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