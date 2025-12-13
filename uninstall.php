<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// 插件核心 option
$settings_option = 'rwwcl_settings';
$version_option  = 'rwwcl_version';

// 读取设置
$settings = get_option( $settings_option, [] );

// 是否允许卸载时清理数据（建议你未来加一个设置项）
$delete_data = ! empty( $settings['delete_data_on_uninstall'] );

// 1️⃣ 删除插件 options
delete_option( $settings_option );
delete_option( $version_option );

// 2️⃣ 删除插件 transients
$transients = [
    'rwwcl_last_converted',
    'rwwcl_bulk_progress',
    'rwwcl_total_images',
];

foreach ( $transients as $transient ) {
    delete_transient( $transient );
}

// 3️⃣ 删除 postmeta（仅在用户允许时）
if ( $delete_data ) {
    global $wpdb;

    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like( '_rwwcl_' ) . '%'
        )
    );
}
