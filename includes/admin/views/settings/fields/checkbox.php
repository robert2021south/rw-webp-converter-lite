<?php
if (!defined('ABSPATH')) exit;
/** @var string $name  */
/** @var string $value  */
/** @var string $label */
/** @var string $description */
?>
<label>
    <input type="hidden" id="<?php echo esc_attr($name); ?>" name="<?php echo esc_attr($name); ?>" value="0" />
    <input type="checkbox"
           name="<?php echo esc_attr($name); ?>"
           value="1"
        <?php checked($value, 1); ?>
    >
    <?php echo esc_html($label); ?>
</label>

<?php if (!empty($description)) : ?>
    <p class="description"><?php echo esc_html($description); ?></p>
<?php endif; ?>