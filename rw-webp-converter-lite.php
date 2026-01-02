<?php
/**
 * Plugin Name: RW WebP Converter Lite
 * Description: RW WebP Converter Lite is a lightweight WordPress plugin that converts JPG and PNG images to WebP format in bulk and automatically converts newly uploaded images, helping improve website performance. Automatic conversion can be toggled on or off in the settings.
 * Version: 1.0.1
 * Author: RobertWP
 * Author URI: https://robertwp.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rw-webp-converter-lite
 * Domain Path: /languages
 */

namespace RobertWP\WebPConverterLite;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RWWCL_PLUGIN_NAME', 'RW WebP Converter Lite' );
define( 'RWWCL_VERSION_OPTION', 'rwwcl_version' );
define( 'RWWCL_PLUGIN_VERSION', '1.0.1' );
define( 'RWWCL_PLUGIN_FILE', __FILE__ );
define( 'RWWCL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RWWCL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RWWCL_ASSETS_URL', RWWCL_PLUGIN_URL . 'assets/' );

require_once RWWCL_PLUGIN_DIR . 'includes/core/plugin.php';

