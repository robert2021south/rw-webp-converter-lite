<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Plugin core option
$rwwcl_settings_option = 'rwwcl_settings';
$rwwcl_version_option  = 'rwwcl_version';

// Read settings
$rwwcl_settings = get_option( $rwwcl_settings_option, [] );

// 1️⃣ Delete plugin options
delete_option( $rwwcl_settings_option );
delete_option( $rwwcl_version_option );

// 2️⃣ Delete plugin transients
$rwwcl_transients = [
    'rwwcl_last_converted',
    'rwwcl_bulk_progress',
    'rwwcl_total_images',
];

foreach ( $rwwcl_transients as $transient ) {
    delete_transient( $transient );
}

// 3️⃣ Delete postmeta
$rwwcl_delete_data = ! empty( $rwwcl_settings['delete_data_on_uninstall'] );
if ( $rwwcl_delete_data ) {
    global $wpdb;
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s",
            $wpdb->esc_like( '_rwwcl_' ) . '%'
        )
    );
}
