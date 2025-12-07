<?php
namespace RobertWP\ImageOptimizerLite\Core;

class VersionChecker{

    public static function check(): void
    {
        // 防止被其他插件或异常环境调用
        if ( ! defined('RWIOL_PLUGIN_VERSION') ) {
            return;
        }

        $saved_version = get_option(RWIOL_VERSION_OPTION);

        if (version_compare($saved_version, RWIOL_PLUGIN_VERSION, '<')) {
            update_option(RWIOL_VERSION_OPTION, RWIOL_PLUGIN_VERSION);
        }
    }
}
