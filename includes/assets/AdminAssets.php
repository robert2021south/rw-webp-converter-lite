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
        // 始终加载批量处理的 JS（如果需要）
        wp_enqueue_script('rwwcl-admin-bulk-min', RWWCL_ASSETS_URL . 'js/rwwcl-admin-bulk.min.js', ['jquery'], RWWCL_PLUGIN_VERSION , true);
        wp_localize_script('rwwcl-admin-bulk-min', 'rwwcl_object', [
            'nonce'    => wp_create_nonce('rwwcl_bulk_nonce'),
        ]);

        // 检查是否是插件设置页面
        if ($hook === 'tools_page_rwwcl-main' || $hook === 'settings_page_rwwcl-main') {
            // 检查当前 tab 是否为 about
            $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'overview';

            if ($current_tab === 'about') {
                // 在 about 标签页加载反馈相关的 JS
                wp_enqueue_script(
                    'rwwcl-admin-feedback-min',
                    RWWCL_ASSETS_URL . 'js/rwwcl-admin-feedback.min.js',
                    ['jquery'],
                    RWWCL_PLUGIN_VERSION,
                    true
                );

                wp_localize_script('rwwcl-admin-feedback-min', 'rwwclFeedbackObject', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('rwwcl_feedback_nonce'),
                    'strings' => [
                        'selectType' => __('Please select feedback type', 'rw-webp-converter-lite'),
                        'rateOrWrite' => __('Please either rate or write something', 'rw-webp-converter-lite'),
                        'submitting' => __('Submitting...', 'rw-webp-converter-lite'),
                        'thankYou' => __('Thank you for your feedback!', 'rw-webp-converter-lite'),
                        'error' => __('An error occurred. Please try again.', 'rw-webp-converter-lite'),
                    ]
                ]);
            }
        }

        // 插件停用反馈的 JS（仅在 plugins.php 页面加载）
        if ($hook === 'plugins.php') {
            wp_enqueue_script('rwwcl-admin-deactivate-min', RWWCL_ASSETS_URL . 'js/rwwcl-admin-deactivate.min.js', ['jquery'], RWWCL_PLUGIN_VERSION, true);
            wp_localize_script('rwwcl-admin-deactivate-min', 'rwwclDeactivate', [
                'nonce'      => wp_create_nonce('rwwcl_deactivate_feedback_nonce'),
                'slug'       => 'rw-webp-converter-lite',
            ]);
        }
    }
}
