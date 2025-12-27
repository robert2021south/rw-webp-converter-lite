<?php
namespace RobertWP\WebPConverterLite\Core;

class Context
{
    public static function is_plugin_context(): bool
    {
        global $pagenow;

        if (is_admin()) {
            $page = sanitize_text_field(wp_unslash($_GET['page'] ?? ''));
            // 1. Check if on admin post/page/custom type list page
            if ($pagenow === 'edit.php') {
                return true;
            }
            // 2. Check for plugin-specific pages (e.g., ?page=rwwcl...)
            if (str_starts_with($page, 'rwwcl')) {
                return true;
            }
        }

        // 3. Check for plugin-specific AJAX/REST operations
        $action = sanitize_text_field(wp_unslash( $_REQUEST['action'] ?? '' ));
        if (str_starts_with($action, 'rwwcl_')) {
            return true;
        }

        $uri = sanitize_text_field(wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ));
        if (defined('REST_REQUEST') && REST_REQUEST && strpos($uri ?? '', '/wp-json/rwwcl/') !== false) {
            return true;
        }

        return false;
    }
}

