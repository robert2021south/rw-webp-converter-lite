<?php
namespace RobertWP\WebPConverterLite\Admin\Settings;

use RobertWP\WebPConverterLite\Traits\Singleton;
use RobertWP\WebPConverterLite\Admin\Ui\SettingsRenderer;

class SettingsRegistrar {
    use Singleton;

    const  RWWCL_SETTINGS_OPTION = 'rwwcl_settings';

    public function register_settings(): void
    {
        // 注册设置组
        register_setting(
            'rwwcl_settings_group',
            self::RWWCL_SETTINGS_OPTION,
            [$this, 'sanitize']    // 数据验证回调
        );

        // 注册设置 section
        add_settings_section(
            'rwwcl_general_section',
            __('General Settings', 'rw-webp-converter-lite'),
            '__return_false',
            'rwwcl_settings'
        );

        // 自动压缩上传
        add_settings_field(
            'auto_optimize',
            __('Auto Optimize Uploads', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_auto_optimize_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        // 压缩等级字段
        add_settings_field(
            'quality',
            __('Compression Level', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_quality_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );

        // WebP 转换
        add_settings_field(
            'webp',
            __('Generate WebP', 'rw-webp-converter-lite'),
            [SettingsRenderer::class, 'render_webp_field'],
            'rwwcl_settings',
            'rwwcl_general_section'
        );
    }

    // 数据验证回调
    public function sanitize($input): array
    {
        $output = [];
        $output['auto_optimize'] = !empty($input['auto_optimize']) ? 1 : 0;
        $output['quality'] = in_array($input['quality'], ['low','medium','high']) ? $input['quality'] : 'medium';
        $output['webp'] = !empty($input['webp']) ? 1 : 0;
        return $output;
    }

}