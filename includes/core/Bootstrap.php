<?php
namespace RobertWP\ImageOptimizerLite\Core;

use RobertWP\ImageOptimizerLite\Admin\Settings\SettingsRegistrar;
use RobertWP\ImageOptimizerLite\Utils\TemplateLoader;

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

        $data = [];
        $data['auto_optimize'] = 1;
        $data['quality'] = 'medium';
        $data['webp'] = 1;
        update_option( SettingsRegistrar::RWWCL_SETTINGS_OPTION, $data );
    }

    public static function deactivate(): void {

    }

    public static function uninstall(): void {

    }


}