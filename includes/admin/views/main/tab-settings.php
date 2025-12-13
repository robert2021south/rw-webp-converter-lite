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

    <form method="post" action="options.php">
        <?php
        settings_fields($form_args['settings_group']);
        do_settings_sections($form_args['option_name']);
        submit_button($form_args['submit_label']);
        ?>
    </form>

    <?php if (!empty($pro_fields)) : ?>
        <table class="form-table">
            <tbody>
            <?php foreach ($pro_fields as $field) : ?>
                <tr>
                    <th scope="row"><?php echo esc_html($field['label']); ?></th>
                    <td><?php TemplateLoader::load('settings/fields/pro', $field); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>
