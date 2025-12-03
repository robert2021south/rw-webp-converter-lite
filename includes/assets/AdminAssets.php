<?php
namespace RobertWP\ImageOptimizerLite\Assets;

class AdminAssets {

    public static function enqueue() {
        self::enqueue_styles();
    }

    private static function enqueue_styles(): void
    {
        wp_register_style('rwiol-admin-style-min', RWIOL_ASSETS_URL. 'css/rwiol-admin-style.min.css', [], RWIOL_PLUGIN_VERSION );
        wp_enqueue_style('rwiol-admin-style-min');
    }

}
