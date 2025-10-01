#!/bin/bash

# SOP JSON Viewer - File Generator Script
# This script helps generate the basic plugin structure and files

PLUGIN_NAME="sop-json-viewer"
PLUGIN_DIR="../sop-json-viewer"

echo "🚀 SOP JSON Viewer - File Generator"
echo "=================================="

# Function to create directory if it doesn't exist
create_dir() {
    if [ ! -d "$1" ]; then
        mkdir -p "$1"
        echo "✅ Created directory: $1"
    else
        echo "ℹ️  Directory already exists: $1"
    fi
}

# Function to create file with content
create_file() {
    local file_path="$1"
    local content="$2"
    local description="$3"

    # Create directory if it doesn't exist
    create_dir "$(dirname "$file_path")"

    # Create file with content
    echo "$content" > "$file_path"
    echo "✅ $description: $file_path"
}

# Create main plugin file
echo ""
echo "📁 Creating main plugin file..."

MAIN_PLUGIN_CONTENT="<?php
/**
 * Plugin Name: SOP JSON Viewer
 * Plugin URI: mailto:hrudy715@gmail.com/sop-json-viewer
 * Description: Plugin WordPress untuk menampilkan konten SOP dalam format accordion interaktif menggunakan data JSON dengan editor validasi real-time
 * Version: 1.0.0
 * Author: Rudy Hermawan
 * Author URI: mailto:hrudy715@gmail.com
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
    \$plugin = new SOP_JSON_Viewer();
    \$plugin->init();
}
add_action('plugins_loaded', 'sjp_initialize_plugin');
"

create_file "$PLUGIN_DIR/sop-json-viewer.php" "$MAIN_PLUGIN_CONTENT" "Created main plugin file"

# Create includes directory and files
echo ""
echo "📁 Creating includes directory and files..."

INCLUDES_DIR="$PLUGIN_DIR/includes"
create_dir "$INCLUDES_DIR"

# Main plugin class
MAIN_CLASS_CONTENT="<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Viewer {

    public function __construct() {
        // Constructor - initialize plugin
    }

    public function init() {
        // Initialize hooks and filters
        add_action('init', array(\$this, 'load_textdomain'));

        // Register shortcode
        add_shortcode('sop-accordion', array(\$this, 'render_sop_accordion'));

        // Enqueue assets
        add_action('wp_enqueue_scripts', array(\$this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array(\$this, 'enqueue_admin_assets'));

        // Admin menu
        add_action('admin_menu', array(\$this, 'add_admin_menu'));

        // AJAX handlers
        add_action('wp_ajax_sjp_save_sop_data', array(\$this, 'ajax_save_sop_data'));
        add_action('wp_ajax_sjp_load_sop_data', array(\$this, 'ajax_load_sop_data'));

        // Activation/Deactivation hooks
        register_activation_hook(SJP_PLUGIN_FILE, array(\$this, 'activate'));
        register_deactivation_hook(SJP_PLUGIN_FILE, array(\$this, 'deactivate'));
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

    public function enqueue_frontend_assets() {
        wp_enqueue_style('sjp-accordion-css', SJP_PLUGIN_URL . 'assets/css/sop-accordion.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-accordion-js', SJP_PLUGIN_URL . 'assets/js/sop-accordion.js', array('jquery'), SJP_VERSION, true);
    }

    public function enqueue_admin_assets(\$hook) {
        // Only load on our admin pages
        if (strpos(\$hook, 'sjp') === false) {
            return;
        }

        wp_enqueue_style('sjp-admin-css', SJP_PLUGIN_URL . 'assets/css/admin.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-admin-js', SJP_PLUGIN_URL . 'assets/js/admin-editor.js', array('jquery'), SJP_VERSION, true);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('SOP JSON Viewer', 'sop-json-viewer'),
            __('SOP Viewer', 'sop-json-viewer'),
            'manage_options',
            'sjp-admin',
            array(\$this, 'admin_page_callback'),
            'dashicons-editor-table',
            30
        );
    }

    public function admin_page_callback() {
        echo '<div class=\"wrap\"><h1>' . __('SOP JSON Viewer - Admin', 'sop-json-viewer') . '</h1>';
        echo '<p>' . __('Welcome to SOP JSON Viewer admin interface.', 'sop-json-viewer') . '</p></div>';
    }

    public function render_sop_accordion(\$atts) {
        \$atts = shortcode_atts(array(
            'id' => 'default-sop',
            'class' => ''
        ), \$atts);

        // Placeholder implementation
        return '<div class=\"sop-json-viewer ' . esc_attr(\$atts['class']) . '\">' .
               '<p>Accordion will be rendered here for SOP ID: ' . esc_html(\$atts['id']) . '</p>' .
               '</div>';
    }

    public function ajax_save_sop_data() {
        // Placeholder AJAX handler
        wp_send_json_success('Data saved (placeholder)');
    }

    public function ajax_load_sop_data() {
        // Placeholder AJAX handler
        wp_send_json_success(array('title' => 'Sample SOP', 'sections' => array()));
    }
}
"

create_file "$INCLUDES_DIR/class-sop-json-viewer.php" "$MAIN_CLASS_CONTENT" "Created main plugin class"

# Create assets directory and files
echo ""
echo "📁 Creating assets directory and files..."

ASSETS_DIR="$PLUGIN_DIR/assets"
create_dir "$ASSETS_DIR/css"
create_dir "$ASSETS_DIR/js"
create_dir "$ASSETS_DIR/images"

# CSS file
CSS_CONTENT="/* SOP JSON Viewer - Frontend Styles */

