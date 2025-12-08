<?php

namespace RobertWP\WebPConverterLite\Admin\Pages\Compressor;

use Imagick;
use RobertWP\WebPConverterLite\Traits\Singleton;
use Throwable;

class Compressor
{
    use Singleton;

    private $quality = 70;

    /**
     * 设置压缩质量（由设置页传入的级别）
     */
    public function set_quality($level): void
    {
        $this->quality = match ($level) {
            'low'    => 80,
            'medium' => 70,
            'high'   => 60,
            default  => 70,
        };
    }

    /**
     * 根据文件类型压缩图片
     *
     * @param string $file_path
     * @param int $attachment_id
     * @return array
     */
    public function compress(string $file_path, int $attachment_id): array
    {
        if (!file_exists($file_path)) {
            return [
                'success' => false,
                'message' => 'File does not exist.',
                'original_size' => 0,
                'new_size' => 0,
            ];
        }

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                return $this->compress_jpg($file_path, $attachment_id);
            case 'png':
                return $this->compress_png($file_path, $attachment_id);
            default:
                // 不处理
                return [
                    'success' => false,
                    'message' => 'Unsupported file type.',
                    'original_size' => @filesize($file_path) ?: 0,
                    'new_size' => @filesize($file_path) ?: 0,
                ];
        }
    }

    /**
     * 压缩 JPG / JPEG
     */
    private function compress_jpg(string $file_path, int $attachment_id): array
    {
        $original_size = @filesize($file_path) ?: 0;

        $editor = wp_get_image_editor($file_path);
        if (is_wp_error($editor)) {
            return [
                'success' => false,
                'message' => 'Failed to load image editor: ' . $editor->get_error_message(),
                'original_size' => $original_size,
                'new_size' => $original_size,
            ];
        }

        $editor->set_quality($this->quality);
        $result = $editor->save($file_path);

        if (is_wp_error($result)) {
            return [
                'success' => false,
                'message' => 'Failed to save image: ' . $result->get_error_message(),
                'original_size' => $original_size,
                'new_size' => $original_size,
            ];
        }

        $new_size = @filesize($file_path) ?: $original_size;

        // 写入 postmeta
        update_post_meta($attachment_id, '_rwwcl_original_size', $original_size);
        update_post_meta($attachment_id, '_rwwcl_optimized_size', $new_size);
        update_post_meta($attachment_id, '_rwwcl_optimized', 1);

        error_log("Compressed JPG file: {$file_path}, original_size={$original_size}, new_size={$new_size}");

        return [
            'success' => true,
            'message' => 'JPG image optimized successfully.',
            'original_size' => $original_size,
            'new_size' => $new_size,
        ];
    }

    /**
     * 压缩 PNG
     */
    private function compress_png(string $file_path, int $attachment_id): array
    {
        $original_size = @filesize($file_path) ?: 0;

        try {
            $img = new Imagick($file_path);
            $img->stripImage(); // 去掉元数据
            $img->writeImage($file_path);
            $img->clear();
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Imagick PNG compression failed: ' . $e->getMessage(),
                'original_size' => $original_size,
                'new_size' => $original_size,
            ];
        }

        $new_size = @filesize($file_path) ?: $original_size;

        update_post_meta($attachment_id, '_rwwcl_original_size', $original_size);
        update_post_meta($attachment_id, '_rwwcl_optimized_size', $new_size);
        update_post_meta($attachment_id, '_rwwcl_optimized', 1);

        error_log("Compressed PNG file: {$file_path}, original_size={$original_size}, new_size={$new_size}");

        return [
            'success' => true,
            'message' => 'PNG image optimized successfully (metadata stripped).',
            'original_size' => $original_size,
            'new_size' => $new_size,
        ];
    }


}