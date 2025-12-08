<?php
namespace RobertWP\WebPConverterLite\Core;

class VersionChecker{

    public static function check(): void
    {
        // 防止被其他插件或异常环境调用
        if ( ! defined('RWWCL_PLUGIN_VERSION') ) {
            return;
        }

        $saved_version = get_option(RWWCL_VERSION_OPTION);

        if (version_compare($saved_version, RWWCL_PLUGIN_VERSION, '<')) {
            update_option(RWWCL_VERSION_OPTION, RWWCL_PLUGIN_VERSION);
        }
    }
}
