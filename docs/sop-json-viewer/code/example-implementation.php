<?php
/**
 * SOP JSON Viewer - Example Implementation
 *
 * This file contains example code untuk berbagai use case
 * dalam implementasi plugin SOP JSON Viewer.
 */

// Example 1: Basic Shortcode Usage
function sjp_example_basic_usage() {
    $content = '
    <h2>Prosedur Kerja Harian</h2>
    <p>Berikut adalah prosedur kerja harian yang harus diikuti oleh semua karyawan:</p>

    [sop-accordion id="prosedur-harian"]

    <p>Silakan baca dan pahami setiap langkah dengan seksama.</p>
    ';

    return $content;
}

// Example 2: Multiple SOPs pada satu halaman
function sjp_example_multiple_sops() {
    $content = '
    <div class="sop-container">
        <section class="sop-section">
            <h2>Prosedur Administrasi</h2>
            [sop-accordion id="prosedur-admin" class="admin-style"]
        </section>

        <section class="sop-section">
            <h2>Prosedur Operasional</h2>
            [sop-accordion id="prosedur-ops" class="ops-style"]
        </section>

        <section class="sop-section">
            <h2>Prosedur Keuangan</h2>
            [sop-accordion id="prosedur-keuangan" class="finance-style"]
        </section>
    </div>
    ';

    return $content;
}

