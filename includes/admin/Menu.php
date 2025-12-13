<?php
namespace RobertWP\WebPConverterLite\Admin;

use RobertWP\WebPConverterLite\Admin\UI\AdminPageRenderer;
use RobertWP\WebPConverterLite\Traits\Singleton;

class Menu {
    use Singleton;

    public function add_settings_menu(): void
    {
        add_management_page(
            __( 'RW WebP Converter Lite', 'rw-webp-converter-lite' ),
            __( 'RW WebP Converter', 'rw-webp-converter-lite' ),
            'manage_options',
            'rwwcl-main',
            [AdminPageRenderer::class, 'render_main_page']
        );
    }
}
