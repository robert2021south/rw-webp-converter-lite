<?php
namespace RobertWP\WebPConverterLite\Admin\UI;

use RobertWP\WebPConverterLite\Utils\TemplateLoader;

class AdminPageRenderer
{

    /**
     * Render the main plugin page with tabs.
     */
    public static function render_main_page(): void
    {
        // 当前选中的 tab
        $active_tab = $_GET['tab'] ?? 'overview';

        // 所有 tab 键和标签
        $tabs = [
            'overview' => __( 'Overview', 'rw-webp-converter-lite' ),
            'settings' => __( 'Settings', 'rw-webp-converter-lite' ),
            'about'    => __( 'About', 'rw-webp-converter-lite' ),
        ];

        // 为每个 tab 生成完整 URL
        $tab_urls = [];
        foreach ($tabs as $key => $label) {
            $tab_urls[$key] = add_query_arg(
                [
                    'page' => 'rwwcl-main',
                    'tab'  => $key,
                ],
                admin_url('tools.php')
            );
        }

        // 确保 active_tab 在允许的 tab 里
        if (!isset($tabs[$active_tab])) {
            $active_tab = 'overview';
        }

        // 可选：额外数据传给模板
        $view_data = [
            'active_tab' => $active_tab,
            'tabs'       => $tabs,
            'tab_urls'   => $tab_urls,
        ];

        TemplateLoader::load('main/main-page', $view_data);
    }

}
