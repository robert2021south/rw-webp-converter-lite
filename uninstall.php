<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// 插件核心 option
$rwwcl_settings_option = 'rwwcl_settings';
$rwwcl_version_option  = 'rwwcl_version';

// 读取设置
$rwwcl_settings = get_option( $rwwcl_settings_option, [] );

// 是否允许卸载时清理数据（建议你未来加一个设置项）
$rwwcl_delete_data = ! empty( $rwwcl_settings['delete_data_on_uninstall'] );

// 1️⃣ 删除插件 options
delete_option( $rwwcl_settings_option );
delete_option( $rwwcl_version_option );

// 2️⃣ 删除插件 transients
$rwwcl_transients = [
    'rwwcl_last_converted',
    'rwwcl_bulk_progress',
    'rwwcl_total_images',
];

foreach ( $rwwcl_transients as $transient ) {
    delete_transient( $transient );
}

// 3️⃣ 删除 postmeta（仅在用户允许时）
if ( $rwwcl_delete_data ) {
    global $wpdb;

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like( '_rwwcl_' ) . '%'
        )
    );
}
