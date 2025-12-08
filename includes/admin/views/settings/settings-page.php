<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** @var array $stats  */
/** @var string $upgrade_url  */
?>

<div class="wrap rwwcl-wrap">

    <h1><?php _e('RW Image Optimizer Lite', 'rw-webp-converter-lite'); ?></h1>

    <p class="description">
        <?php _e('Optimize your Media Library images for better performance and faster loading speed.', 'rw-webp-converter-lite'); ?>
    </p>

    <hr class="wp-header-end">
    <?php settings_errors(); ?>

    <!-- ===========================
         SECTION 1: Compression Settings
    ============================ -->
    <h2 class="title"><?php _e('Compression Settings', 'rw-webp-converter-lite'); ?></h2>

    <form method="post" action="options.php">
        <?php
        wp_nonce_field('rwwcl_save_settings_nonce');
        settings_fields('rwwcl_settings_group');
        do_settings_sections('rwwcl_settings');
        submit_button(__('Save Changes', 'rw-webp-converter-lite'));
        ?>
    </form>

    <!-- ===========================
         SECTION 1.1: Pro Features (灰显)
    ============================ -->
    <h3 class="title"><?php _e('Pro Features (Preview)', 'rw-webp-converter-lite'); ?></h3>

    <fieldset disabled class="rwwcl-pro-feature">
        <label for="pro_webp_auto">
            <input type="checkbox" id="pro_webp_auto" name="pro_webp_auto">
            <?php _e('Auto-convert all uploads to WebP (Pro)', 'rw-webp-converter-lite'); ?>
        </label>
        <p class="description">
            <?php _e('This feature is available in the Pro version. Upgrade to unlock advanced WebP conversion.', 'rw-webp-converter-lite'); ?>
        </p>

        <label for="pro_bulk_scheduler">
            <input type="checkbox" id="pro_bulk_scheduler" name="pro_bulk_scheduler">
            <?php _e('Scheduled bulk optimization (Pro)', 'rw-webp-converter-lite'); ?>
        </label>
        <p class="description">
            <?php _e('Automatically optimize your Media Library on a schedule. Pro version only.', 'rw-webp-converter-lite'); ?>
        </p>
    </fieldset>

    <hr>

    <!-- ===========================
         SECTION 2: Bulk Optimization
    ============================ -->
    <h2 class="title"><?php _e('Bulk Optimization', 'rw-webp-converter-lite'); ?></h2>

    <p><?php _e('Scan your existing Media Library and optimize uncompressed images.', 'rw-webp-converter-lite'); ?></p>

    <button id="rwwcl-scan-btn" class="button button-secondary">
        <?php _e('Scan Unoptimized Images', 'rw-webp-converter-lite'); ?>
    </button>

    <button id="rwwcl-start-bulk-btn" class="button button-primary" disabled>
        <?php _e('Start Bulk Optimization', 'rw-webp-converter-lite'); ?>
    </button>

    <!-- Pro 专用按钮灰显 -->
    <button id="rwwcl-start-bulk-pro" class="button button-primary" disabled>
        <?php _e('Start Pro Bulk Optimization', 'rw-webp-converter-lite'); ?>
    </button>
    <p class="description">
        <?php _e('Scheduled and advanced bulk optimization available in Pro version.', 'rw-webp-converter-lite'); ?>
    </p>

    <div id="rwwcl-progress" style="margin-top: 15px; display: none;">
        <div class="rwwcl-progress-bar">
            <span class="rwwcl-progress-value" style="width:0%;"></span>
        </div>
        <p id="rwwcl-progress-text">0%</p>
    </div>

    <hr>

    <!-- ===========================
         SECTION 3: Optimization Statistics
    ============================ -->
    <h2 class="title"><?php _e('Optimization Statistics', 'rw-webp-converter-lite'); ?></h2>

    <table class="widefat striped">
        <tbody>
        <tr>
            <th><?php _e('Total Original Size', 'rw-webp-converter-lite'); ?></th>
            <td><?php echo esc_html($stats['total_before']); ?></td>
        </tr>
        <tr>
            <th><?php _e('Total Optimized Size', 'rw-webp-converter-lite'); ?></th>
            <td><?php echo esc_html($stats['total_after']); ?></td>
        </tr>
        <tr>
            <th><?php _e('Total Saved Space', 'rw-webp-converter-lite'); ?></th>
            <td><?php echo esc_html($stats['saved']); ?> (<?php echo esc_html($stats['percent']); ?>%)</td>
        </tr>
        <tr>
            <th><?php _e('Optimized Images Count', 'rw-webp-converter-lite'); ?></th>
            <td><?php echo esc_html($stats['optimized_count']); ?></td>
        </tr>
        </tbody>
    </table>

    <hr>

    <!-- ===========================
         SECTION 4: PRO Features Teaser
    ============================ -->
    <div class="rwwcl-pro-upgrade-box">
        <h2><?php _e('Upgrade to PRO', 'rw-webp-converter-lite'); ?></h2>
        <p><?php _e('Unlock advanced features such as AVIF, advanced WebP settings, unused images cleaner, optimization scheduler, and more.', 'rw-webp-converter-lite'); ?></p>
        <a href="<?php echo esc_url($upgrade_url);?>" target="_blank" class="button button-primary">
            <?php _e('Learn More', 'rw-webp-converter-lite'); ?>
        </a>
    </div>

</div>

<!-- ===========================
     CSS for Gray Display
=========================== -->
<style>
    .rwwcl-pro-feature {
        opacity: 0.5;
        pointer-events: none;
        margin-bottom: 20px;
    }
</style>
