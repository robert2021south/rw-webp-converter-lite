<?php
namespace RobertWP\WebPConverterLite\Utils;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;

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

    public static function get_upgrade_url( $source = ''): string
    {

        $base_url = 'https://robertwp.com/rw-webp-converter-pro/';
        if ( $source ) {
            return add_query_arg( 'source', $source, $base_url );
        }
        return $base_url;

    }

    public static function terminate(): void
    {
        if (defined('WP_ENV') && WP_ENV === 'testing') {
            throw new \Exception('terminate called');
        }
        exit;
    }

}


