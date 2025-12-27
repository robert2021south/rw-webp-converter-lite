<?php
if (!defined('ABSPATH')) exit;
/** @var string $name */
/** @var string $options */
/** @var string $value */
/** @var string $description */
?>
<label>
<select name="<?php echo esc_attr($name); ?>">
    <?php foreach ($options as $rwwcl_opt): ?>
        <option value="<?php echo esc_attr($rwwcl_opt); ?>" <?php selected($value, $rwwcl_opt); ?>>
            <?php echo esc_html($rwwcl_opt); ?>
        </option>
    <?php endforeach; ?>
</select>
</label>
<?php if (!empty($description)) : ?>
    <p class="description"><?php echo esc_html($description); ?></p>
<?php endif; ?>
