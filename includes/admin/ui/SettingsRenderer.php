<?php
namespace RobertWP\WebPConverterLite\Admin\Ui;

use RobertWP\WebPConverterLite\Admin\Pages\Statistics\Statistics;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Utils\Helper;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class SettingsRenderer {

    public static function render_settings_page(): void
    {
        $stats = Statistics::get_instance()->get_summary();
        $upgrade_url = Helper::get_upgrade_url('setting-page');
        TemplateLoader::load('settings/settings-page',[
            'stats'=>$stats,
            'upgrade_url'=>$upgrade_url
        ]);
    }

    // 字段渲染
    public static function render_quality_field(): void
    {
        $settings = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION, []);
        $value = $settings['quality'] ?? 'medium';
        ?>
        <fieldset>
            <label><input type="radio" name="rwwcl_settings[quality]" value="low" <?php checked($value,'low'); ?>> <?php _e('Low', 'rw-webp-converter-lite'); ?></label><br>
            <label><input type="radio" name="rwwcl_settings[quality]" value="medium" <?php checked($value,'medium'); ?>> <?php _e('Medium', 'rw-webp-converter-lite'); ?></label><br>
            <label><input type="radio" name="rwwcl_settings[quality]" value="high" <?php checked($value,'high'); ?>> <?php _e('High', 'rw-webp-converter-lite'); ?></label>
        </fieldset>
        <?php
    }

    public static function render_auto_optimize_field(): void
    {
        $settings = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION, []);
        $value = $settings['auto_optimize'] ?? 0;
        ?>
        <input type="checkbox" name="rwwcl_settings[auto_optimize]" value="1" <?php checked($value,1); ?>>
        <?php _e('Automatically optimize images on upload', 'rw-webp-converter-lite'); ?>
        <?php
    }

    public static function render_webp_field(): void
    {
        $settings = get_option(SettingsRegistrar::RWWCL_SETTINGS_OPTION, []);
        $value = $settings['webp'] ?? 0;
        ?>
        <input type="checkbox" name="rwwcl_settings[webp]" value="1" <?php checked($value,1); ?>>
        <?php _e('Generate WebP version of images', 'rw-webp-converter-lite'); ?>
        <?php
    }

}