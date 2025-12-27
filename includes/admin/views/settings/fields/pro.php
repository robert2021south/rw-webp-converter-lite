<?php
if (!defined('ABSPATH')) exit;
/** @var array $field */
?>
<div class="rwwcl-pro-feature-card">
    <div class="rwwcl-pro-feature-header">
        <h4><?php echo esc_html($field['label']); ?></h4>
    </div>
    <div class="rwwcl-pro-feature-preview">
        <?php if ($field['type'] === 'checkbox'): ?>
            <div class="rwwcl-pro-checkbox-preview">
                <input type="checkbox" disabled>
                <span><?php echo esc_html__('Enabled in PRO', 'rw-webp-converter-lite'); ?></span>
            </div>
        <?php elseif ($field['type'] === 'select'): ?>
            <select disabled>
                <option><?php echo esc_html__('Multiple options available', 'rw-webp-converter-lite'); ?></option>
            </select>
        <?php elseif ($field['type'] === 'number'): ?>
            <div class="rwwcl-pro-number-preview">
                <input type="number" disabled value="50">
            </div>
        <?php else: ?>
            <input type="text" disabled value="<?php echo esc_attr__('Available in PRO', 'rw-webp-converter-lite'); ?>">
        <?php endif; ?>
    </div>

    <?php if (!empty($field['description'])) : ?>
        <div class="rwwcl-pro-feature-desc">
            <?php echo esc_html($field['description']); ?>
        </div>
    <?php endif; ?>
</div>