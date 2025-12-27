<?php
namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\Helper;

/**
 * AutoOptimizer: 负责与 WP 附件钩子集成，调用 WebPConverter 与 RecentConversions
 */
class AutoOptimizer {
    use Singleton;

    /**
     * hook callback
     * 删除附件时调用，清理相关 webp 与记录
     * @param int $attachment_id
     * @return void
     */
    public function handle_deleted_attachment(int $attachment_id): void
    {
        RecentConversions::get_instance()->remove_records_for_attachment($attachment_id);

        // 重新计算统计（保持旧行为）
        if (class_exists('Statistics')) {
            Statistics::get_instance()->recalculate();
        }
    }

    /**
     * hook callback
     * 处理新上传图片转WebP
     * @param array $metadata
     * @param int $attachment_id
     * @return array
     */
    public function handle_upload(array $metadata, int $attachment_id): array
    {
        // 读取设置
        $settings = Helper::get_settings();
        if (empty($settings['auto_optimize'])) {
            return $metadata;
        }

        $mime = get_post_mime_type($attachment_id);
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return $metadata;
        }

        $file_path = get_attached_file($attachment_id);
        if (!$file_path) {
            return $metadata;
        }

        // 调用通用处理
        $this->process_conversion_for_attachment($attachment_id, $file_path);

        return $metadata;
    }

    /**
     * 编辑图片->保存图片->点击“更新”按钮后重新针对主文件进行转换WebP
     *
     * @param string $new_file_path 编辑后的主文件路径（带 -e 后缀）
     * @param int $attachment_id
     * @return false|array
     */
    public function convert_single_file(string $new_file_path, int $attachment_id): false|array
    {
        if (!file_exists($new_file_path)) {
            return false;
        }

        return $this->process_conversion_for_attachment($attachment_id, $new_file_path);
    }

    /**
     * 核心私有方法：统一的转换流程（可被上传/编辑流程复用）
     *
     * @param int $attachment_id
     * @param string $source_path
     * @return array|false 返回转换结果数组或 false
     */
    private function process_conversion_for_attachment(int $attachment_id, string $source_path): false|array
    {

        //Get configuration
        $settings = Helper::get_settings();
        $overwrite = !empty($settings['overwrite_webp']);
        $keep_original = !empty($settings['keep_original']);
        $quality = (int) $settings['webp_quality'];
        $skip_threshold = (int) $settings['skip_small'];

        //1. If Skip small image or not
        if ($this->should_skip_image($source_path, $skip_threshold)) {
            return false;
        }

        // WebP file path
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $source_path, 1);

        //2. 判断是否是编辑生成的新主文件
        $is_new_master_file = preg_match('/^(.*?)-e\d+\.(jpg|jpeg|png)$/i', basename($source_path), $matches) === 1;
        if ($is_new_master_file) {
            $this->cleanup_old_webps_for_edited_image($source_path, $matches);
        }

        //3. 如果已经存在 webp 且 不需要覆盖，则跳过
        if (file_exists($webp_path) && !$overwrite) {
            return false;
        }

        //4. Call WebPConverter
        $converter = WebPConverter::get_instance();
        $result = $converter->convert_file_to_webp($source_path, $webp_path, $quality);
        if (!$result) {
            return false;
        }

        //5. 不保留原图则删除（注意：这里遵循旧逻辑 —— Helper::maybe_delete_original 的第二个参数是 keep_original）
        if (!$keep_original) {
            $this->delete_original_attachment_file( $attachment_id);
        }

        //6. Update meta
        update_post_meta($attachment_id, '_rwwcl_converted', 1);

        //7. 记录最近转换（RecentConversions）
        $record = [
            'id'            => $attachment_id,
            'file'          => basename($result['file']),
            'original_url'  => $result['original_url'],
            'webp_url'      => $result['webp_url'],
            'original_size' => $result['original_size'],
            'webp_size'     => $result['webp_size'],
            'saved'         => $result['original_size'] - $result['webp_size'],
            'time'          => time(),
            'webp_path'     => $result['webp_path'],
        ];
        RecentConversions::get_instance()->add_record($record);

        return $result;
    }

    private function cleanup_old_webps_for_edited_image(string $source_path, array $matches): void
    {
        $base_name = $matches[1]; // 原图基础名
        $dir = dirname($source_path);
        $basename = basename($source_path);
        $new_webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $basename);

        // 找到原图及上一次编辑生成的 WebP
        $old_webps = glob($dir . '/' . $base_name . '*.webp');

        foreach ($old_webps as $webp) {
            if ($webp !== $new_webp && file_exists($webp)) {
                wp_delete_file($webp);

                // 同步 Recent Conversions
                RecentConversions::get_instance()->remove_record_by_webp_path($webp);
            }
        }
    }

    private function should_skip_image(string $source_path, int $skip_threshold): bool
    {

        if ($skip_threshold <= 0) {
            // 0 表示不跳过任何图片
            return false;
        }

        $size = getimagesize($source_path);
        if (!$size) {
            return false;
        }

        $width = $size[0];
        $height = $size[1];
        $longest_edge = max($width, $height);

        return $longest_edge <= $skip_threshold;
    }

    /**
     * 删除 attachment 的物理文件（只在确实要删除时调用）
     * 保留一个独立方法方便测试与替换行为（不再依赖 Helper::maybe_delete_original）
     *
     * @param int $attachment_id
     * @return void
     */
    public function delete_original_attachment_file(int $attachment_id): void
    {
        $file = get_attached_file($attachment_id);
        if ($file && file_exists($file)) {
            wp_delete_file($file);
        }
    }

}