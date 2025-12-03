<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/** @var string $type  */
/** @var string $option  */
/** @var string $value  */
/** @var string $desc */
?>
<label>
    <input type="<?php echo esc_attr($type); ?>" id="<?php echo esc_attr($option); ?>" name="<?php echo esc_attr($option); ?>" value="<?php echo esc_attr($value)?>" class="regular-text"/>
</label>
<p class="description">
    <?php echo esc_html($desc); ?>
</p>


