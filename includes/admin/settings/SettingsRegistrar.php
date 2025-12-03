<?php
namespace RobertWP\ImageOptimizerLite\Admin\Settings;

use RobertWP\ImageOptimizerLite\Traits\Singleton;

class SettingsRegistrar {
    use Singleton;

    const OPTION_SITE_SETTINGS = 'rwiol_site_settings';

    public function register_settings(): void
    {
        self::register_settings_fields('rwiol-settings');
    }

    public static function register_settings_fields($page_slug): void
    {
        // === 第一组设置：功能设置 ===
        add_settings_section(
            'rwiol_feature_section',
            __('Feature Settings', 'rw-image-optimizer-lite'),
            null,
            $page_slug
        );

        $fields = [
            [
                'id' => 'rwiol_stat_field',
                'option' => 'stat_enabled',
                'label' => __('Enable page view statistics', 'rw-image-optimizer-lite'),
                'desc' => __('When enabled, the page views of each article will be automatically counted.', 'rw-image-optimizer-lite')
            ],
            [
                'id' => 'rwiol_sort_field',
                'option' => 'sort_enabled',
                'label' => __('Enable sorting', 'rw-image-optimizer-lite'),
                'desc' => __('When enabled, You can sort the articles on the article list page by clicking "Views".', 'rw-image-optimizer-lite')
            ],
            [
                'id' => 'rwiol_rest_api_field',
                'option' => 'rest_api_enabled',
                'label' => __('Enable REST API', 'rw-image-optimizer-lite'),
                'desc' => __('When enabled, you can retrieve the view count of a specific post via the REST API.', 'rw-image-optimizer-lite')
            ],
        ];

        foreach ($fields as $field) {
            SettingsRenderer::render_checkbox_setting_field($field, $page_slug, 'rwiol_feature_section' );
        }

        // === 第二组设置：数据设置 ===
        add_settings_section(
            'rwiop_data_section',
            __('Data Settings', 'rw-image-optimizer-lite'),
            null,
            $page_slug
        );

        $data_fields = [
            [
                'id' => 'rwiop_delete_data_field',
                'option' => 'delete_data_on_uninstall',
                'label' => __('Delete data on uninstall', 'rw-image-optimizer-lite'),
                'desc'  => __('When checked, all statistical data will be permanently deleted when the plugin is uninstalled.', 'rw-image-optimizer-lite')
            ]
        ];

        foreach ($data_fields as $field) {
            SettingsRenderer::render_checkbox_setting_field($field, $page_slug, 'rwiop_data_section');
        }
    }

    // 获取有效配置
    public static function get_effective_setting($key) {
        $site_settings = get_option(self::OPTION_SITE_SETTINGS, []);
        return $site_settings[$key] ?? '0';
    }

}