<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

function rwwcl_delete_all_options_metas(): void
{
    //option to be delete
    $rwwcl_version_option = 'rwwcl_version';
    $rwwcl_site_settings_option = 'rwwcl_site_settings';

    // 1. 如果设置了删除插件时同时删除数据，就删除 postmeta中的数据
    $all_settings = get_option($rwwcl_site_settings_option, []);
    $delete_data = !empty($all_settings['delete_data_on_uninstall']);

    if ($delete_data) {
        global $wpdb;
        $query = $wpdb->prepare(
            "DELETE FROM $wpdb->postmeta WHERE meta_key LIKE %s",
            '_rwwcl_%'
        );
        $wpdb->query($query);
    }

    // 2. 删除选项
    $option_names = array(
        $rwwcl_version_option,
        $rwwcl_site_settings_option
    );

    foreach ($option_names as $option_name) {
        delete_option(sanitize_key($option_name));
    }

}

rwwcl_delete_all_options_metas();