.sop-json-viewer {
    margin: 20px 0;
    font-family: inherit;
}

.sop-title {
    color: #333;
    margin-bottom: 10px;
    font-size: 1.5em;
}

.sop-description {
    color: #666;
    margin-bottom: 20px;
    line-height: 1.5;
}

.sop-accordion {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}

.sop-section {
    border-bottom: 1px solid #eee;
}

.sop-section:last-child {
    border-bottom: none;
}

.sop-section-header {
    width: 100%;
    padding: 15px 20px;
    background: #f8f9fa;
    border: none;
    text-align: left;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.1em;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.sop-section-header:hover {
    background: #e9ecef;
}

.sop-toggle-icon {
    font-size: 1.2em;
    font-weight: bold;
    transition: transform 0.3s ease;
}

.sop-section-content {
    padding: 0;
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s ease, padding 0.3s ease;
}

.sop-section-content:not([hidden]) {
    max-height: 1000px;
    padding: 20px;
}

.sop-content {
    line-height: 1.6;
    margin-bottom: 15px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sop-section-header {
        padding: 12px 15px;
        font-size: 1em;
    }

    .sop-section-content:not([hidden]) {
        padding: 15px;
    }
}
"

create_file "$ASSETS_DIR/css/sop-accordion.css" "$CSS_CONTENT" "Created frontend CSS"

# JavaScript file
JS_CONTENT="(function($) {
    'use strict';

    class SOPAccordion {
        constructor(element) {
            this.element = $(element);
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            this.element.on('click', '.sop-section-header', (e) => {
                e.preventDefault();
                this.toggleSection($(e.currentTarget));
            });
        }

        toggleSection(\$header) {
            const \$content = \$header.next('.sop-section-content');
            const isExpanded = \$header.attr('aria-expanded') === 'true';

            if (isExpanded) {
                this.closeSection(\$header, \$content);
            } else {
                this.openSection(\$header, \$content);
            }
        }

        openSection(\$header, \$content) {
            \$header.attr('aria-expanded', 'true');
            \$content.attr('hidden', false);

            \$content.css('max-height', '0px');
            \$content.animate({
                'max-height': \$content.prop('scrollHeight') + 'px',
                'padding-top': '20px',
                'padding-bottom': '20px'
            }, 300);
        }

        closeSection(\$header, \$content) {
            \$header.attr('aria-expanded', 'false');

            \$content.animate({
                'max-height': '0px',
                'padding-top': '0px',
                'padding-bottom': '0px'
            }, 300, function() {
                \$content.attr('hidden', true);
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        $('.sop-json-viewer').each(function() {
            new SOPAccordion($(this));
        });
    });

})(jQuery);
"

create_file "$ASSETS_DIR/js/sop-accordion.js" "$JS_CONTENT" "Created frontend JavaScript"

# Admin JavaScript
ADMIN_JS_CONTENT="(function($) {
    'use strict';

    class SOPAdminEditor {
        constructor() {
            this.init();
        }

        init() {
            this.bindEvents();
            console.log('SOP JSON Viewer admin initialized');
        }

        bindEvents() {
            // Add your admin functionality here
            $('#sjp-admin-form').on('submit', function(e) {
                e.preventDefault();
                console.log('Form submitted');
            });
        }
    }

    // Initialize when document is ready
    $(document).ready(function() {
        new SOPAdminEditor();
    });

})(jQuery);
"

create_file "$ASSETS_DIR/js/admin-editor.js" "$ADMIN_JS_CONTENT" "Created admin JavaScript"

# Create README file
echo ""
echo "📁 Creating documentation..."

README_CONTENT="# SOP JSON Viewer

Plugin WordPress untuk menampilkan konten SOP dalam format accordion interaktif menggunakan data JSON dengan editor validasi real-time.

## Installation

