<?php
if (!defined('ABSPATH')) exit;
?>

<div class="rwwcl-tab-panel rwwcl-bulk">

    <h2><?php echo esc_html__( 'Bulk Convert', 'rw-webp-converter-lite' ); ?></h2>

    <p><?php echo esc_html__( 'Convert your existing JPEG and PNG images to WebP in bulk.', 'rw-webp-converter-lite' ); ?></p>

    <button id="rwwcl-start-bulk" class="button button-primary">
        <?php echo esc_html__( 'Start Bulk Conversion', 'rw-webp-converter-lite' ); ?>
    </button>

    <!-- Progress Area -->
    <div id="rwwcl-bulk-progress" class="rwwcl-bulk-progress" style="display:none; margin-top:20px;">

        <p class="rwwcl-progress-heading">
            <strong><?php echo esc_html__( 'Processing… Please wait.', 'rw-webp-converter-lite' ); ?></strong>
        </p>

        <div class="rwwcl-progress-wrapper" style="background:#eee; height:20px; border-radius:3px; overflow:hidden;">
            <div class="rwwcl-progress-bar-inner" style="background:#0073aa; width:0%; height:100%;"></div>
        </div>

        <p class="rwwcl-progress-text" style="margin-top:8px;">0%</p>
    </div>

</div>
