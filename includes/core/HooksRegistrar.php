<?php
namespace RobertWP\WebPConverterLite\Core;

use RobertWP\WebPConverterLite\Admin\Ajax\BulkConverter;
use RobertWP\WebPConverterLite\Admin\Ajax\DeactivateFeedbackHandler;
use RobertWP\WebPConverterLite\Admin\Menu;
use RobertWP\WebPConverterLite\Admin\Services\AutoOptimizer;
use RobertWP\WebPConverterLite\Admin\Services\WebPConverter;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsHandler;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Admin\UI\AdminNotice;
use RobertWP\WebPConverterLite\Admin\UI\PluginMetaLinks;
use RobertWP\WebPConverterLite\Assets\AdminAssets;

class HooksRegistrar {

    public static function register(): void
    {
        self::register_core_hooks();    // Core functionality, such as version checking, activation, etc.
        self::register_admin_hooks();    // Admin backend hooks
    }

    private static function register_core_hooks(): void
    {
        add_action('admin_init', self::cb([VersionChecker::class, 'check']));
        add_action('admin_init', self::cb([AdminNotice::class,'maybe_add_notice']));
        add_action('admin_init', self::cb([EnvironmentChecker::class,'maybe_show_image_library_notice']));
    }

    private static function register_admin_hooks(): void
    {
        if (!is_admin()) return;

        $menu = Menu::get_instance();
        $sr = SettingsRegistrar::get_instance();
        $sh = SettingsHandler::get_instance();
        $ao = AutoOptimizer::get_instance();
        $bc = BulkConverter::get_instance();
        $wc = WebPConverter::get_instance();
        $df = DeactivateFeedbackHandler::get_instance();

        // admin_menu
        add_action('admin_menu', [$menu, 'add_settings_menu']);

        // admin_init
        add_action('admin_init', [$sr, 'register_settings']);

        //upload
        add_filter('wp_generate_attachment_metadata', [$ao, 'handle_upload'], 10, 2);

        //wp_ajax_
        add_action('wp_ajax_rwwcl_bulk_convert', self::cb([$bc, 'handle_request']));
        add_action('wp_ajax_rwwcl_deactivate_feedback', [$df, 'handle_request']);

        //delete
        add_action('delete_attachment', [$ao, 'handle_deleted_attachment']);

        //edit
        add_filter('wp_update_attachment_metadata', [$wc, 'after_edit_metadata'], 10, 2);

        // plugin meta
        add_action('plugin_action_links_' . plugin_basename(RWWCL_PLUGIN_FILE), [PluginMetaLinks::class, 'add_links']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);

        // update_option_
        add_action('update_option_rwwcl_settings', self::cb([$sh, 'after_settings_saved']), 10, 2);

    }

    private static function cb($callback): callable
    {
        return CallbackWrapper::plugin_context_only($callback);
    }

}
