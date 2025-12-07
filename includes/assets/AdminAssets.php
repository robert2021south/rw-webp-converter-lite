<?php
namespace RobertWP\ImageOptimizerLite\Assets;

class AdminAssets {

    public static function enqueue(): void
    {
        self::enqueue_scripts();
    }

    private static function enqueue_scripts(): void
    {
        wp_enqueue_script('rwiol-admin-scan-min', RWIOL_PLUGIN_URL . 'assets/js/rwiol-admin-scan.min.js', ['jquery'], RWIOL_PLUGIN_VERSION , true);
        wp_localize_script('rwiol-admin-scan-min', 'rwiol_object', [
            'nonce'    => wp_create_nonce('rwiol_nonce'),
        ]);


    }

}
