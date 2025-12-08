<?php
namespace RobertWP\ImageOptimizerLite\Admin\Pages\Bulk;

use RobertWP\ImageOptimizerLite\Traits\Singleton;

class Scanner {
    use Singleton;

    /**
     * AJAX: 扫描未优化的图片
     */
    public function scan_unoptimized_images(): void
    {
        check_ajax_referer('rwwcl_nonce', 'nonce');

        // 扫描上传目录
        $uploads = wp_get_upload_dir();
        $path = $uploads['basedir'];

        $images = $this->find_images($path);

        // 保存到 transient，供批量优化使用
        set_transient('rwwcl_scan_results', $images, 3600);

        wp_send_json_success([
            'count'  => count($images),
            'files'  => $images,
        ]);
    }

    /**
     * 查找未优化的图片：检查 meta 是否存在
     */
    private function find_images($base_dir): array {
        $images = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($base_dir)
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;

            $path = $file->getPathname();

            if (!preg_match('/\.(jpg|jpeg|png)$/i', $path)) {
                continue;
            }

            // 检查是否已优化
            $hash = md5($path);
            $meta = get_option('rwwcl_meta_' . $hash);

            if (!$meta || empty($meta['optimized'])) {
                $images[] = $path;
            }
        }

        return $images;
    }
}