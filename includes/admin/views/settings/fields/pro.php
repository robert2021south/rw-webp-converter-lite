<?php
if (!defined('ABSPATH')) exit;
/** @var string $type */
/** @var string $value */
/** @var string $label */
/** @var string $description */
?>
<fieldset disabled style="opacity:0.5;">
    <label>
        <?php if ($type === 'checkbox'): ?>
            <input type="checkbox" <?php checked($value, 1); ?>>
        <?php elseif ($type === 'number'): ?>
            <input type="number" value="<?php echo esc_attr($value ?? 50); ?>" style="width:80px;">
        <?php elseif ($type === 'select'): ?>
            <select><option><?php echo esc_html($value ?? 'Default'); ?></option></select>
        <?php elseif ($type === 'textarea'): ?>
            <textarea rows="3" style="width:100%;"><?php echo esc_textarea($value ?? ''); ?></textarea>
        <?php endif; ?>

        <?php echo esc_html($label); ?>
    </label>

    <?php if (!empty($description)) : ?>
        <p class="description"><?php echo esc_html($description); ?></p>
    <?php endif; ?>
</fieldset>
