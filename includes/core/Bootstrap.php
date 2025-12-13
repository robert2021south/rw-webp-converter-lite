<?php
namespace RobertWP\WebPConverterLite\Core;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class Bootstrap {
    private static bool $initialized = false;

    public static function run(): void {
        if (self::$initialized) {
            return;
        }

        HooksRegistrar::register();

        TemplateLoader::init(plugin_dir_path(RWWCL_PLUGIN_FILE));

        self::$initialized = true;
    }

    public static function activate(): void {
        update_option(RWWCL_VERSION_OPTION, RWWCL_PLUGIN_VERSION);

        $defaults = [
            'auto_optimize'            => 1,
            'webp_quality'             => 80,
            'keep_original'            => 1,
            'overwrite_webp'           => 0,
            'skip_small'               => 300,
            'delete_data_on_uninstall' => 0,
        ];

        $existing = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION, []);
        $data = wp_parse_args($existing, $defaults);

        update_option( SettingsRegistrar::RWWCL_SETTINGS_OPTION, $data );
    }

    public static function deactivate(): void {

    }

    public static function uninstall(): void {

    }


}