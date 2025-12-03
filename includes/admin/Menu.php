<?php
namespace RobertWP\ImageOptimizerLite\Admin;

use RobertWP\ImageOptimizerLite\Admin\Settings\SettingsRenderer;
use RobertWP\ImageOptimizerLite\Traits\Singleton;

class Menu {
    use Singleton;

    public function add_settings_menu(): void
    {
        add_menu_page(
            __('License Manager DB Setup', 'rw-licensemanager-pro'),          // page title
            __('License Manager', 'rw-licensemanager-pro'),              // menu title
            'manage_options',          // Permission requirement
            'rwlmp-settings', // menu slug
            [SettingsRenderer::class, 'render_settings_page'], // Callback function
            'dashicons-shield',
            2
        );

        // 注意：当子菜单中仅存在一个且与主菜单 slug 相同，WordPress 会默认隐藏该子菜单项。
        add_submenu_page(
            'rwlmp-settings',
            __('Settings', 'rw-licensemanager-pro'),
            __('Settings', 'rw-licensemanager-pro'),
            'manage_options',
            'rwlmp-settings',
            [SettingsRenderer::class, 'render_settings_page']
        );


    }


}
