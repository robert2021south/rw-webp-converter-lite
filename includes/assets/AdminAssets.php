<?php
namespace RobertWP\WebPConverterLite\Assets;

class AdminAssets {

    public static function enqueue($hook): void
    {
        self::enqueue_styles($hook);
        self::enqueue_scripts($hook);
    }

    private static function enqueue_styles($hook): void
    {
        wp_register_style('rwwcl-admin-style-min', RWWCL_ASSETS_URL. 'css/rwwcl-admin-style.min.css', [], RWWCL_PLUGIN_VERSION );
        wp_enqueue_style('rwwcl-admin-style-min');

        if ($hook !== 'plugins.php') return;

        wp_enqueue_style('rwwcl-admin-deactivate-modal-min', RWWCL_ASSETS_URL . 'css/rwwcl-admin-deactivate-modal.min.css', [], RWWCL_PLUGIN_VERSION);
        wp_enqueue_style('rwwcl-admin-deactivate-modal-min');
    }

    private static function enqueue_scripts($hook): void
    {
        wp_enqueue_script('rwwcl-admin-bulk-min', RWWCL_ASSETS_URL . 'js/rwwcl-admin-bulk.min.js', ['jquery'], RWWCL_PLUGIN_VERSION , true);
        wp_localize_script('rwwcl-admin-bulk-min', 'rwwcl_object', [
            'nonce'    => wp_create_nonce('rwwcl_bulk_nonce'),
        ]);

        if ($hook !== 'plugins.php') return;

        wp_enqueue_script('rwwcl-admin-deactivate-min', RWWCL_ASSETS_URL . 'js/rwwcl-admin-deactivate.min.js', ['jquery'], RWWCL_PLUGIN_VERSION, true);
        wp_localize_script('rwwcl-admin-deactivate-min', 'rwwclDeactivate', [
            'nonce'      => wp_create_nonce('rwwcl_deactivate_feedback_nonce'),
            'slug'       => 'rw-webp-converter-lite',
        ]);

    }

}
