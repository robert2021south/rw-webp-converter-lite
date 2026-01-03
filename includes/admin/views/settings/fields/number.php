<?php
if (!defined('ABSPATH')) exit;
/** @var string $name */
/** @var string $value */
/** @var string $min */
/** @var string $max */
/** @var string $step */
/** @var string $description */
?>
<label>
<input type="number"
       name="<?php echo esc_attr($name); ?>"
       value="<?php echo esc_attr($value); ?>"
       min="<?php echo esc_attr($min ?? 0); ?>"
        <?php if (isset($max)) : ?>
            max="<?php echo esc_attr($max); ?>"
        <?php endif; ?>
       step="<?php echo esc_attr($step ?? 1); ?>"
       style="width:80px;"
>
</label>
<?php if (!empty($description)) : ?>
    <p class="description"><?php echo esc_html($description); ?></p>
<?php endif; ?>
