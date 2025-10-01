<?php
/**
 * Plugin Name: SOP JSON Viewer
 * Plugin URI: https://yoursite.com/sop-json-viewer
 * Description: Plugin WordPress untuk menampilkan konten SOP dalam format accordion interaktif menggunakan data JSON dengan editor validasi real-time
 * Version: 1.0.0
 * Author: Rudy Hermawan
 * Author URI: https://yoursite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sop-json-viewer
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SJP_VERSION', '1.0.0');
define('SJP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SJP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SJP_PLUGIN_FILE', __FILE__);

// Include required classes
include_once SJP_PLUGIN_PATH . 'includes/class-sop-json-viewer.php';
include_once SJP_PLUGIN_PATH . 'includes/class-admin-interface.php';
include_once SJP_PLUGIN_PATH . 'includes/class-json-validator.php';

// Initialize plugin
function sjp_initialize_plugin() {
    $plugin = new SOP_JSON_Viewer();
    $plugin->init();
}
add_action('plugins_loaded', 'sjp_initialize_plugin');