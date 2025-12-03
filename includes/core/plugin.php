<?php
namespace RobertWP\ImageOptimizerLite\Core;

require_once __DIR__ . '/../autoload.php';

add_action('plugins_loaded', [Bootstrap::class, 'run']);

register_activation_hook(RWIOL_PLUGIN_FILE, [Bootstrap::class, 'activate']);
register_deactivation_hook(RWIOL_PLUGIN_FILE, [Bootstrap::class, 'deactivate']);
register_uninstall_hook(RWIOL_PLUGIN_FILE, [Bootstrap::class, 'uninstall']);
