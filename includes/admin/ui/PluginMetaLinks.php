<?php
namespace RobertWP\ImageOptimizerLite\Admin\UI;

class PluginMetaLinks {
    public static function add_links($links){
        $settings_link = '<a href="' . admin_url('admin.php?page=rwiol-settings') . '">' . __('Settings', 'rw-image-optimizer-lite') . '</a>';
        array_unshift($links, $settings_link);
        return $links;

    }
}