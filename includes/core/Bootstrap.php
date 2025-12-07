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

        TemplateLoader::init(plugin_dir_path(RWIOL_PLUGIN_FILE));

        self::$initialized = true;
    }

    public static function activate(): void {
        update_option(RWIOL_VERSION_OPTION, RWIOL_PLUGIN_VERSION);

        $data = [];
        $data['auto_optimize'] = 1;
        $data['quality'] = 'medium';
        $data['webp'] = 1;
        update_option( SettingsRegistrar::RWIOL_SETTINGS_OPTION, $data );
    }

    public static function deactivate(): void {

    }

    public static function uninstall(): void {

    }


}