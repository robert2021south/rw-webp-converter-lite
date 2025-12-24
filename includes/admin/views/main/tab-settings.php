<?php
if (!defined('ABSPATH')) exit;

use RobertWP\WebPConverterLite\Utils\TemplateLoader;

/**
 * @var array $form_args
 * @var array $pro_fields
 */
?>

<div class="rwwcl-tab-panel rwwcl-settings">

    <h2><?php echo esc_html__('Settings', 'rw-webp-converter-lite'); ?></h2>

    <!-- 1. Lite版本可用的设置 -->
    <div class="rwwcl-lite-settings-section">
        <h3 style="margin-top:0; color:#1d2327;">
            <?php echo esc_html__('Basic Settings', 'rw-webp-converter-lite'); ?>
        </h3>

        <form method="post" action="options.php">
            <?php
            settings_fields($form_args['settings_group']);
            do_settings_sections($form_args['option_name']);
            submit_button($form_args['submit_label']);
            ?>
        </form>
    </div>

    <!-- 2. PRO功能展示（表单外） -->
    <?php if (!empty($pro_fields)) : ?>
        <div class="rwwcl-pro-settings-section">
            <h3>
                <span class="pro-badge">PRO</span>
                <?php echo esc_html__('Advanced Features', 'rw-webp-converter-lite'); ?>
            </h3>

            <div class="rwwcl-pro-features-grid">
                <?php foreach ($pro_fields as $field){
                    TemplateLoader::load('settings/fields/pro',['field'=>$field]);
                }
                 ?>
            </div>

            <!-- 统一的升级提示 -->
            <div class="rwwcl-pro-upgrade-cta" style="text-align:center; margin:30px 0; padding:25px; background:linear-gradient(135deg, #f8f9fa 0%, #f0f6fc 100%); border-radius:8px; border:2px dashed #72aee6;">
                <h4 style="margin-top:0; color:#1d2327;">
                    <?php echo esc_html__('Ready for more?', 'rw-webp-converter-lite'); ?>
                </h4>
                <p style="color:#646970; max-width:600px; margin:10px auto 20px;">
                    <?php echo esc_html__('Upgrade to PRO for advanced optimization, CDN integration, bulk operations, and priority support.', 'rw-webp-converter-lite'); ?>
                </p>
                <a href="https://robertwp.com/rw-webp-converter-pro/"
                   target="_blank"
                   class="button button-primary button-hero"
                   style="background:#ffb900; border-color:#ffb900; color:#000; font-weight:bold; padding:12px 24px;">
                    <?php echo esc_html__('Upgrade to PRO', 'rw-webp-converter-lite'); ?>
                </a>
                <p style="margin:15px 0 0; font-size:12px; color:#8c8f94;">
                    <?php echo esc_html__('30-day money-back guarantee', 'rw-webp-converter-lite'); ?>
                </p>
            </div>
        </div>
    <?php endif; ?>

</div>