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
        wp_register_style('rwwcl-admin-style-min', RWWCL_ASSETS_URL. 'css/rwwcl-admin-style.min.css', [], RWWCL_PLUGIN_VERSION );
        wp_enqueue_style('rwwcl-admin-style-min');
    }

    private static function enqueue_scripts(): void
    {
        wp_enqueue_script('rwwcl-admin-bulk-min', RWWCL_ASSETS_URL . 'js/rwwcl-admin-bulk.min.js', ['jquery'], RWWCL_PLUGIN_VERSION , true);
        wp_localize_script('rwwcl-admin-bulk-min', 'rwwcl_object', [
            'nonce'    => wp_create_nonce('rwwcl_bulk_nonce'),
        ]);
    }

}
