<?php
namespace RobertWP\WebPConverterLite\Core;

class VersionChecker{

    public static function check(): void
    {
        // Prevent being called by other plugins or in abnormal environments
        if ( ! defined('RWWCL_PLUGIN_VERSION') ) {
            return;
        }

        $saved_version = get_option(RWWCL_VERSION_OPTION);

        if (version_compare($saved_version, RWWCL_PLUGIN_VERSION, '<')) {
            update_option(RWWCL_VERSION_OPTION, RWWCL_PLUGIN_VERSION);
        }
    }
}
