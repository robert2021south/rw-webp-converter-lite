<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** @var array $stats  */
/** @var string $upgrade_url  */
?>

<div class="wrap rwiol-wrap">

    <h1><?php _e('RW Image Optimizer Lite', 'rw-image-optimizer-lite'); ?></h1>

    <p class="description">
        <?php _e('Optimize your Media Library images for better performance and faster loading speed.', 'rw-image-optimizer-lite'); ?>
    </p>

    <hr class="wp-header-end">
    <?php settings_errors(); ?>

    <!-- ===========================
         SECTION 1: Compression Settings
    ============================ -->
    <h2 class="title"><?php _e('Compression Settings', 'rw-image-optimizer-lite'); ?></h2>

    <form method="post" action="options.php">
        <?php
        wp_nonce_field('rwiol_save_settings_nonce');
        settings_fields('rwiol_settings_group');
        do_settings_sections('rwiol_settings');
        submit_button(__('Save Changes', 'rw-image-optimizer-lite'));
        ?>
    </form>

    <!-- ===========================
         SECTION 1.1: Pro Features (灰显)
    ============================ -->
    <h3 class="title"><?php _e('Pro Features (Preview)', 'rw-image-optimizer-lite'); ?></h3>

    <fieldset disabled class="rwiol-pro-feature">
        <label for="pro_webp_auto">
            <input type="checkbox" id="pro_webp_auto" name="pro_webp_auto">
            <?php _e('Auto-convert all uploads to WebP (Pro)', 'rw-image-optimizer-lite'); ?>
        </label>
        <p class="description">
            <?php _e('This feature is available in the Pro version. Upgrade to unlock advanced WebP conversion.', 'rw-image-optimizer-lite'); ?>
        </p>

        <label for="pro_bulk_scheduler">
            <input type="checkbox" id="pro_bulk_scheduler" name="pro_bulk_scheduler">
            <?php _e('Scheduled bulk optimization (Pro)', 'rw-image-optimizer-lite'); ?>
        </label>
        <p class="description">
            <?php _e('Automatically optimize your Media Library on a schedule. Pro version only.', 'rw-image-optimizer-lite'); ?>
        </p>
    </fieldset>

    <hr>

    <!-- ===========================
         SECTION 2: Bulk Optimization
    ============================ -->
    <h2 class="title"><?php _e('Bulk Optimization', 'rw-image-optimizer-lite'); ?></h2>

    <p><?php _e('Scan your existing Media Library and optimize uncompressed images.', 'rw-image-optimizer-lite'); ?></p>

    <button id="rwiol-scan-btn" class="button button-secondary">
        <?php _e('Scan Unoptimized Images', 'rw-image-optimizer-lite'); ?>
    </button>

    <button id="rwiol-start-bulk-btn" class="button button-primary" disabled>
        <?php _e('Start Bulk Optimization', 'rw-image-optimizer-lite'); ?>
    </button>

    <!-- Pro 专用按钮灰显 -->
    <button id="rwiol-start-bulk-pro" class="button button-primary" disabled>
        <?php _e('Start Pro Bulk Optimization', 'rw-image-optimizer-lite'); ?>
    </button>
    <p class="description">
        <?php _e('Scheduled and advanced bulk optimization available in Pro version.', 'rw-image-optimizer-lite'); ?>
    </p>

    <div id="rwiol-progress" style="margin-top: 15px; display: none;">
        <div class="rwiol-progress-bar">
            <span class="rwiol-progress-value" style="width:0%;"></span>
        </div>
        <p id="rwiol-progress-text">0%</p>
    </div>

    <hr>

    <!-- ===========================
         SECTION 3: Optimization Statistics
    ============================ -->
    <h2 class="title"><?php _e('Optimization Statistics', 'rw-image-optimizer-lite'); ?></h2>

    <table class="widefat striped">
        <tbody>
        <tr>
            <th><?php _e('Total Original Size', 'rw-image-optimizer-lite'); ?></th>
            <td><?php echo esc_html($stats['total_before']); ?></td>
        </tr>
        <tr>
            <th><?php _e('Total Optimized Size', 'rw-image-optimizer-lite'); ?></th>
            <td><?php echo esc_html($stats['total_after']); ?></td>
        </tr>
        <tr>
            <th><?php _e('Total Saved Space', 'rw-image-optimizer-lite'); ?></th>
            <td><?php echo esc_html($stats['saved']); ?> (<?php echo esc_html($stats['percent']); ?>%)</td>
        </tr>
        <tr>
            <th><?php _e('Optimized Images Count', 'rw-image-optimizer-lite'); ?></th>
            <td><?php echo esc_html($stats['optimized_count']); ?></td>
        </tr>
        </tbody>
    </table>

    <hr>

    <!-- ===========================
         SECTION 4: PRO Features Teaser
    ============================ -->
    <div class="rwiol-pro-upgrade-box">
        <h2><?php _e('Upgrade to PRO', 'rw-image-optimizer-lite'); ?></h2>
        <p><?php _e('Unlock advanced features such as AVIF, advanced WebP settings, unused images cleaner, optimization scheduler, and more.', 'rw-image-optimizer-lite'); ?></p>
        <a href="<?php echo esc_url($upgrade_url);?>" target="_blank" class="button button-primary">
            <?php _e('Learn More', 'rw-image-optimizer-lite'); ?>
        </a>
    </div>

</div>

<!-- ===========================
     CSS for Gray Display
=========================== -->
<style>
    .rwiol-pro-feature {
        opacity: 0.5;
        pointer-events: none;
        margin-bottom: 20px;
    }
</style>
