<?php

namespace RobertWP\WebPConverterLite\Admin\Pages\Converter;

use RobertWP\WebPConverterLite\Traits\Singleton;

class WebPConverter
{
    use Singleton;

    /**
     * 将图片转换为 WebP
     *
     * @param string $file_path 原图路径（绝对路径）
     * @return array|false 结构化数据（给 metadata 使用）
     */
    public function convert(string $file_path): false|array
    {
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return false; // 非支持类型
        }

        // 获取 WP 编辑器
        $editor = wp_get_image_editor($file_path);
        if (is_wp_error($editor)) {
            error_log("WebP convert error: " . $editor->get_error_message());
            return false;
        }

        // 输出 WebP 文件路径（绝对路径）
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path);

        // 保存为 WebP
        $result = $editor->save($webp_path, 'image/webp');
        if (is_wp_error($result)) {
            error_log("WebP save error: " . $result->get_error_message());
            return false;
        }

        // 获取图片尺寸
        $size = @getimagesize($webp_path);
        $width  = $size[0] ?? 0;
        $height = $size[1] ?? 0;

        // 转换为 WP 需要的相对路径 uploads/Y/m/file.webp
        $upload_dir = wp_upload_dir();
        $relative_path = str_replace($upload_dir['basedir'] . '/', '', $webp_path);

        return [
            'file'      => $webp_path,      // 绝对路径
            'relative'  => $relative_path,  // WP metadata 需要的相对路径
            'width'     => $width,
            'height'    => $height,
            'mime'      => 'image/webp',
        ];
    }
}
