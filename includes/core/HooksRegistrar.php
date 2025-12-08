<?php
namespace RobertWP\WebPConverterLite\Core;

use RobertWP\WebPConverterLite\Admin\Menu;
use RobertWP\WebPConverterLite\Admin\Pages\Bulk\Scanner;
use RobertWP\WebPConverterLite\Admin\Pages\Compressor\AutoCompressor;
use RobertWP\WebPConverterLite\Admin\Pages\Compressor\BatchCompressor;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsHandler;
use RobertWP\WebPConverterLite\Admin\Settings\SettingsRegistrar;
use RobertWP\WebPConverterLite\Admin\UI\AdminNotice;
use RobertWP\WebPConverterLite\Admin\UI\PluginMetaLinks;
use RobertWP\WebPConverterLite\Assets\AdminAssets;

class HooksRegistrar {

    public static function register(): void
    {
        self::register_core_hooks();    // 核心功能，如版本检查、激活等
        self::register_admin_hooks();    // 管理后台钩子
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
        $settings_registrar = SettingsRegistrar::get_instance();
        $settings_handler = new SettingsHandler();
        $ac = AutoCompressor::get_instance();
        $bc = BatchCompressor::get_instance();
        $s = Scanner::get_instance();

        // admin_menu
        add_action('admin_menu', [$menu, 'add_settings_menu']);

        // admin_init
        add_action('admin_init', [$settings_registrar, 'register_settings']);

        //wp_
        add_filter('wp_generate_attachment_metadata', [$ac, 'handle_attachment_metadata'], 10, 2);

        //wp_ajax_
        add_action('wp_ajax_rwwcl_scan_images', [$s, 'scan_unoptimized_images']);
        add_action('wp_ajax_rwwcl_bulk_optimize', [$bc, 'handle_batch_optimize_ajax']);

        // plugin meta
        add_action('plugin_action_links_' . plugin_basename(RWWCL_PLUGIN_FILE), [PluginMetaLinks::class, 'add_links']);
        add_action('admin_enqueue_scripts', [AdminAssets::class, 'enqueue']);

        // update_option_
        add_action('update_option_rwwcl_settings', self::cb([$settings_handler, 'after_settings_saved']), 10, 2);


    }

    private static function cb($callback): callable
    {
        return CallbackWrapper::plugin_context_only($callback);
    }

}
