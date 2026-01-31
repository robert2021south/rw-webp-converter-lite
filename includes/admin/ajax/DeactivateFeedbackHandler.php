<?php
namespace RobertWP\WebPConverterLite\Admin\Ajax;

use RobertWP\WebPConverterLite\Traits\Singleton;

class DeactivateFeedbackHandler {
    use Singleton;

    private const API_BASE = 'https://api.robertwp.com/api';
    private const TOKEN_URL    = self::API_BASE . '/auth/issue-feedback-token';
    private const FEEDBACK_URL = self::API_BASE . '/feedback/deactivations';
    private const PLUGIN_SLUG = 'rw-webp-converter-lite';

    public function handle_request(): void {
        check_ajax_referer('rwwcl_deactivate_feedback_nonce', 'nonce');

        $reason   = sanitize_text_field(wp_unslash($_POST['reason'] ?? ''));
        $reason_detail = sanitize_text_field(wp_unslash($_POST['reason_detail'] ?? ''));

        //fire-and-forget
        $payload = [
            'plugin_slug'    => self::PLUGIN_SLUG,
            'plugin_version' => RWWCL_PLUGIN_VERSION,
            'reason_code'    => $reason,
            'reason_detail'  => $reason_detail,
            'wp_version'     => $this->get_wp_version(),
            'php_version'    => PHP_VERSION,
            'locale'         => get_locale(),
            'site_url'       => get_site_url(),
        ];

        $this->async_feedback($payload);

        // Response immediately
        wp_send_json_success();
    }

    private function async_feedback(array $payload): void {
        add_action('shutdown', function() use ($payload) {
            ignore_user_abort(true);

            $token_response = wp_remote_post(self::TOKEN_URL, [
                'headers' => ['Content-Type' => 'application/json'],
                'body'    => wp_json_encode(['site_url' => get_site_url()]),
                'timeout' => 5, // Change it to 5 seconds, which is more stable.
            ]);

            $body = json_decode(wp_remote_retrieve_body($token_response), true);
            $token = $body['data']['token'] ?? '';

            if ($token) {
                wp_remote_post(self::FEEDBACK_URL, [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'body'    => wp_json_encode($payload),
                    'timeout' => 5,
                ]);
            }
        });
    }


    private function get_wp_version(): string {
        global $wp_version;
        return $wp_version ?? '';
    }

}