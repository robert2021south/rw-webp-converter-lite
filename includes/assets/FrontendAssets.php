<?php
namespace RobertWP\ImageOptimizerLite\Assets;

class FrontendAssets {

    public static function enqueue(): void
    {
        self::enqueue_styles();
        self::enqueue_scripts();
    }

    public static function enqueue_styles(): void
    {
        wp_register_style('rwiol-wp-style-min', RWIOL_ASSETS_URL. 'css/rwiol-wp-style.min.css', [], RWIOL_PLUGIN_VERSION );
        wp_enqueue_style('rwiol-wp-style-min');
    }

    private static function enqueue_scripts(): void
    {

        $supported_types = apply_filters('rwiol_supported_post_types', ['post','page']);
        if (!is_singular($supported_types)) return;

        $post_type = get_post_type();

        wp_enqueue_script('rwiol-tracker-min', RWIOL_PLUGIN_URL . 'assets/js/rwiol-tracker.min.js', ['jquery'], RWIOL_PLUGIN_VERSION , true);
        wp_localize_script('rwiol-tracker-min', 'rwiol_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'post_id' => get_the_ID(),
            'post_type'    => $post_type,
            'nonce_action' => "rwiol_add_view_nonce_$post_type",
            'nonce'    => wp_create_nonce('rwiol_add_view_nonce_'.$post_type),
        ]);
    }

}
