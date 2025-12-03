<?php
namespace RobertWP\ImageOptimizerLite\Core;

use RobertWP\ImageOptimizerLite\Admin\Menu\AdminMenuManager;
use RobertWP\ImageOptimizerLite\Admin\Settings\SettingsHandler;
use RobertWP\ImageOptimizerLite\Admin\Settings\SettingsRegistrar;
use RobertWP\ImageOptimizerLite\Admin\UI\AdminNotice;
use RobertWP\ImageOptimizerLite\Admin\UI\PluginMetaLinks;
use RobertWP\ImageOptimizerLite\Assets\AdminAssets;
use RobertWP\ImageOptimizerLite\Assets\FrontendAssets;
use RobertWP\ImageOptimizerLite\Modules\Cleaner\Cleaner;
use RobertWP\ImageOptimizerLite\Modules\Export\PostViewsExporter;
use RobertWP\ImageOptimizerLite\Modules\PostColumn\PostViewsColumn;
use RobertWP\ImageOptimizerLite\Modules\RestApi\RestApi;
use RobertWP\ImageOptimizerLite\Modules\Shortcode\ShortcodeHandler;
use RobertWP\ImageOptimizerLite\Modules\Sort\Sort;
use RobertWP\ImageOptimizerLite\Modules\tracker\Tracker;

class HooksRegistrar {

    public static function register(): void
    {
        self::register_core_hooks();    // 核心功能，如版本检查、激活等
        self::register_admin_hooks();    // 管理后台钩子
        self::register_frontend_hooks();    // 前台钩子
        self::register_feature_hooks();    // 功能性模块（视具体项目结构）
    }

    private static function register_core_hooks(): void
    {
        add_action('admin_init', self::cb([VersionChecker::class, 'check']));
        add_action('admin_init', self::cb([AdminNotice::class,'maybe_add_notice']));
    }

    private static function register_admin_hooks(): void
    {
        if (!is_admin()) return;

        $menu_manager = AdminMenuManager::get_instance();
        $settings_registrar = SettingsRegistrar::get_instance();
        $settings_handler = new SettingsHandler();
        $columns = new PostViewsColumn();

        // admin_menu
        add_action('admin_menu', [$menu_manager, 'add_settings_menu']);
        add_action('admin_menu', [PostViewsExporter::class, 'add_export_submenu']);
        add_action('admin_menu', [Cleaner::class, 'add_cleaner_submenu']);

        // admin_posthandle_network_settings_form
        add_action('admin_post_rwiol_save_settings', self::cb([$settings_handler,'handle_settings_form']));
        add_action('admin_post_rwiol_cleaner', self::cb([Cleaner::class, 'handle_cleaner_request']));
        add_action('admin_post_rwiol_export_csv', self::cb([PostViewsExporter::class, 'handle_export_csv']));

        // admin_init
        add_action('admin_init', [$settings_registrar, 'register_settings']);
        //add_action('admin_init', self::cb([AdminNotice::class, 'maybe_show_general_notice']));

        // option update hook
        add_action('update_option_rwiol_settings', self::cb([$settings_handler, 'after_settings_saved']), 10, 2);

        // UI columns
        add_filter('manage_posts_columns', self::cb([$columns, 'maybe_add_views_column']));
        add_action('manage_posts_custom_column', self::cb([$columns, 'maybe_display_views_column']), 10, 2);
        add_filter('manage_page_posts_columns', self::cb([$columns, 'maybe_add_views_column']));
        add_action('manage_page_posts_custom_column', self::cb([$columns, 'maybe_display_views_column']), 10, 2);

        // plugin meta
        add_action('plugin_action_links_' . plugin_basename(RWIOL_PLUGIN_FILE), [PluginMetaLinks::class, 'add_links']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);

    }

    private static function register_frontend_hooks(): void
    {
        $display = new ShortcodeHandler();
        add_shortcode('rwiol_post_views', self::cb([$display, 'display_post_views']));

        $tracker = new Tracker();
        add_action('wp_ajax_nopriv_rwiol_add_view', self::cb([$tracker, 'track_views_ajax']));
        add_action('wp_ajax_rwiol_add_view', self::cb([$tracker, 'track_views_ajax']));
        add_action('wp_enqueue_scripts', [FrontendAssets::class, 'enqueue']);
    }

    private static function register_feature_hooks(): void
    {
        RestApi::maybe_register_hooks();
        Sort::maybe_register_hooks();
    }

    private static function cb($callback): callable
    {
        return CallbackWrapper::plugin_context_only($callback);
    }

}
