<?php
namespace RobertWP\WebPConverterLite\Admin\Ui;

use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Utils\Helper;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class SettingsRenderer {

    // -----------------------------
    // Lite 核心设置
    // -----------------------------
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
            'description'=> __('Skip images smaller than this pixel size (longest edge). Set 0 to convert all images.', 'rw-webp-converter-lite'),
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
    // Pro 功能统一渲染
    // -----------------------------

    public static function get_pro_fields(): array
    {

        return  [
            [
                'label'       => 'Smart PNG/JPEG Optimization',
                'type'        => 'checkbox',
                'description' => 'Apply different compression strategies for JPEG and PNG images to maintain quality and transparency',
            ],
            [
                'label'       => 'WebP & AVIF Formats',
                'type'        => 'checkbox',
                'description' => 'Optimize images in modern formats for smaller size and wider browser compatibility',
            ],
            [
                'label'       => 'Retina & Responsive Optimization',
                'type'        => 'checkbox',
                'description' => 'Automatically generate multi-size images and integrate with srcset/picture for high-DPI and responsive devices',
            ],
            [
                'label'       => 'Preserve EXIF Metadata',
                'type'        => 'checkbox',
                'description' => 'Keep camera and copyright metadata for professional photography sites',
            ],
            [
                'label'       => 'Conversion History & Advanced Logs',
                'type'        => 'checkbox',
                'description' => 'Store full conversion records and provide advanced error reporting',
            ],
            [
                'label'       => 'CDN / External Storage Compatibility',
                'type'        => 'checkbox',
                'description' => 'Serve images via CDN or external storage for faster loading on high-traffic sites',
            ],
            [
                'label'       => 'White-label Mode',
                'type'        => 'checkbox',
                'description' => 'Remove plugin branding from admin pages and front-end output',
            ],
            [
                'label'       => 'Priority Support & Early Access',
                'type'        => 'checkbox',
                'description' => 'Get priority assistance and early access to new features',
            ],
        ];

    }

}
