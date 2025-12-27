<?php
namespace RobertWP\WebPConverterLite\Admin\Settings;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Admin\Ui\SettingsRenderer;

class SettingsRegistrar {
    use Singleton;

    const RWWCL_SETTINGS_OPTION = 'rwwcl_settings';

    public function register_settings(): void {
        // Register Settings Group
        register_setting(
            'rwwcl_settings_group',
            self::RWWCL_SETTINGS_OPTION,
            [$this, 'sanitize']
        );

        /**
         * =============================
         * Register Settings
         * =============================
         */
        add_settings_section(
            'rwwcl_general_section',
            __('General Settings', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_general_section_description'],
            'rwwcl_settings'
        );

        // -----------------------------
        // Register Field
        // -----------------------------

        add_settings_field(
            'auto_optimize',
            __('Auto Optimize Uploads', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_auto_optimize_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        add_settings_field(
            'webp_quality',
            __('WebP Quality', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_webp_quality_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        add_settings_field(
            'keep_original',
            __('Keep Original Images', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_keep_original_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        add_settings_field(
            'overwrite_webp',
            __('Overwrite Existing WebP', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_overwrite_webp_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        add_settings_field(
            'skip_small',
            __('Skip Small Images', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_skip_small_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        /**
         * =============================
         * Data & Cleanup
         * =============================
         */
        add_settings_section(
            'rwwcl_data_section',
            __('Data & Cleanup', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_data_section_description'],
            'rwwcl_settings'
        );

        add_settings_field(
            'delete_data_on_uninstall',
            __('Delete Data on Uninstall', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_delete_data_on_uninstall_field'],
            'rwwcl_settings',
            'rwwcl_data_section'
        );
    }

    public function sanitize($input): array
    {
        return [
            'auto_optimize'           => !empty($input['auto_optimize']) ? 1 : 0,
            'webp_quality'            => isset($input['webp_quality']) ? (int) $input['webp_quality'] : 0,
            'keep_original'           => !empty($input['keep_original']) ? 1 : 0,
            'overwrite_webp'          => !empty($input['overwrite_webp']) ? 1 : 0,
            'skip_small'              => isset($input['skip_small']) ? (int) $input['skip_small'] : 0,
            'delete_data_on_uninstall'=> !empty($input['delete_data_on_uninstall']) ? 1 : 0,
        ];
    }

}