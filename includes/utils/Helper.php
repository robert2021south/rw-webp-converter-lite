<?php
namespace RobertWP\WebPConverterLite\Utils;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
class TerminateException extends \RuntimeException {}

class Helper
{

    /**
     * 获取 RW WebP Converter Lite 所有设置，带默认值
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
     * 压缩率 = (original - optimized) / original
     * 正值 = 压缩成功
     * 负值 = 体积变大
     *
     * @param int $original_size 原始大小（字节）
     * @param int $optimized_size 优化后大小（字节）
     * @param int $precision 百分比小数位，默认 1
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

        // 防止 -0%
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
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            echo json_encode([
                'success' => true,
                'data'    => $data,
            ]);
            self::terminate();
        }

        wp_send_json_success($data);
    }

    public static function terminate(): void
    {
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            throw new TerminateException('Execution terminated');
        }
        exit;
    }

}


