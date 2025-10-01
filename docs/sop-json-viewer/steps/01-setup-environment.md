# 01 - Setup Environment

## Tujuan Step
Mempersiapkan environment development dan struktur dasar plugin SOP JSON Viewer.

## Langkah Detail

### 1.1 Setup Development Environment
1. Pastikan WordPress sudah terinstall (lokal atau development server)
2. Install dan aktivasi tema default (Twenty Twenty-One atau similar)
3. Pastikan PHP version 7.4+ dan WordPress 5.0+
4. Install development tools: text editor, Git, Node.js (untuk minify assets jika diperlukan)

### 1.2 Create Plugin Structure
1. Buat folder `sop-json-viewer/` di `wp-content/plugins/`
2. Create file utama: `sop-json-viewer.php`
3. Buat folder struktur:
   ```
   includes/ (untuk PHP classes)
   assets/ (untuk CSS, JS, images)
   templates/ (untuk admin templates)
   ```

### 1.3 Basic Plugin Header
Buat file `sop-json-viewer.php` dengan header dasar:

```php
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

// Include main class
include_once SJP_PLUGIN_PATH . 'includes/class-sop-json-viewer.php';

// Initialize plugin
function sjp_initialize_plugin() {
    $plugin = new SOP_JSON_Viewer();
    $plugin->init();
}
add_action('plugins_loaded', 'sjp_initialize_plugin');
```

### 1.4 Create Main Plugin Class
Buat file `includes/class-sop-json-viewer.php`:

```php
<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Viewer {

    public function __construct() {
        // Constructor
    }

    public function init() {
        // Initialize hooks
        add_action('init', array($this, 'load_textdomain'));
        register_activation_hook(SJP_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(SJP_PLUGIN_FILE, array($this, 'deactivate'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('sop-json-viewer', false, dirname(SJP_PLUGIN_FILE) . '/languages/');
    }

    public function activate() {
        // Activation logic
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Deactivation logic
        flush_rewrite_rules();
    }
}
```

### 1.5 Create Basic Assets Structure
1. Buat folder `assets/css/`, `assets/js/`, `assets/images/`
2. Create placeholder files:
   - `assets/css/sop-accordion.css`
   - `assets/js/sop-accordion.js`
   - `assets/js/admin-editor.js`

## File yang Dibuat di Step Ini
- `sop-json-viewer/sop-json-viewer.php` (file utama plugin)
- `sop-json-viewer/includes/class-sop-json-viewer.php` (main class)
- `sop-json-viewer/assets/css/sop-accordion.css` (placeholder)
- `sop-json-viewer/assets/js/sop-accordion.js` (placeholder)
- `sop-json-viewer/assets/js/admin-editor.js` (placeholder)

## Testing Checkpoint
1. Plugin dapat diaktivasi tanpa error
2. Tidak ada PHP error atau warning di debug log
3. Plugin muncul di list installed plugins
4. Text domain loaded dengan benar

## Next Step
Lanjut ke Step 02 untuk implementasi core functionality dan shortcode.