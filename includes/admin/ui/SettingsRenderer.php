<?php
namespace RobertWP\WebPConverterLite\Admin\Ui;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Utils\Helper;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class SettingsRenderer {

    public static function render_general_section_description(): void
    {
        echo '<p class="description">'
            . esc_html__(
                'Configure the core behavior of the plugin. These settings control automatic optimization, WebP quality, and how images are handled.',
                'rw-webp-converter-lite'
            )
            . '</p>';
    }

    public static function render_auto_optimize_field(): void
    {
        $settings = Helper::get_settings();

        TemplateLoader::load('settings/fields/checkbox', [
            'name'        => SettingsRegistrar::RWWCL_SETTINGS_OPTION.'[auto_optimize]',
            'value'       => $settings['auto_optimize'] ?? 0,
            'label'       => __('Automatically optimize images on upload (Lite)', 'rw-webp-converter-lite'),
        ]);
    }

    public static function render_webp_quality_field(): void
    {
        $settings = Helper::get_settings();

        TemplateLoader::load('settings/fields/select', [
            'name'        => SettingsRegistrar::RWWCL_SETTINGS_OPTION.'[webp_quality]',
            'value'       => $settings['webp_quality'] ?? 80,
            'options'     => [60, 70, 80, 90],
            'description'=> __('Set the quality of generated WebP images (higher = better quality, larger size).', 'rw-webp-converter-lite'),
        ]);
    }

    public static function render_keep_original_field(): void
    {
        $settings = Helper::get_settings();

        TemplateLoader::load('settings/fields/checkbox', [
            'name'  => SettingsRegistrar::RWWCL_SETTINGS_OPTION.'[keep_original]',
            'value' => $settings['keep_original'] ?? 1,
            'label' => __('Keep the original images after WebP conversion.', 'rw-webp-converter-lite'),
        ]);
    }

    public static function render_overwrite_webp_field(): void
    {
        $settings = Helper::get_settings();

        TemplateLoader::load('settings/fields/checkbox', [
            'name'  => SettingsRegistrar::RWWCL_SETTINGS_OPTION.'[overwrite_webp]',
            'value' => $settings['overwrite_webp'] ?? 0,
            'label' => __('Overwrite existing WebP files if they exist.', 'rw-webp-converter-lite'),
        ]);
    }

    public static function render_skip_small_field(): void
    {
        $settings = Helper::get_settings();

        TemplateLoader::load('settings/fields/number', [
            'name'        => SettingsRegistrar::RWWCL_SETTINGS_OPTION.'[skip_small]',
            'value'       => $settings['skip_small'] ?? 0,
            'min'         => 0,
            'max'         => 10000,
            'description'=> __('Skip images smaller than this pixel size (longest edge). Set 0 to convert all images. Recommended range: 0–10000.', 'rw-webp-converter-lite'),
        ]);
    }

    /**
     * Data & Cleanup section description
     */
    public static function render_data_section_description(): void
    {
        echo '<p class="description">'
            . esc_html__(
                'These settings control how plugin data is handled when uninstalling. Use with caution.',
                'rw-webp-converter-lite'
            )
            . '</p>';
    }

    public static function render_delete_data_on_uninstall_field(): void
    {
        $settings = Helper::get_settings(); // 获取现有设置
        $value = $settings['delete_data_on_uninstall'] ?? 0;

        TemplateLoader::load('settings/fields/checkbox', [
            'name'  => SettingsRegistrar::RWWCL_SETTINGS_OPTION . '[delete_data_on_uninstall]',
            'value' => $value,
            'label' => __('Delete all plugin data on uninstall', 'rw-webp-converter-lite'),
            'description' => __('If enabled, all settings, WebP conversion records, and transients will be removed when the plugin is uninstalled.', 'rw-webp-converter-lite'),
        ]);
    }

    // -----------------------------
    // Pro features unified rendering
    // -----------------------------

    public static function get_pro_fields(): array
    {
        return [
            [
                'label'       => 'Automatic URL Replacement',
                'type'        => 'checkbox',
                'description' => 'The Lite version does not replace existing image URLs or modify database records.',
            ],
            [
                'label'       => 'Front-end Image Delivery Optimization',
                'type'        => 'checkbox',
                'description' => 'Advanced front-end image handling is intentionally excluded to ensure predictable behavior.',
            ],
        ];
    }


}
