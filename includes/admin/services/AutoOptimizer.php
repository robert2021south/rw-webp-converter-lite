<?php
namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\Helper;

/**
 * AutoOptimizer: Responsible for integrating with WP attachment hooks, calling WebPConverter and RecentConversions
 */
class AutoOptimizer {
    use Singleton;

    /**
     * hook callback
     * Called when deleting attachments to clean up related webp files and records
     * @param int $attachment_id
     * @return void
     */
    public function handle_deleted_attachment(int $attachment_id): void
    {
        RecentConversions::get_instance()->remove_records_for_attachment($attachment_id);

        // Recalculating statistics (maintaining legacy behavior)
        if (class_exists('Statistics')) {
            Statistics::get_instance()->recalculate();
        }
    }

    /**
     * hook callback
     * Processing newly uploaded images for WebP conversion
     * @param array $metadata
     * @param int $attachment_id
     * @return array
     */
    public function handle_upload(array $metadata, int $attachment_id): array
    {
        // Reading settings
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

        // Calling general processing
        $this->process_conversion_for_attachment($attachment_id, $file_path);

        return $metadata;
    }

    /**
     * Converting WebP for the main file after editing images → saving images → clicking the "Update" button
     *
     * @param string $new_file_path Path of the edited main file (with -e suffix)
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
     * Core private method: Unified conversion process (reusable for upload/edit workflows)
     *
     * @param int $attachment_id
     * @param string $source_path
     * @return array|false Returns conversion result array or false
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

        //2. Determining if it is a newly generated main file from editing
        $is_new_master_file = preg_match('/^(.*?)-e\d+\.(jpg|jpeg|png)$/i', basename($source_path), $matches) === 1;
        if ($is_new_master_file) {
            $this->cleanup_old_webps_for_edited_image($source_path, $matches);
        }

        //3. Skipping if webp already exists and overwriting is not needed
        if (file_exists($webp_path) && !$overwrite) {
            return false;
        }

        //4. Call WebPConverter
        $converter = WebPConverter::get_instance();
        $result = $converter->convert_file_to_webp($source_path, $webp_path, $quality);
        if (!$result) {
            return false;
        }

        //5. Deleting the original image if not retained (Note: Following legacy logic here — the second
        if (!$keep_original) {
            $this->delete_original_attachment_file( $attachment_id);
        }

        //6. Update meta
        update_post_meta($attachment_id, '_rwwcl_converted', 1);

        //7. Recording recent conversions (RecentConversions)
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
        $base_name = $matches[1]; // Base name of the original image
        $dir = dirname($source_path);
        $basename = basename($source_path);
        $new_webp = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $basename);

        // Finding the original image and the WebP generated from the previous edit
        $old_webps = glob($dir . '/' . $base_name . '*.webp');

        foreach ($old_webps as $webp) {
            if ($webp !== $new_webp && file_exists($webp)) {
                wp_delete_file($webp);

                // Synchronizing Recent Conversions
                RecentConversions::get_instance()->remove_record_by_webp_path($webp);
            }
        }
    }

    private function should_skip_image(string $source_path, int $skip_threshold): bool
    {

        if ($skip_threshold <= 0) {
            // 0 indicates skipping no images
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
     * Deleting physical files of attachment (only called when deletion is confirmed)
     * Keeping an independent method for easier testing and behavior replacement (no longer relying on Helper::maybe_delete_original)
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