1. Upload folder plugin ke \`/wp-content/plugins/\`
2. Aktivasi plugin melalui menu Plugins di WordPress
3. Akses menu SOP Viewer di admin dashboard

## Usage

### Basic Shortcode
\`\`\`php
[sop-accordion id=\"nama-sop\"]
\`\`\`

### Dengan Custom Class
\`\`\`php
[sop-accordion id=\"nama-sop\" class=\"custom-style\"]
\`\`\`

## Features

- ✅ Shortcode untuk menampilkan SOP dalam format accordion
- ✅ Admin interface untuk edit JSON dengan validasi real-time
- ✅ Support nested accordion untuk sub-procedures
- ✅ Responsive design untuk semua device
- ✅ Accessibility support (WCAG 2.1 AA)
- ✅ Import/Export functionality untuk backup

## Requirements

- WordPress 5.0+
- PHP 7.4+
- Modern web browser dengan JavaScript enabled

## Support

Untuk support dan pertanyaan:
- Email: hrudy715@gmail.com
- Documentation: [Link dokumentasi]

## Changelog

### Version 1.0.0
- Initial release dengan basic functionality
- Admin interface untuk JSON editing
- Responsive accordion display
- Import/Export features
"

create_file "$PLUGIN_DIR/README.md" "$README_CONTENT" "Created plugin README"

# Create sample JSON data
echo ""
echo "📁 Creating sample data..."

SAMPLE_DATA='{
  "title": "Contoh Prosedur Operasional Standar",
  "description": "Dokumen ini berisi contoh struktur SOP untuk keperluan testing dan demonstrasi.",
  "sections": [
    {
      "title": "Persiapan Kerja",
      "content": "Sebelum memulai aktivitas kerja, pastikan untuk melakukan persiapan sebagai berikut:\n\n1. Periksa peralatan kerja\n2. Siapkan dokumen yang diperlukan\n3. Koordinasi dengan tim terkait\n4. Pastikan kondisi kesehatan prima",
      "subsections": [
        {
          "title": "Pemeriksaan Peralatan",
          "content": "Lakukan pemeriksaan menyeluruh terhadap semua peralatan yang akan digunakan. Pastikan tidak ada kerusakan atau malfunction."
        },
        {
          "title": "Koordinasi Tim",
          "content": "Lakukan koordinasi dengan anggota tim untuk memastikan semua orang memahami tugas dan tanggung jawab masing-masing."
        }
      ]
    },
    {
      "title": "Pelaksanaan Kerja",
      "content": "Selama pelaksanaan kerja, ikuti prosedur sebagai berikut:\n\n- Dokumentasi setiap langkah penting\n- Laporkan kendala yang ditemui\n- Jaga komunikasi dengan tim\n- Prioritas keselamatan kerja",
      "subsections": [
        {
          "title": "Dokumentasi",
          "content": "Catat semua aktivitas penting dalam log book atau sistem dokumentasi yang telah ditentukan."
        }
      ]
    },
    {
      "title": "Penyelesaian Kerja",
      "content": "Setelah menyelesaikan pekerjaan:\n\n1. Verifikasi hasil kerja\n2. Bersihkan area kerja\n3. Serahkan hasil kepada pihak terkait\n4. Buat laporan akhir"
    }
  ]
}'

create_file "$PLUGIN_DIR/sample-data.json" "$SAMPLE_DATA" "Created sample JSON data"

# Create .gitignore file
echo ""
echo "📁 Creating Git configuration..."

GITIGNORE_CONTENT="# SOP JSON Viewer - Git Ignore

# WordPress
/wp-content/
/wp-admin/
/wp-includes/
/wp-*.php

# Plugin specific
node_modules/
*.log
.DS_Store
Thumbs.db

# Development files
*.backup
*.tmp
.cache/

# Build files
dist/
build/

# IDE files
.vscode/
.idea/
*.swp
*.swo

# OS files
.DS_Store
Thumbs.db

# Logs
*.log
error_log

# Temporary files
*.tmp
*.temp
"

create_file "$PLUGIN_DIR/.gitignore" "$GITIGNORE_CONTENT" "Created .gitignore file"

echo ""
echo "🎉 File generation completed!"
echo ""
echo "Summary of created files:"
echo "=========================="
echo "📁 Main Plugin:"
echo "  • sop-json-viewer.php"
echo "  • README.md"
echo ""
echo "📁 Includes:"
echo "  • includes/class-sop-json-viewer.php"
echo ""
echo "📁 Assets:"
echo "  • assets/css/sop-accordion.css"
echo "  • assets/js/sop-accordion.js"
echo "  • assets/js/admin-editor.js"
echo ""
echo "📁 Sample Data:"
echo "  • sample-data.json"
echo ""
echo "📁 Configuration:"
echo "  • .gitignore"
echo ""
echo "Next steps:"
echo "1. Copy the generated files to your WordPress plugins directory"
echo "2. Activate the plugin in WordPress admin"
echo "3. Start customizing the code according to your needs"
echo "4. Test the functionality thoroughly"
echo ""
echo "Happy coding! 🚀"