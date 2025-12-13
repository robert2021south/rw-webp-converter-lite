<?php
if (!defined('ABSPATH')) exit;

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

        <?php foreach ($tabs as $tab_key => $tab_label):
            $is_active = $active_tab === $tab_key ? ' nav-tab-active' : '';
            ?>
            <a href="<?php echo esc_url($tab_urls[$tab_key]); ?>"
               class="nav-tab<?php echo esc_attr($is_active); ?>">
                <?php echo esc_html($tab_label); ?>
            </a>
        <?php endforeach; ?>

    </h2>

    <div class="rwwcl-tab-content">
        <?php
        switch ($active_tab) {
            case 'settings':
                // 准备表单相关的参数
                $form_args = [
                    'settings_group' => 'rwwcl_settings_group',
                    'option_name'    => SettingsRegistrar::RWWCL_SETTINGS_OPTION,
                    'submit_label'   => __('Save Changes', 'rw-webp-converter-lite'),
                ];
                $pro_fields    = SettingsRenderer::get_pro_fields();

                //
                $tab_data['form_args'] = $form_args;
                $tab_data['pro_fields'] = $pro_fields;
                break;
            case 'status':
                $tab_data['conversion_status'] = [];//get_conversion_status();
                break;
            default:
                $tab_data = [];
                break;
        }
        // 载入对应 tab 内容
        TemplateLoader::load("main/tab-{$active_tab}", $tab_data);
        ?>
    </div>
</div>
