<?php
namespace RobertWP\WebPConverterLite\Utils;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
class TerminateException extends \RuntimeException {}

class Helper
{

    /**
     * Retrieve all settings for RW WebP Converter Lite with default values
     */
    public static function get_settings(): array
    {
        $defaults = [
            'auto_optimize' => true,
            'webp_quality'  => 80,
            'keep_original' => true,
            'overwrite_webp' => true,
            'skip_small' => 300,
            'delete_data_on_uninstall' => 0
        ];

        $settings = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION, []);
        return wp_parse_args($settings, $defaults);
    }

    /**
     * Format image compression result.
     *
     * Compression rate = (original - optimized) / original
     * Positive value = compression successful
     * Negative value = increased file size
     *
     * @param int $original_size Original size (in bytes)
     * @param int $optimized_size Optimized size (in bytes)
     * @param int $precision Decimal places for percentage, default 1
     *
     * @return string Escaped text, safe for HTML output
     */
    public static function image_compression_text(int $original_size, int $optimized_size, int $precision = 1): string
    {

        if ($original_size <= 0 || $optimized_size < 0) {
            return 'N/A';
        }

        $diff_bytes = abs($original_size - $optimized_size);
        $rate = ($original_size - $optimized_size) / $original_size * 100;

        // Prevent showing -0%
        if (abs($rate) < 0.1) {
            $rate = 0;
        }

        $sign = $rate >= 0 ? '+' : '-';

        return sprintf('%s (%s%s%%)', size_format($diff_bytes,2), $sign, round(abs($rate), $precision));
    }

    public static function get_upgrade_url( $source = ''): string
    {

        $base_url = 'https://robertwp.com/rw-webp-converter-pro/';
        if ( $source ) {
            return add_query_arg( 'source', $source, $base_url );
        }
        return $base_url;

    }

    public static function send_json_success(array $data): void
    {
        wp_send_json_success($data);
    }

}


