<?php
namespace RobertWP\WebPConverterLite\Assets;

class AdminAssets {

    public static function enqueue(): void
    {
        self::enqueue_styles();
        self::enqueue_scripts();
    }

    private static function enqueue_styles(): void
    {
        wp_register_style('rwwcl-admin-style-min', RWWCL_PLUGIN_URL. 'css/rwwcl-admin-style.min.css', [], RWWCL_PLUGIN_VERSION );
        wp_enqueue_style('rwwcl-admin-style-min');
    }

    private static function enqueue_scripts(): void
    {
        wp_enqueue_script('rwwcl-admin-scan-min', RWWCL_PLUGIN_URL . 'assets/js/rwwcl-admin-scan.min.js', ['jquery'], RWWCL_PLUGIN_VERSION , true);
        wp_localize_script('rwwcl-admin-scan-min', 'rwwcl_object', [
            'nonce'    => wp_create_nonce('rwwcl_nonce'),
        ]);
    }

}
