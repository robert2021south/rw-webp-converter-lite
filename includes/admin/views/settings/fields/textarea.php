<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/** @var string $option  */
/** @var string $value  */
/** @var string $desc */
?>
<label>
    <textarea
            id="<?php echo esc_attr($option); ?>"
            name="<?php echo esc_attr($option); ?>"
            class="regular-textarea">
            <?php echo esc_attr($value)?>
    </textarea>
</label>

<?php if (!empty($description)) : ?>
    <p class="description"><?php echo esc_html($description); ?></p>
<?php endif; ?>