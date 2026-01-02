<?php
if (!defined('ABSPATH')) exit;

use RobertWP\WebPConverterLite\Utils\Helper;

/**
 * @var array  $stats
 * @var array  $recent_records
 */


$rwwcl_button_text = __('Start Bulk Conversion', 'rw-webp-converter-lite');
$rwwcl_button_disabled = false;

if ($stats['total_images'] === 0) {
    $rwwcl_button_text = __('No Images Found', 'rw-webp-converter-lite');
    $rwwcl_button_disabled = true;

} elseif ($stats['remaining_images'] === 0) {
    $rwwcl_button_text = __('✓ All Images Converted', 'rw-webp-converter-lite');
    $rwwcl_button_disabled = true;

} elseif ($stats['converted_images'] > 0) {
    $rwwcl_button_text = __('Continue Bulk Conversion', 'rw-webp-converter-lite');
}

?>

<div class="rwwcl-tab-panel rwwcl-overview">

    <!-- 1. Dashboard / Quick Start -->
    <h2><?php echo esc_html__('Overview', 'rw-webp-converter-lite'); ?></h2>

    <p><?php echo esc_html__('Welcome to RW WebP Converter Lite, by RobertWP.', 'rw-webp-converter-lite'); ?></p>

    <div class="notice notice-info" style="margin: 15px 0; padding: 10px 15px; border-left: 4px solid #72aee6;">
        <p style="margin: 0;">
            <strong><?php echo esc_html__('Important:', 'rw-webp-converter-lite'); ?></strong>
            <?php echo esc_html__('This plugin only generates WebP files. It does NOT replace image URLs in the database, nor affect the display of existing images.', 'rw-webp-converter-lite'); ?>
        </p>
    </div>

    <div class="rwwcl-cards">
        <div class="rwwcl-card">
            <h3><?php echo esc_html__('Quick Start', 'rw-webp-converter-lite'); ?></h3>
            <p><?php echo esc_html__('Click the button below to bulk convert your JPEG/PNG images to WebP format.', 'rw-webp-converter-lite'); ?></p>
        </div>
    </div>

    <!-- 2. Statistics -->

    <div class="rwwcl-status-cards">
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Total Images', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html($stats['total_images']); ?></strong></p>
        </div>
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Converted Images', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html($stats['converted_images']); ?></strong></p>
        </div>
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Remaining Images', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html($stats['remaining_convertible']); ?></strong></p>
        </div>
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Skipped Images', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html($stats['skipped_small_images']); ?></strong></p>
        </div>
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Conversion Rate', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html($stats['conversion_rate']); ?>%</strong></p>
        </div>
        <div class="rwwcl-card">
            <h4><?php echo esc_html__('Total Space Saved', 'rw-webp-converter-lite'); ?></h4>
            <p><strong><?php echo esc_html(size_format($stats['space_saved'],2)); ?></strong></p>
        </div>
    </div>

    <!-- 3. Bulk Convert -->
    <div class="rwwcl-bulk-convert">
        <h3><?php echo esc_html__('Bulk Convert', 'rw-webp-converter-lite'); ?></h3>

        <button
                id="rwwcl-start-bulk"
                class="button button-primary"
            <?php disabled($rwwcl_button_disabled); ?>
        >
            <?php echo esc_html($rwwcl_button_text); ?>
        </button>

        <p class="rwwcl-auto-hint">
            <strong><?php echo esc_html__( 'Note:', 'rw-webp-converter-lite' ); ?></strong>
             <?php echo esc_html__(
                'New images will be automatically converted after upload. You can change this in Settings.',
                'rw-webp-converter-lite'
            ); ?>
        </p>

        <!-- Progress Area -->
        <div id="rwwcl-bulk-progress" class="rwwcl-bulk-progress" style="display:none; margin-top:20px;">
            <p class="rwwcl-progress-heading"><strong><?php echo esc_html__('Processing… Please wait.', 'rw-webp-converter-lite'); ?></strong></p>
            <div class="rwwcl-progress-wrapper" style="background:#eee; height:20px; border-radius:3px; overflow:hidden;">
                <div class="rwwcl-progress-bar-inner" style="background:#0073aa; width:0%; height:100%;"></div>
            </div>
            <p class="rwwcl-progress-text" style="margin-top:8px;">0%</p>
        </div>
    </div>


    <!-- 4. Recent Conversions -->
    <h3 style="margin-top:40px;"><?php echo esc_html__('Recent Conversions', 'rw-webp-converter-lite'); ?></h3>

    <?php if (empty($recent_records)): ?>
        <p><?php echo esc_html__('No recent conversion records found.', 'rw-webp-converter-lite'); ?></p>
    <?php else: ?>
        <table class="widefat striped rwwcl-status-table">
            <thead>
            <tr>
                <th><?php echo esc_html__('File', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Original Size', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('WebP Size', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Size Change', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Time', 'rw-webp-converter-lite'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($recent_records as $rec): ?>
                <tr>
                    <td>
                            <?php
                            $thumb_url = '';
                            if (!empty($rec['id']) && get_post_status($rec['id'])) {
                                $thumb = wp_get_attachment_image_src($rec['id'], 'thumbnail');
                                $thumb_url = $thumb ? $thumb[0] : '';
                            }
                            ?>
                            <a href="<?php echo esc_url($rec['original_url']); ?>" target="_blank" style="display:flex; align-items:center; text-decoration:none; color:inherit;">
                                <?php if ($thumb_url): ?>
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" style="width:40px;height:40px;margin-right:8px;object-fit:cover;border:1px solid #ccc;">
                                    <span><?php echo esc_html($rec['file']); ?></span>
                                <?php else: ?>
                                    <span style="display:inline-block;width:40px;height:40px;background:#eee;margin-right:8px;border:1px solid #ccc;"></span>
                                    <span style="text-decoration:line-through;color:#999;"><?php echo esc_html($rec['file']); ?></span>
                                <?php endif; ?>
                            </a>
                    </td>
                    <td ><?php echo esc_html(size_format($rec['original_size'],2)); ?></td>
                    <td>
                        <?php if (!empty($rec['webp_url'])): ?>
                            <a href="<?php echo esc_url($rec['webp_url']); ?>" target="_blank"><?php echo esc_html(size_format($rec['webp_size'],2)); ?></a>
                        <?php else: ?>
                            <?php echo esc_html(size_format($rec['webp_size'],2)); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html(Helper::image_compression_text($rec['original_size'],$rec['webp_size'])); ?></td>
                    <td><?php echo esc_html(human_time_diff($rec['time'], time()) . ' ago'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
