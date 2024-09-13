<?php

/**
 * Plugin Name: Vortex
 * Plugin URI: https://github.com/NeoAdewole/vortex-wp
 * Description: A Plugin to the Vortex contenct agggregator for WordPress.   
 * Version: 1.0.0
 * Author: Niyi Adewole
 * Author URI: https://github.com/NeoAdewole/vortex-wp
 * Text Domain:       vortex
 * Version:           0.0.1
 * Requires at least: 6.2
 * Requires PHP:      7.0
 * License: MIT
 * License URI: https://github.com/NeoAdewole/vortex-wp/MITLICENSE
 */

// namespace Vortex;

if (!function_exists('add_action')) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define('VORTEXWP_VERSION', '0.0.1');
define('VORTEXWP__MINIMUM_WP_VERSION', '6.2');
define('VORTEXWP__MINIMUM_PHP_VERSION', '7.0');
define('VORTEXWP__PLUGIN_URL', plugin_dir_url(__DIR__));
define('VORTEXWP__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VORTEXWP__PLUGIN_FILE', __FILE__);


// Includes
$rootFiles = glob(VORTEXWP__PLUGIN_DIR . 'includes/*.php');
$subdirectoryFiles = glob(VORTEXWP__PLUGIN_DIR . 'includes/**/*.php');
$allFiles = array_merge($rootFiles, $subdirectoryFiles);

foreach ($allFiles as $filename) {
  include_once($filename);
}

// Hooks
register_activation_hook(__FILE__, array('vortex', 'vortex_plugin_activation'));
register_deactivation_hook(__FILE__, array('vortex', 'vortex_plugin_deactivation'));

add_action('init', array('vortex', 'init'));
add_action('init', 'vortex_register_assets');
add_action('admin_init', array('Vortex_Settings', 'vortex_register_settings'));
add_action('admin_init', array('Vortex_Channels', 'vortex_register_channel_settings'));
add_action('admin_menu', array('Vortex_Menu', 'vortex_admin_menus'));
add_action('wp_enqueue_scripts', 'vortex_enqueue_scripts', 90);
