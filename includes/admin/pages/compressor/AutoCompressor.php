<?php

namespace RobertWP\WebPConverterLite\Admin\Pages\Compressor;

use RobertWP\WebPConverterLite\Admin\Pages\Converter\WebPConverter;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Traits\Singleton;
use Throwable;

class AutoCompressor
{
    use Singleton;

    /**
     * 在附件生成完毕后处理压缩
     *
     * @param array $metadata 附件元数据
     * @param int $attachment_id 附件 ID
     * @return array 返回元数据
     */
    public function handle_attachment_metadata(array $metadata, int $attachment_id): array
    {

        $settings = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION);
        if (empty($settings['auto_optimize']) || $settings['auto_optimize'] != '1') {
            return $metadata;
        }

        // 获取附件文件路径
        $file = get_attached_file($attachment_id);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
            return $metadata;
        }

        // 调用压缩逻辑
        try {
            // 1. Compressed
            $result = Compressor::get_instance()->compress($file, $attachment_id);
            if (!$result['success']) {
                error_log("Compression failed: " . $result['message']);
            }else{
                // 2. generate webp
                if (!empty($settings['webp']) && $settings['webp'] == '1') {
                    $webp_data = WebPConverter::get_instance()->convert($file);
                    if ($webp_data && is_array($webp_data)) {
                        $this->attach_webp_metadata($attachment_id, $webp_data);
                        error_log("Generated WebP file: {$webp_data['file']}");
                    }
                }
            }
        } catch (Throwable $e) {
            error_log("Compression exception: " . $e->getMessage());
        }

        return $metadata;
    }

    private function attach_webp_metadata(int $attachment_id, array $webp_data): void
    {
        if (empty($webp_data['file']) || empty($webp_data['width']) || empty($webp_data['height'])) {
            error_log("WebP metadata missing required fields");
            return;
        }

        // 获取当前 metadata
        $metadata = wp_get_attachment_metadata($attachment_id);
        if (!is_array($metadata)) {
            $metadata = [];
        }

        // 获取 uploads 的 base path (绝对路径)
        $upload_dir = wp_upload_dir();
        $base_dir   = trailingslashit($upload_dir['basedir']);

        // WebP 文件路径（绝对路径 → 相对路径）
        $webp_abs  = $webp_data['file'];
        $webp_rel  = str_replace($base_dir, '', $webp_abs);  // 例如 "2025/12/file.webp"

        // 写入 metadata 的新字段
        $metadata['webp'] = [
            'file'   => $webp_rel,
            'width'  => $webp_data['width'],
            'height' => $webp_data['height'],
            'mime'   => 'image/webp',
        ];

        // 更新 metadata
        wp_update_attachment_metadata($attachment_id, $metadata);

        error_log("WebP metadata saved: {$webp_rel}");
    }

}
