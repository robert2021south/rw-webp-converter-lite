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

    <div class="rwwcl-lite-settings-section">
        <h3>
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


        <div class="rwwcl-pro-settings-section">
            <h3>
                <?php echo esc_html__('Features Not Included in Lite', 'rw-webp-converter-lite'); ?>
            </h3>

            <div class="rwwcl-pro-features-grid">
                <?php
                if (!empty($pro_fields)){
                   foreach ($pro_fields as $field){
                     TemplateLoader::load('settings/fields/pro',['field'=>$field]);
                   }
                }
                ?>
            </div>

            <div class="rwwcl-pro-upgrade-cta">
                <h4>
                    <?php echo esc_html__('Learn more about RW WebP Converter Lite?', 'rw-webp-converter-lite'); ?>
                </h4>
                <p class="p1">
                    <?php echo esc_html__("See what this plugin does, what it doesn't do, and how future extensions may help advanced use cases.", 'rw-webp-converter-lite'); ?>
                </p>
                <a href="https://robertwp.com/rw-webp-converter-lite/"
                   target="_blank"
                   class="button button-primary button-hero"
                   style="background:#ffb900; border-color:#ffb900; color:#000; font-weight:bold; padding:12px 24px;">
                    <?php echo esc_html__('View Plugin Details', 'rw-webp-converter-lite'); ?>
                </a>
            </div>
        </div>


</div>