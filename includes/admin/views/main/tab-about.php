<?php
if (!defined('ABSPATH')) exit;
?>

<div class="rwwcl-tab-panel rwwcl-about">

    <div class="about-header">
        <h2><?php echo esc_html__('About', 'rw-webp-converter-lite'); ?></h2>
        <p class="version"><?php echo esc_html__('Version', 'rw-webp-converter-lite'); ?> <?php echo esc_html(RWWCL_PLUGIN_VERSION); ?></p>
    </div>

    <div class="about-intro">
        <p><?php echo esc_html__('RW WebP Converter Lite helps you convert images into modern WebP format to significantly improve your website loading speed and reduce bandwidth usage.', 'rw-webp-converter-lite'); ?></p>
    </div>

    <div class="about-features">
        <h3><?php echo esc_html__('Key Features', 'rw-webp-converter-lite'); ?></h3>

        <div class="features-grid">
            <div class="feature-item">
                <span class="dashicons dashicons-images-alt2"></span>
                <h4><?php echo esc_html__('Bulk Conversion', 'rw-webp-converter-lite'); ?></h4>
                <p><?php echo esc_html__('Convert multiple images at once with easy batch processing', 'rw-webp-converter-lite'); ?></p>
            </div>

            <div class="feature-item">
                <span class="dashicons dashicons-performance"></span>
                <h4><?php echo esc_html__('Performance Optimized', 'rw-webp-converter-lite'); ?></h4>
                <p><?php echo esc_html__('Automatically skip small images to save server resources', 'rw-webp-converter-lite'); ?></p>
            </div>

            <div class="feature-item">
                <span class="dashicons dashicons-admin-settings"></span>
                <h4><?php echo esc_html__('Flexible Settings', 'rw-webp-converter-lite'); ?></h4>
                <p><?php echo esc_html__('Control quality, overwrite behavior, and original file handling', 'rw-webp-converter-lite'); ?></p>
            </div>

            <div class="feature-item">
                <span class="dashicons dashicons-chart-area"></span>
                <h4><?php echo esc_html__('Detailed Statistics', 'rw-webp-converter-lite'); ?></h4>
                <p><?php echo esc_html__('Track conversion progress and space savings in real-time', 'rw-webp-converter-lite'); ?></p>
            </div>
        </div>
    </div>

    <div class="about-benefits">
        <h3><?php echo esc_html__('Why Use WebP?', 'rw-webp-converter-lite'); ?></h3>

        <div class="benefits-list">
            <div class="benefit">
                <span class="dashicons dashicons-chart-line"></span>
                <div>
                    <h4><?php echo esc_html__('Better Performance', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('WebP images are typically 25-35% smaller than JPEG/PNG with the same quality', 'rw-webp-converter-lite'); ?></p>
                </div>
            </div>

            <div class="benefit">
                <span class="dashicons dashicons-search"></span>
                <div>
                    <h4><?php echo esc_html__('Improved SEO', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('Faster loading images help with Core Web Vitals and search rankings', 'rw-webp-converter-lite'); ?></p>
                </div>
            </div>

            <div class="benefit">
                <span class="dashicons dashicons-money-alt"></span>
                <div>
                    <h4><?php echo esc_html__('Reduced Bandwidth', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('Smaller files mean lower hosting costs and faster page loads', 'rw-webp-converter-lite'); ?></p>
                </div>
            </div>

            <div class="benefit">
                <span class="dashicons dashicons-smartphone"></span>
                <div>
                    <h4><?php echo esc_html__('Modern Format', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('Supported by all modern browsers (Chrome, Firefox, Safari, Edge)', 'rw-webp-converter-lite'); ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="about-cta">
        <h3><?php echo esc_html__('Need Help?', 'rw-webp-converter-lite'); ?></h3>

        <div class="cta-content">
            <div class="cta-item">
                <span class="dashicons dashicons-book-alt"></span>
                <div>
                    <h4><?php echo esc_html__('Documentation', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('Check our documentation for detailed usage instructions', 'rw-webp-converter-lite'); ?></p>
                    <a href="http://docs.robertwp.com/rw-webp-converter-lite/en/#/" target="_blank" class="button button-secondary"><?php echo esc_html__('View Docs', 'rw-webp-converter-lite'); ?></a>
                </div>
            </div>

            <div class="cta-item">
                <span class="dashicons dashicons-email-alt"></span>
                <div>
                    <h4><?php echo esc_html__('Feedback Welcome', 'rw-webp-converter-lite'); ?></h4>
                    <p><?php echo esc_html__('This is a Lite version. Your feedback helps us improve!', 'rw-webp-converter-lite'); ?></p>
                    <a href="mailto:support@robertwp.com?subject=RW%20WebP%20Converter%20Feedback" target="_blank" class="button button-secondary">
                        <?php echo esc_html__('Send Feedback', 'rw-webp-converter-lite'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="about-pro">
        <div class="pro-banner">
            <h3>
                <span class="dashicons dashicons-star-filled"></span>
                <?php echo esc_html__('Advanced & Future Capabilities', 'rw-webp-converter-lite'); ?>
            </h3>

            <p>
                <?php echo esc_html__('This section outlines the design principles and current limitations of the Lite version, as well as the types of advanced scenarios it intentionally does not cover.', 'rw-webp-converter-lite'); ?>
            </p>

            <ul class="pro-features">
                <li>
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php echo esc_html__('Generates WebP files alongside original images', 'rw-webp-converter-lite'); ?>
                </li>

                <li>
                    <span class="dashicons dashicons-info-outline"></span>
                    <?php echo esc_html__('Designed for cautious and manual workflows', 'rw-webp-converter-lite'); ?>
                </li>

                <li>
                    <span class="dashicons dashicons-admin-links"></span>
                    <?php echo esc_html__('Does not replace existing image URLs', 'rw-webp-converter-lite'); ?>
                </li>

                <li>
                    <span class="dashicons dashicons-database"></span>
                    <?php echo esc_html__('No database modifications', 'rw-webp-converter-lite'); ?>
                </li>

            </ul>

            <div class="pro-cta">
                <p>
                    <strong>
                        <?php echo esc_html__('Built for developers, agencies, and high-traffic websites.', 'rw-webp-converter-lite'); ?>
                    </strong>
                </p>

                <a href="https://robertwp.com/rw-webp-converter-lite/"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="button button-primary">
                    <span class="dashicons dashicons-external"></span>
                    <?php echo esc_html__('Learn more on the plugin website', 'rw-webp-converter-lite'); ?>
                </a>

                <p class="pro-note">
                    <small>
                        <?php echo esc_html__('Opens in a new window', 'rw-webp-converter-lite'); ?>
                    </small>
                </p>
            </div>
        </div>
    </div>


    <div class="about-footer">
        <p class="copyright">
            &copy; <?php echo esc_html(gmdate('Y')); ?> RW WebP Converter.
            <?php echo esc_html__('Made with', 'rw-webp-converter-lite'); ?>
            <span class="dashicons dashicons-heart" style="color:#e74c3c;"></span>
            <?php echo esc_html__('for the WordPress community.', 'rw-webp-converter-lite'); ?>
        </p>

        <div class="footer-links">
            <a href="https://wordpress.org/plugins/rw-webp-converter-lite" target="_blank">
                <span class="dashicons dashicons-wordpress"></span>
                <?php echo esc_html__('WordPress.org', 'rw-webp-converter-lite'); ?>
            </a>
            <a href="https://github.com/robert2021south/rw-webp-converter-lite" target="_blank">
                <span class="dashicons dashicons-editor-code"></span>
                <?php echo esc_html__('GitHub', 'rw-webp-converter-lite'); ?>
            </a>
            <a href="https://robertwp.com/privacy-policy/" target="_blank">
                <span class="dashicons dashicons-shield"></span>
                <?php echo esc_html__('Privacy Policy', 'rw-webp-converter-lite'); ?>
            </a>
        </div>
    </div>

</div>
