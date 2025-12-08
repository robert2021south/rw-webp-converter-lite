<?php
namespace RobertWP\WebPConverterLite\Admin;

use RobertWP\WebPConverterLite\Admin\Ui\SettingsRenderer;
use RobertWP\WebPConverterLite\Traits\Singleton;

class Menu {
    use Singleton;

    public function add_settings_menu(): void
    {
        add_submenu_page(
            'tools.php',
            __('RW WebP Converter Lite', 'rw-webp-converter-lite'),
            __('RW WebP Converter', 'rw-webp-converter-lite'),
            'manage_options',
            'rwwcl-settings',                                                           
            [SettingsRenderer::class, 'render_settings_page']
        );
    }

}