// Example 3: Custom Styling dengan CSS
function sjp_example_custom_styling() {
    ?>
    <style>
    /* Custom styling untuk SOP sections */
    .sop-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .sop-section {
        margin-bottom: 40px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007cba;
    }

    .sop-section h2 {
        color: #007cba;
        margin-bottom: 20px;
    }

    /* Custom accordion styling */
    .admin-style .sop-section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .ops-style .sop-section-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .finance-style .sop-section-header {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    </style>

    <div class="sop-container">
        <h1>Dokumentasi Prosedur Perusahaan</h1>

        <section class="sop-section">
            <h2>Administrasi</h2>
            [sop-accordion id="prosedur-admin" class="admin-style"]
        </section>

        <section class="sop-section">
            <h2>Operasional</h2>
            [sop-accordion id="prosedur-ops" class="ops-style"]
        </section>

        <section class="sop-section">
            <h2>Keuangan</h2>
            [sop-accordion id="prosedur-keuangan" class="finance-style"]
        </section>
    </div>
    <?php
}

// Example 4: Integration dengan tema atau page template
function sjp_example_theme_integration() {
    // Dalam functions.php tema
    add_action('wp_enqueue_scripts', 'sjp_theme_enqueue_assets');

    function sjp_theme_enqueue_assets() {
        wp_enqueue_style('sop-custom-style', get_template_directory_uri() . '/css/sop-custom.css', array(), '1.0.0');
    }
}

// Example 5: Widget untuk sidebar
class SOP_Quick_Access_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'sop_quick_access_widget',
            __('SOP Quick Access', 'sop-json-viewer'),
            array('description' => __('Display quick access ke SOP penting', 'sop-json-viewer'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Display quick access links
        $quick_sops = array(
            'prosedur-admin' => 'Administrasi',
            'prosedur-keamanan' => 'Keamanan',
            'prosedur-darurat' => 'Darurat'
        );

        echo '<ul class="sop-quick-links">';
        foreach ($quick_sops as $sop_id => $title) {
            echo '<li><a href="' . esc_url(home_url('/sop/' . $sop_id)) . '">' . esc_html($title) . '</a></li>';
        }
        echo '</ul>';

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Quick SOP Access', 'sop-json-viewer');
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

// Register widget
function sjp_register_widgets() {
    register_widget('SOP_Quick_Access_Widget');
}
add_action('widgets_init', 'sjp_register_widgets');

// Example 6: REST API endpoint untuk external access
function sjp_register_api_routes() {
    register_rest_route('sop-json-viewer/v1', '/sop/(?P<id>[a-zA-Z0-9-_]+)', array(
        'methods' => 'GET',
        'callback' => 'sjp_get_sop_data_api',
        'permission_callback' => '__return_true' // Public access
    ));
}
add_action('rest_api_init', 'sjp_register_api_routes');

function sjp_get_sop_data_api($request) {
    $sop_id = $request->get_param('id');

    if (empty($sop_id)) {
        return new WP_Error('no_sop_id', 'SOP ID is required', array('status' => 400));
    }

    // Get SOP data dari database
    $sop_data = get_option('sjp_sop_data_' . $sop_id, array());

    if (empty($sop_data)) {
        return new WP_Error('sop_not_found', 'SOP data not found', array('status' => 404));
    }

    return new WP_REST_Response($sop_data, 200);
}

// Example 7: Gutenberg block (untuk WordPress 5.0+)
function sjp_register_gutenberg_block() {
    // Register JavaScript untuk block
    wp_register_script(
        'sjp-gutenberg-block',
        plugins_url('assets/js/gutenberg-block.js', SJP_PLUGIN_FILE),
        array('wp-blocks', 'wp-element', 'wp-components', 'wp-editor'),
        SJP_VERSION
    );

    // Register block
    register_block_type('sop-json-viewer/sop-accordion', array(
        'editor_script' => 'sjp-gutenberg-block',
        'render_callback' => 'sjp_render_gutenberg_block',
        'attributes' => array(
            'sopId' => array(
                'type' => 'string',
                'default' => 'default-sop'
            ),
            'customClass' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
}
add_action('init', 'sjp_register_gutenberg_block');

function sjp_render_gutenberg_block($attributes) {
    $sop_id = $attributes['sopId'] ?? 'default-sop';
    $custom_class = $attributes['customClass'] ?? '';

    return sprintf(
        '[sop-accordion id="%s" class="%s"]',
        esc_attr($sop_id),
        esc_attr($custom_class)
    );
}

// Example 8: CLI command untuk batch operations (WP-CLI)
if (defined('WP_CLI') && WP_CLI) {
    class SOP_JSON_Viewer_CLI {

        public function export_all($args, $assoc_args) {
            $sop_ids = get_option('sjp_all_sop_ids', array());

            foreach ($sop_ids as $sop_id) {
                $sop_data = get_option('sjp_sop_data_' . $sop_id, array());

                if (!empty($sop_data)) {
                    $filename = "sop-{$sop_id}-" . date('Y-m-d') . '.json';
                    file_put_contents(
                        WP_CONTENT_DIR . '/exports/' . $filename,
                        json_encode($sop_data, JSON_PRETTY_PRINT)
                    );

                    WP_CLI::log("Exported: {$filename}");
                }
            }

            WP_CLI::success('All SOP data exported successfully');
        }

        public function import_batch($args, $assoc_args) {
            $directory = $assoc_args['directory'] ?? WP_CONTENT_DIR . '/imports/';

            if (!is_dir($directory)) {
                WP_CLI::error("Directory {$directory} not found");
            }

            $files = glob($directory . '/*.json');
            $imported = 0;

            foreach ($files as $file) {
                $sop_id = basename($file, '.json');

                $data = json_decode(file_get_contents($file), true);

                if ($data && update_option('sjp_sop_data_' . $sop_id, $data)) {
                    $imported++;
                    WP_CLI::log("Imported: {$sop_id}");
                }
            }

            WP_CLI::success("{$imported} SOP files imported successfully");
        }
    }

    WP_CLI::add_command('sop-json-viewer', 'SOP_JSON_Viewer_CLI');
}

// Example 9: Hook untuk custom functionality
// Tambahkan hook ini ke functions.php tema untuk custom behavior

// Custom filter untuk modify SOP data sebelum display
function sjp_custom_modify_sop_data($sop_data, $sop_id) {
    // Contoh: tambahkan informasi author dan last modified
    $sop_data['meta'] = array(
        'last_modified' => get_option('sjp_sop_modified_' . $sop_id, current_time('mysql')),
        'author' => get_option('sjp_sop_author_' . $sop_id, get_bloginfo('name')),
        'version' => get_option('sjp_sop_version_' . $sop_id, '1.0.0')
    );

    return $sop_data;
}
add_filter('sjp_sop_data', 'sjp_custom_modify_sop_data', 10, 2);

// Custom action ketika SOP section dibuka
function sjp_track_section_open($header, $content) {
    $sop_id = $header.closest('.sop-json-viewer').data('sop-id');
    $section_title = $header.find('.sop-section-title').text();

    // Track ke analytics atau logging system
    error_log("SOP Section Opened - ID: {$sop_id}, Section: {$section_title}");
}
add_action('sop:sectionOpened', 'sjp_track_section_open', 10, 2);

// Example 10: Performance optimization dengan caching
function sjp_optimize_sop_performance() {
    // Cache SOP data untuk 1 jam
    $cache_duration = HOUR_IN_SECONDS;

    add_filter('sjp_sop_data', function($sop_data, $sop_id) use ($cache_duration) {
        $cache_key = 'sjp_sop_data_' . $sop_id;
        $cached_data = wp_cache_get($cache_key);

        if ($cached_data !== false) {
            return $cached_data;
        }

        wp_cache_set($cache_key, $sop_data, '', $cache_duration);
        return $sop_data;
    }, 10, 2);
}
add_action('init', 'sjp_optimize_sop_performance');

// Example 11: Multilingual support dengan Polylang
function sjp_multilingual_sop_support() {
    if (function_exists('pll_register_string')) {
        // Register SOP titles untuk translation
        $sop_ids = get_option('sjp_all_sop_ids', array());

        foreach ($sop_ids as $sop_id) {
            $sop_data = get_option('sjp_sop_data_' . $sop_id, array());

            if (!empty($sop_data['title'])) {
                pll_register_string($sop_id . '_title', $sop_data['title'], 'SOP JSON Viewer');
            }

            if (!empty($sop_data['description'])) {
                pll_register_string($sop_id . '_description', $sop_data['description'], 'SOP JSON Viewer');
            }
        }
    }
}
add_action('init', 'sjp_multilingual_sop_support');

// Example 12: Integration dengan WooCommerce
function sjp_woocommerce_sop_integration() {
    // Auto-display SOP untuk produk tertentu
    add_filter('the_content', function($content) {
        global $post;

        if ($post->post_type === 'product' && function_exists('wc_get_product')) {
            $product = wc_get_product($post->ID);
            $category = $product->get_category_ids();

            // Display specific SOP berdasarkan kategori produk
            if (in_array(123, $category)) { // Ganti 123 dengan category ID
                $content .= do_shortcode('[sop-accordion id="prosedur-produk"]');
            }
        }

        return $content;
    });
}
add_action('init', 'sjp_woocommerce_sop_integration');
?>