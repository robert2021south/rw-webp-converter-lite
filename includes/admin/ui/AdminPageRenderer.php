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
        // Currently selected tab
        $active_tab = wp_unslash($_GET['tab'] ?? 'overview');

        // All tab keys and labels
        $tabs = [
            'overview' => __( 'Overview', 'rw-webp-converter-lite' ),
            'settings' => __( 'Settings', 'rw-webp-converter-lite' ),
            'about'    => __( 'About', 'rw-webp-converter-lite' ),
        ];

        // Generate full URL for each tab
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

        // Ensure active_tab is among allowed tabs
        if (!isset($tabs[$active_tab])) {
            $active_tab = 'overview';
        }

        // Pass additional data to the template
        $view_data = [
            'active_tab' => $active_tab,
            'tabs'       => $tabs,
            'tab_urls'   => $tab_urls,
        ];

        TemplateLoader::load('main/main-page', $view_data);
    }

}
