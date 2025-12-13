<?php

namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;

/**
 * WebPConverter: 单一职责：把一个文件转换为 webp（并返回转换信息）
 */
class WebPConverter {
    use Singleton;

    /**
     * 将 $input_path 转成 WebP 保存到 $output_path（覆盖/不覆盖由调用方决定）。
     * 返回转换后的信息数组（与旧的 convert() 接口类似），失败返回 false。
     *
     * 注意：本方法不做 attachment 相关的 meta 更新或 transient 操作。
     *
     * @param string $input_path
     * @param string $output_path
     * @param int $quality
     * @return array|false
     */
    public function convert_file_to_webp(string $input_path, string $output_path, int $quality = 80): false|array
    {
        if (!file_exists($input_path)) {
            return false;
        }

        $ext = strtolower(pathinfo($input_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return false;
        }

        $editor = wp_get_image_editor($input_path);
        if (is_wp_error($editor)) {
            error_log('WebPConverter: wp_get_image_editor failed for ' . $input_path);
            return false;
        }

        $orig_size = @filesize($input_path);

        // set_quality 支持 int
        $editor->set_quality($quality);

        // 保存到目标路径（注意：目标目录需要可写）
        $result = $editor->save($output_path, 'image/webp');

        if (is_wp_error($result)) {
            error_log('WebPConverter: save failed for ' . $output_path . ' - ' . $result->get_error_message());
            return false;
        }

        $webp_size = @filesize($output_path);

        // 生成基于上传目录的 URL 对应
        $upload_dir = wp_upload_dir();
        $webp_url  = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $output_path);
        $orig_url  = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $input_path);

        return [
            'file'          => basename($input_path),
            'original_path' => $input_path,
            'webp_path'     => $output_path,
            'original_url'  => $orig_url,
            'webp_url'      => $webp_url,
            'original_size' => $orig_size,
            'webp_size'     => $webp_size,
            'saved'         => max($orig_size - $webp_size, 0),
            'time'          => time(),
        ];
    }

    /**
     * hook callback
     * 上传图片和修改图片后点击“更新”按钮后都会调用本方法。
     */
    public function after_edit_metadata($metadata, $attachment_id)
    {
        // 判断是否为编辑后的文件（带 -e123456）
        if (!preg_match('/-e\d+\.(jpg|jpeg|png)$/i', basename($metadata['file']))) {
            return $metadata;
        }

        $file_path = trailingslashit(wp_upload_dir()['basedir']) . $metadata['file'];

        if (!file_exists($file_path)) {
            return $metadata;
        }

        // 强制转换 WebP
        AutoOptimizer::get_instance()->convert_single_file($file_path, $attachment_id);
        return $metadata; // 一定要返回!!!
    }


}
