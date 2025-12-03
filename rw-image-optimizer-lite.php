<?php
/**
 * Plugin Name: RW Image Optimizer Lite
 * Description:
 * Version: 1.0.2
 * Author: RobertWP (Robert South)
 * Author URI: https://robertwp.com
 * License: GPLv3 or later
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: rw-image-optimizer-lite
 * Domain Path: /languages
 */

namespace RobertWP\ImageOptimizerLite;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'RWIOL_PLUGIN_NAME', 'RW Image Optimizer Lite' );
define( 'RWIOL_VERSION_OPTION', 'rwiol_version' );
define( 'RWIOL_PLUGIN_VERSION', '1.0.0' );
define( 'RWIOL_PLUGIN_FILE', __FILE__ );
define( 'RWIOL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RWIOL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RWIOL_ASSETS_URL', RWIOL_PLUGIN_URL . 'assets/' );

require_once RWIOL_PLUGIN_DIR . 'includes/core/plugin.php';

