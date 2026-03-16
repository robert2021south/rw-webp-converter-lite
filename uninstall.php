<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Plugin core option
$rwwcl_settings_option = 'rwwcl_settings';
$rwwcl_version_option  = 'rwwcl_version';
$rwwcl_site_uuid  = 'rwwcl_site_uuid';

// Read settings
$rwwcl_settings = get_option( $rwwcl_settings_option, [] );

// 1️⃣ Delete plugin options
delete_option( $rwwcl_settings_option );
delete_option( $rwwcl_version_option );
delete_option( $rwwcl_site_uuid );

// 2️⃣ Delete plugin transients
$rwwcl_transients = [
    'rwwcl_last_converted',
    'rwwcl_bulk_progress',
    'rwwcl_total_images',
];

foreach ( $rwwcl_transients as $transient ) {
    delete_transient( $transient );
}

global $wpdb;

$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->options} 
         WHERE option_name LIKE %s 
            OR option_name LIKE %s",
        $wpdb->esc_like('_transient_rwwcl_api_token_') . '%',
        $wpdb->esc_like('_transient_timeout_rwwcl_api_token_') . '%'
    )
);

// 3️⃣ Delete postmeta
$rwwcl_delete_data = ! empty( $rwwcl_settings['delete_data_on_uninstall'] );
if ( $rwwcl_delete_data ) {
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like( '_rwwcl_' ) . '%'
        )
    );
}
