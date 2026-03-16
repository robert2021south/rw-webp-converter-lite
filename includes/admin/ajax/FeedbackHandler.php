<?php
namespace RobertWP\WebPConverterLite\Admin\Ajax;

use Random\RandomException;
use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Utils\Helper;

class FeedbackHandler {
    use Singleton;

    private const API_BASE_URL = 'https://api.robertwp.com/api';
    private const PLUGIN_SLUG = 'rw-webp-converter-lite';

    /**
     * @throws RandomException
     */
    public function handle_feedback_submission(): void {

        // Security checks
        if (!check_ajax_referer('rwwcl_feedback_nonce', 'nonce', false)) {
            wp_send_json_error('Security check failed', 403);
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied', 403);
        }

        // Get and validate input
        $rating = isset($_POST['rating']) ? floatval($_POST['rating']) : 0;
        $feedback_type = isset($_POST['feedback_type']) ? sanitize_text_field(wp_unslash($_POST['feedback_type'])) : 'general';
        $feedback_message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
        $user_email = isset($_POST['user_email']) ? sanitize_email(wp_unslash($_POST['user_email'])) : '';

        // Validate: either rating or message is required
        if ($rating === 0 && empty($feedback_message)) {
            wp_send_json_error('Please provide either a rating or a message', 400);
        }

        // Get access token first
        $token = $this->get_access_token();
        if (!$token) {
            wp_send_json_error('Unable to obtain access token. Please try again later.', 500);
            return;
        }

        // Prepare data for API
        $payload = [
            'rating'         => $rating,
            'feedback_type'  => $feedback_type,
            'message'        => $feedback_message,
            'user_email'     => $user_email,
            'wp_version'     => $this->get_wp_version(),
            'php_version'    => PHP_VERSION,
            'locale'         => get_locale(),
            'meta'           => null,
        ];

        // Send asynchronously
        $this->async_feedback($payload, $token);

        // Respond immediately to the user
        wp_send_json_success('Thank you for your feedback!');
    }

    /* ================= Private Method ===========================*/

    /**
     * Send feedback asynchronously on shutdown
     *
     * @param array $payload
     * @param string $token
     */
    private function async_feedback(array $payload, string $token): void {
        add_action('shutdown', function() use ($payload, $token) {
            // Don't let the client abort the request
            ignore_user_abort(true);

            wp_remote_post(self::API_BASE_URL . '/feedback', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'body'    => wp_json_encode($payload),
                'timeout' => 5,
                'blocking' => false, // Non-blocking request
            ]);
        });
    }

    /**
     * Get WordPress version
     *
     * @return string
     */
    private function get_wp_version(): string {
        global $wp_version;
        return $wp_version ?? '';
    }

    /**
     * Get access token from Laravel API
     *
     * @return string|null
     * @throws RandomException
     */
    private static function get_access_token(): ?string
    {
        $siteUuid = Helper::get_site_uuid();
        $token_option = 'rwwcl_api_token_' . md5($siteUuid);

        // Check for cached token
        $cached_token = get_transient($token_option);
        if ($cached_token) {
            return $cached_token;
        }

        // Request new token
        $response = wp_remote_post(self::API_BASE_URL . '/auth/issue-feedback-token', [
            'timeout' => 15,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'site_uuid' => $siteUuid,
                'plugin_slug' => self::PLUGIN_SLUG,
                'plugin_version' => RWWCL_PLUGIN_VERSION,
            ]),
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (isset($body['status']) && $body['status'] === 'success' && isset($body['data']['token'])) {
            $token = $body['data']['token'];
            set_transient($token_option, $token, MINUTE_IN_SECONDS * 4);
            return $token;
        }

        return null;
    }
}