<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/** @var string $option  */
/** @var string $value  */
/** @var string $description */
?>
<label>
    <input
            type="text"
            id="<?php echo esc_attr($option); ?>"
            name="<?php echo esc_attr($option); ?>"
            value="<?php echo esc_attr($value)?>"
            class="regular-text"
    />
</label>

<?php if (!empty($description)) : ?>
    <p class="description"><?php echo esc_html($description); ?></p>
<?php endif; ?>