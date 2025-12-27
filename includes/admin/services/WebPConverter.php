<?php

namespace RobertWP\WebPConverterLite\Admin\Services;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\Helper;

/**
 * WebPConverter: Single responsibility: convert a file to webp (and return conversion information)
 */
class WebPConverter {
    use Singleton;

    /**
     * Convert $input_path to WebP and save to $output_path (overwrite or not is decided by the caller).
     * Return conversion information array (similar to the old convert() interface), return false on failure.
     *
     * Note: This method does not perform attachment-related meta updates or transient operations.
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
            return false;
        }

        $orig_size = @filesize($input_path);

        // set_quality supports int
        $editor->set_quality($quality);

        // Save to the target path (Note: The target directory must be writable)
        $result = $editor->save($output_path, 'image/webp');

        if (is_wp_error($result)) {
            return false;
        }

        $webp_size = @filesize($output_path);

        // Generate the corresponding URL based on the upload directory
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
            'saved'         => $orig_size - $webp_size,
            'time'          => time(),
        ];
    }

    /**
     * hook callback
     * This method will be called both after uploading images and after clicking the "Update" button when modifying images.
     */
    public function after_edit_metadata($metadata, $attachment_id)
    {
        if (!preg_match('/-e\d+\.(jpg|jpeg|png)$/i', basename($metadata['file']))) {
            return $metadata;
        }

        $file_path = trailingslashit(wp_upload_dir()['basedir']) . $metadata['file'];

        if (!file_exists($file_path)) {
            return $metadata;
        }

        // Force convert WebP, invoke the pure converter
        $converter = WebPConverter::get_instance();
        $webp_path = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file_path, 1);
        $result = $converter->convert_file_to_webp($file_path, $webp_path, (int) Helper::get_settings()['webp_quality']);

        if ($result) {
            update_post_meta($attachment_id, '_rwwcl_converted', 1);

            // Record RecentConversions
            $record = [
                'id'            => $attachment_id,
                'file'          => basename($result['original_path']),
                'original_url'  => $result['original_url'],
                'webp_url'      => $result['webp_url'],
                'original_size' => $result['original_size'],
                'webp_size'     => $result['webp_size'],
                'saved'         => $result['saved'],
                'time'          => time(),
                'webp_path'     => $result['webp_path'] ?? '',
            ];
            RecentConversions::get_instance()->add_record($record);
        }

        return $metadata;
    }


}
