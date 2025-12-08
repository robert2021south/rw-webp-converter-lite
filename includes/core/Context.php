<?php
namespace RobertWP\ImageOptimizerLite\Core;

class Context
{
    public static function is_plugin_context(): bool
    {
        global $pagenow;

        if (is_admin()) {
            $page = sanitize_text_field(wp_unslash($_GET['page'] ?? ''));
            // 1. 检查是否在后台文章/页面/自定义类型列表页
            if ($pagenow === 'edit.php') {
                return true;
            }
            // 2. 检查插件专属页面（如 ?page=rwwcl...）
            if (str_starts_with($page, 'rwwcl')) {
                return true;
            }
        }

        // 3. 检查插件专属 AJAX/REST 操作
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

