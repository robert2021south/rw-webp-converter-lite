<?php
namespace RobertWP\ImageOptimizerLite\Admin;

use RobertWP\ImageOptimizerLite\Admin\Ui\SettingsRenderer;
use RobertWP\ImageOptimizerLite\Traits\Singleton;

class Menu {
    use Singleton;

    public function add_settings_menu(): void
    {
        add_submenu_page(
            'tools.php',
            __('RW Image Optimizer Lite', 'rw-image-optimizer-lite'),
            __('RW Image Optimizer', 'rw-image-optimizer-lite'),
            'manage_options',
            'rwiol-settings',
            [SettingsRenderer::class, 'render_settings_page']
        );
    }

}
