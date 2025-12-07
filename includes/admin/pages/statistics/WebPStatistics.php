<?php
namespace RobertWP\ImageOptimizerLite\Admin\Pages\Statistics;

use RobertWP\ImageOptimizerLite\Traits\Singleton;

class WebPStatistics
{
    use Singleton;

    private $per_page = 10;

    /**
     * 获取附件统计数据
     */
    public function get_stats(int $paged = 1, string $search = ''): array
    {
        global $wpdb;

        $offset = ($paged - 1) * $this->per_page;
        $where = "WHERE post_type = 'attachment'";
        if ($search) {
            $search = esc_sql($search);
            $where .= " AND post_title LIKE '%{$search}%'";
        }

        $results = $wpdb->get_results(
            "SELECT ID, post_title, guid
             FROM {$wpdb->posts}
             {$where}
             ORDER BY ID DESC
             LIMIT {$this->per_page} OFFSET {$offset}"
        );

        $stats = [];
        foreach ($results as $row) {
            $meta = wp_get_attachment_metadata($row->ID);
            $webp = $meta['webp'] ?? null;
            $original_size = $meta['_rwiol_original_size'] ?? 0;
            $optimized_size = $meta['_rwiol_optimized_size'] ?? $original_size;

            $stats[] = [
                'ID'              => $row->ID,
                'title'           => $row->post_title,
                'url'             => $row->guid,
                'thumb'           => wp_get_attachment_image_src($row->ID, [80, 80])[0] ?? '',
                'webp_exists'     => (bool)$webp,
                'webp_file'       => $webp['file'] ?? '',
                'original_size'   => $original_size,
                'optimized_size'  => $optimized_size,
                'compression_rate'=> $original_size > 0 ? round(100 - ($optimized_size / $original_size * 100), 1) : 0,
            ];
        }

        return $stats;
    }

    /**
     * 渲染后台表格
     */
    public function render_table(int $paged = 1, string $search = ''): void
    {
        $stats = $this->get_stats($paged, $search);

        // 搜索表单
        echo '<form method="get"><input type="hidden" name="page" value="rwiol_webp_stats">';
        echo '<input type="text" name="s" placeholder="Search by title or ID" value="' . esc_attr($search) . '">';
        echo '<button type="submit" class="button">Search</button>';
        echo '</form><br>';

        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr>
                <th>Thumb</th>
                <th>ID</th>
                <th>Title</th>
                <th>WebP</th>
                <th>Original Size</th>
                <th>Optimized Size</th>
                <th>Compression Rate</th>
              </tr></thead>';
        echo '<tbody>';

        foreach ($stats as $item) {
            echo '<tr>';
            echo '<td>' . ($item['thumb'] ? '<img src="'.esc_url($item['thumb']).'" width="50">' : '-') . '</td>';
            echo '<td>' . esc_html($item['ID']) . '</td>';
            echo '<td>' . esc_html($item['title']) . '</td>';
            echo '<td>' . ($item['webp_exists'] ? '✅' : '❌') . '</td>';
            echo '<td>' . size_format($item['original_size']) . '</td>';
            echo '<td>' . size_format($item['optimized_size']) . '</td>';
            echo '<td>' . $item['compression_rate'] . '%</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // 分页
        $this->render_pagination($paged, $search);
    }

    private function get_total_count(string $search = ''): int
    {
        global $wpdb;
        $where = "WHERE post_type = 'attachment'";
        if ($search) {
            $search = esc_sql($search);
            $where .= " AND post_title LIKE '%{$search}%'";
        }
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} {$where}");
    }

    private function render_pagination(int $paged, string $search = ''): void
    {
        $total = $this->get_total_count($search);
        $pages = ceil($total / $this->per_page);
        if ($pages <= 1) return;

        $base_url = admin_url('admin.php?page=rwiol_webp_stats');
        if ($search) $base_url .= '&s=' . urlencode($search);

        echo '<div class="tablenav"><div class="tablenav-pages">';
        for ($i = 1; $i <= $pages; $i++) {
            $class = $i == $paged ? 'current' : '';
            echo '<a class="' . $class . '" href="' . esc_url($base_url . '&paged=' . $i) . '">' . $i . '</a> ';
        }
        echo '</div></div>';
    }
}
