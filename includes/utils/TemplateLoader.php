<?php
namespace RobertWP\WebPConverterLite\Utils;

class TemplateLoader
{
    protected static string $plugin_base_path = '';

    public static function init(string $base_path): void
    {
        self::$plugin_base_path = rtrim($base_path, '/\\') . '/';
    }

    public static function render(string $template_name, array $args = [], string $module = ''): string
    {
        $template_file = self::locate($template_name, $module);

        if ($template_file) {
            extract($args, EXTR_SKIP);
            ob_start();
            include $template_file;
            return ob_get_clean();
        }

        return '';
    }

    /**
     * Load Template
     *
     * @param string $template_name Template name (without path, e.g. 'export-settings')
     * @param array $args Data passed to the template
     * @param string $module Module name it belongs to (e.g. 'export', optional)
     */
    public static function load(string $template_name, array $args = [], string $module = ''): void
    {

        $template_file = self::locate($template_name, $module);

        if ($template_file) {
            extract($args, EXTR_SKIP);
            include $template_file;
        }
    }

    /**
     * Returns the actual path of the template (prioritizes internal module templates, then checks global template directories)
     *
     * @param string $template_name
     * @param string $module
     * @return string|null
     */
    protected static function locate(string $template_name, string $module = ''): ?string
    {
        $filename = $template_name . '.php';

        // 1. Module-private template
        if ($module) {
            $module_path = self::$plugin_base_path . 'includes/modules/' . $module . '/templates/' . $filename;
            if (file_exists($module_path)) {
                return $module_path;
            }
        }

        // 2. Admin page template
        $admin_path = self::$plugin_base_path . 'includes/admin/views/' . $filename;
        if (file_exists($admin_path)) {
            return $admin_path;
        }

        // 3. Global template directory
        $normalized = str_replace(['..', '//'], '', $template_name);
        $global_path = self::$plugin_base_path . 'includes/templates/' . $normalized . '.php';
        if (file_exists($global_path)) {
            return $global_path;
        }

        return null;
    }

    public static function exists(string $template_name, string $module = ''): bool
    {
        return self::locate($template_name, $module) !== null;
    }

}
