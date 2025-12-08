<?php
/**
 * Plugin Name: RW WebP Converter Lite
 * Description:
 * Version: 1.0.2
 * Author: RobertWP (Robert South)
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
define( 'RWWCL_PLUGIN_VERSION', '1.0.0' );
define( 'RWWCL_PLUGIN_FILE', __FILE__ );
define( 'RWWCL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RWWCL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RWWCL_ASSETS_URL', RWWCL_PLUGIN_URL . 'assets/' );

require_once RWWCL_PLUGIN_DIR . 'includes/core/plugin.php';

