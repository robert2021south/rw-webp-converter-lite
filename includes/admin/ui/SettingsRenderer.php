<?php
namespace RobertWP\ImageOptimizerLite\Admin\UI;

use RobertWP\ImageOptimizerLite\Utils\Helper;
use RobertWP\ImageOptimizerLite\Utils\TemplateLoader;

class SettingsRenderer {

    public static function render_settings_page(): void
    {
        $upgrade_url = Helper::get_upgrade_url('setting-page');
        TemplateLoader::load('settings/settings-page',[
            'upgrade_url'=>$upgrade_url
        ]);
    }

}