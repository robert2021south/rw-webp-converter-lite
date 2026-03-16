<?php
if (!defined('ABSPATH')) exit;

use RobertWP\WebPConverterLite\Admin\Services\Statistics;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Admin\Ui\SettingsRenderer;
use RobertWP\WebPConverterLite\Utils\TemplateLoader;

/**
 * @var string $active_tab
 * @var array  $tabs
 * @var array  $tab_urls
 */
?>
<div class="wrap rwwcl-admin-wrap">

    <h1><?php echo esc_html__( 'RW WebP Converter Lite', 'rw-webp-converter-lite' ); ?></h1>
    <?php settings_errors();?>
    <h2 class="nav-tab-wrapper rwwcl-tab-wrapper">

        <?php foreach ($tabs as $rwwcl_tab_key => $rwwcl_tab_label):
            $rwwcl_is_active = $active_tab === $rwwcl_tab_key ? ' nav-tab-active' : '';
            ?>
            <a href="<?php echo esc_url($tab_urls[$rwwcl_tab_key]); ?>"
               class="nav-tab<?php echo esc_attr($rwwcl_is_active); ?>">
                <?php echo esc_html($rwwcl_tab_label); ?>
            </a>
        <?php endforeach; ?>

    </h2>

    <div class="rwwcl-tab-content">
        <?php

        //
        $rwwcl_stats = Statistics::get_instance()->get_global_stats();
        $rwwcl_stats['remaining_images'] = max(0, $rwwcl_stats['total_images'] - $rwwcl_stats['converted_images']);

        //
        $rwwcl_recent_records = get_transient('rwwcl_last_converted') ?: [];

        switch ($active_tab) {
            case 'overview':
                $rwwcl_tab_data['stats'] = $rwwcl_stats;
                $rwwcl_tab_data['recent_records'] = $rwwcl_recent_records;
                break;
            case 'settings':
                // Prepare form-related parameters
                $rwwcl_form_args = [
                    'settings_group' => 'rwwcl_settings_group',
                    'option_name'    => SettingsRegistrar::RWWCL_SETTINGS_OPTION,
                    'submit_label'   => __('Save Changes', 'rw-webp-converter-lite'),
                ];
                $rwwcl_pro_fields    = SettingsRenderer::get_pro_fields();

                //
                $rwwcl_tab_data['form_args'] = $rwwcl_form_args;
                $rwwcl_tab_data['pro_fields'] = $rwwcl_pro_fields;
                break;
            default:
                $rwwcl_tab_data = [];
                break;
        }
        // load template
        TemplateLoader::load("main/tab-{$active_tab}", $rwwcl_tab_data);
        ?>
    </div>
</div>
