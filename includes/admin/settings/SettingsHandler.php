<?php
namespace RobertWP\WebPConverterLite\Admin\Settings;

class SettingsHandler {

    public function after_settings_saved( $old_value, $new_value ): void
    {
        do_action( 'rwwcl_settings_saved', $new_value, $old_value );
    }

}