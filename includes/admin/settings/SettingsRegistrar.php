<?php
namespace RobertWP\ImageOptimizerLite\Admin\Settings;

use RobertWP\ImageOptimizerLite\Traits\Singleton;
use RobertWP\ImageOptimizerLite\Admin\Ui\SettingsRenderer;

class SettingsRegistrar {
    use Singleton;

    const  RWIOL_SETTINGS_OPTION = 'rwiol_settings';

    public function register_settings(): void
    {
        // 注册设置组
        register_setting(
            'rwiol_settings_group',
            self::RWIOL_SETTINGS_OPTION,
            [$this, 'sanitize']    // 数据验证回调
        );

        // 注册设置 section
        add_settings_section(
            'rwiol_general_section',
            __('General Settings', 'rw-image-optimizer-lite'),
            '__return_false',
            'rwiol_settings'
        );

        // 自动压缩上传
        add_settings_field(
            'auto_optimize',
            __('Auto Optimize Uploads', 'rw-image-optimizer-lite'),
            [SettingsRenderer::class, 'render_auto_optimize_field'],
            'rwiol_settings',
            'rwiol_general_section'
        );

        // 压缩等级字段
        add_settings_field(
            'quality',
            __('Compression Level', 'rw-image-optimizer-lite'),
            [SettingsRenderer::class, 'render_quality_field'],
            'rwiol_settings',
            'rwiol_general_section'
        );

        // WebP 转换
        add_settings_field(
            'webp',
            __('Generate WebP', 'rw-image-optimizer-lite'),
            [SettingsRenderer::class, 'render_webp_field'],
            'rwiol_settings',
            'rwiol_general_section'
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