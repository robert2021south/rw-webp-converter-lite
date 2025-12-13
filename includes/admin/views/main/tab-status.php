<?php
if (!defined('ABSPATH')) exit;

use RobertWP\WebPConverterLite\Admin\Services\Statistics;

/**
 * 读取最近转换记录（由 bulk 转换时写入）
 */
$records = get_transient('rwwcl_last_converted') ?: [];
foreach ($records as &$rec) {
        $rec['deleted'] = !get_post_status($rec['id']);
}
// 刷新 transient，保持最新状态
set_transient('rwwcl_last_converted', $records, DAY_IN_SECONDS);

/**
 * 全局统计（实时扫描）
 */
$stats = Statistics::get_instance()->get_global_stats();
$total_converted = $stats['converted_images'];
$total_saved     = $stats['space_saved'];
?>

<div class="rwwcl-tab-panel rwwcl-status">

    <h2><?php echo esc_html__('Conversion Status', 'rw-webp-converter-lite'); ?></h2>

    <div class="rwwcl-status-cards">

        <div class="rwwcl-card">
            <h3><?php echo esc_html__('Total Converted', 'rw-webp-converter-lite'); ?></h3>
            <p><strong><?php echo esc_html($total_converted); ?></strong></p>
        </div>

        <div class="rwwcl-card">
            <h3><?php echo esc_html__('Total Space Saved', 'rw-webp-converter-lite'); ?></h3>
            <p><strong><?php echo size_format($total_saved); ?></strong></p>
        </div>

    </div>

    <h3 style="margin-top:30px;"><?php echo esc_html__('Recent Conversions', 'rw-webp-converter-lite'); ?></h3>

    <?php if (empty($records)): ?>
        <p><?php echo esc_html__('No recent conversion records found.', 'rw-webp-converter-lite'); ?></p>
    <?php else: ?>
        <table class="widefat striped rwwcl-status-table">
            <thead>
            <tr>
                <th><?php echo esc_html__('File', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Original Size', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('WebP Size', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Saved', 'rw-webp-converter-lite'); ?></th>
                <th><?php echo esc_html__('Time', 'rw-webp-converter-lite'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($records as $rec): ?>
                <tr>
                    <td style="display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center;">
                            <?php
                            $thumb_url = '';
                            if (!empty($rec['id']) && get_post_status($rec['id'])) {
                                $thumb = wp_get_attachment_image_src($rec['id'], 'thumbnail');
                                $thumb_url = $thumb ? $thumb[0] : '';
                            }
                            ?>

                            <a href="<?php echo esc_url($rec['original_url']); ?>" target="_blank" style="display:flex; align-items:center; text-decoration:none; color:inherit;">
                                <?php if ($thumb_url): ?>
                                    <img src="<?php echo esc_url($thumb_url); ?>" alt="" style="width:40px;height:40px;margin-right:8px;object-fit:cover;border:1px solid #ccc;">
                                    <span><?php echo esc_html($rec['file']); ?></span>
                                <?php else: ?>
                                    <span style="display:inline-block;width:40px;height:40px;background:#eee;margin-right:8px;border:1px solid #ccc;"></span>
                                    <span style="text-decoration:line-through;color:#999;"><?php echo esc_html($rec['file']); ?></span>
                                <?php endif; ?>
                            </a>
                        </div>

                        <?php if (!empty($rec['webp_url'])): ?>
                            <div>
                                <?php if ($thumb_url): ?>
                                    <a href="<?php echo esc_url($rec['webp_url']); ?>" target="_blank" style="font-size:12px;color:#0073aa;">WebP</a>
                                <?php else: ?>
                                    <span style="text-decoration:line-through;color:#999;">WebP</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </td>


                    <td><?php echo esc_html(size_format($rec['original_size'])); ?></td>
                    <td>
                        <?php if (!empty($rec['webp_url'])): ?>
                            <a href="<?php echo esc_url($rec['webp_url']); ?>" target="_blank"><?php echo size_format($rec['webp_size']); ?></a>
                        <?php else: ?>
                            <?php echo esc_html(size_format($rec['webp_size'])); ?>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html(size_format($rec['saved'])); ?></td>
                    <td><?php echo esc_html(human_time_diff($rec['time'], time()) . ' ago'); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    <?php endif; ?>

</div>