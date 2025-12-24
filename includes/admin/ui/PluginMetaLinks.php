<?php
namespace RobertWP\WebPConverterLite\Admin\UI;

class PluginMetaLinks {
    public static function add_links($links){
        $settings_link = '<a href="' . admin_url('admin.php?page=rwwcl-main&tab=settings') . '">' . __('Settings', 'rw-webp-converter-lite') . '</a>';
        array_unshift($links, $settings_link);
        return $links;

    }
}