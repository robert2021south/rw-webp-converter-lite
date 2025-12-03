<?php
namespace RobertWP\ImageOptimizerLite\Modules\RestApi;

use RobertWP\ImageOptimizerLite\Admin\Settings\SettingsRegistrar;
use RobertWP\ImageOptimizerLite\Modules\tracker\Tracker;
use RobertWP\ImageOptimizerLite\Traits\Singleton;
use WP_Error;

class RestApi {
    use Singleton;

    public static function maybe_register_hooks(): void
    {

        if (SettingsRegistrar::get_effective_setting('rest_api_enabled') !== 1) {
            return;
        }

        add_action('rest_api_init', [self::class, 'register_routes']);
    }

    public static function register_routes(): void
    {
        /**
         * REST API endpoint for post view counts.
         * Lite version returns only public data.
         * Endpoint is intentionally public for theme or plugin integration.
         */
        register_rest_route('rwiol/v1', '/views/(?P<id>\d+)', array(
            'methods'  => 'GET',
            'callback' => [self::class,'get_post_views'],
            'args'     => [
                'days' => [
                    'default' => 7,
                    'sanitize_callback' => 'absint',
                ]
            ],
            'permission_callback' => '__return_true' // intentionally public
        ));
    }

    public static function get_post_views($request) {
        $post_id = (int) $request['id'];
        $post    = get_post($post_id);

        if (!$post || 'publish' !== $post->post_status) {
            return new WP_Error('invalid_post', __('Invalid Post ID','rw-postviewstats-lite'), ['status' => 404]);
        }

        $basic_data = [
            'post_id' => $post_id,
            'views'   => Tracker::get_views($post_id),
        ];

        $response_data = $basic_data;

        return apply_filters('rwiol_rest_api_response', $response_data, $post);
    }


}
