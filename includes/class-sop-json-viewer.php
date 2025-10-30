<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Viewer {

    private $sop_data = array();

    public function __construct() {
        // Constructor
    }

    private $admin;

    public function init() {
        // Initialize hooks
        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));

        // Register shortcode
        add_shortcode('sop-accordion', array($this, 'render_sop_accordion'));

        // AJAX handlers untuk admin
        add_action('wp_ajax_sjp_save_sop_data', array($this, 'ajax_save_sop_data'));
        add_action('wp_ajax_sjp_load_sop_data', array($this, 'ajax_load_sop_data'));
        add_action('wp_ajax_sjp_validate_json', array($this, 'ajax_validate_json'));

        // Initialize admin interface
        $this->admin = new SOP_JSON_Viewer_Admin($this);

        register_activation_hook(SJP_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(SJP_PLUGIN_FILE, array($this, 'deactivate'));
    }

    public function load_textdomain() {
        load_plugin_textdomain('sop-json-viewer', false, dirname(SJP_PLUGIN_FILE) . '/languages/');
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('sjp-accordion-css', SJP_PLUGIN_URL . 'assets/css/sop-accordion.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-accordion-js', SJP_PLUGIN_URL . 'assets/js/sop-accordion.js', array(), SJP_VERSION, true);

        wp_localize_script('sjp-accordion-js', 'sjp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sjp_nonce')
        ));
    }

    public function enqueue_admin_assets($hook) {
        $allowed_hooks = array('toplevel_page_sjp-admin', 'sop-json-viewer_page_sjp-settings');

        if (!in_array($hook, $allowed_hooks)) {
            return;
        }

        // Ensure Dashicons are loaded
        wp_enqueue_style('dashicons');
        
        wp_enqueue_style('sjp-admin-css', SJP_PLUGIN_URL . 'assets/css/admin.css', array('dashicons'), SJP_VERSION);
        wp_enqueue_style('sjp-accordion-css', SJP_PLUGIN_URL . 'assets/css/sop-accordion.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-admin-js', SJP_PLUGIN_URL . 'assets/js/admin-editor.js', array(), SJP_VERSION, true);
        wp_enqueue_script('sjp-accordion-js', SJP_PLUGIN_URL . 'assets/js/sop-accordion.js', array(), SJP_VERSION, true);
        wp_enqueue_code_editor(array('type' => 'application/json'));

        // Localize AJAX variables untuk admin script
        wp_localize_script('sjp-admin-js', 'sjp_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sjp_nonce')
        ));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('SOP JSON Viewer', 'sop-json-viewer'),
            __('SOP Viewer', 'sop-json-viewer'),
            'manage_options',
            'sjp-admin',
            array($this, 'admin_page_callback'),
            'dashicons-editor-table',
            30
        );

        add_submenu_page(
            'sjp-admin',
            __('Manage SOP Data', 'sop-json-viewer'),
            __('Manage SOP', 'sop-json-viewer'),
            'manage_options',
            'sjp-admin',
            array($this, 'admin_page_callback')
        );

        add_submenu_page(
            'sjp-admin',
            __('Settings', 'sop-json-viewer'),
            __('Settings', 'sop-json-viewer'),
            'manage_options',
            'sjp-settings',
            array($this, 'settings_page_callback')
        );
    }

    public function register_settings() {
        register_setting('sjp_settings_group', 'sjp_default_sop_id');
        register_setting('sjp_settings_group', 'sjp_enable_validation');
        register_setting('sjp_settings_group', 'sjp_default_section_visibility');
    }

    public function admin_page_callback() {
        $this->admin->admin_page_callback();
    }

    public function settings_page_callback() {
        $this->admin->settings_page_callback();
    }

    public function activate() {
        // Activation logic
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Deactivation logic
        flush_rewrite_rules();
    }

    public function render_sop_accordion($atts) {
        try {
            $atts = shortcode_atts(array(
                'id' => '',
                'class' => '',
                'fallback' => 'default',
                'default_visibility' => get_option('sjp_default_section_visibility', 'hidden')
            ), $atts);

            if (empty($atts['id'])) {
                return $this->render_error('SOP ID is required');
            }

            $sop_data = $this->get_sop_data($atts['id']);

            if (!$sop_data) {
                $fallback_message = $this->get_fallback_content($atts['fallback'], $atts['id']);
                return $fallback_message;
            }

            return $this->render_accordion_html($sop_data, $atts);

        } catch (Exception $e) {
            error_log('SOP JSON Viewer Error: ' . $e->getMessage());
            return $this->render_error('An error occurred while rendering the SOP accordion');
        }
    }

    private function render_error($message) {
        return '<div class="sop-error" style="padding: 15px; background: #ffe6e6; border: 1px solid #ff9999; border-radius: 4px; color: #cc0000; margin: 10px 0;">' .
               '<strong>SOP JSON Viewer Error:</strong> ' . esc_html($message) .
               '</div>';
    }

    private function get_fallback_content($fallback_type, $sop_id) {
        switch ($fallback_type) {
            case 'message':
                return '<div class="sop-fallback" style="padding: 20px; text-align: center; color: #666;">' .
                       '<p>SOP content for "' . esc_html($sop_id) . '" is not available.</p>' .
                       '<p>Please contact the administrator to add this content.</p>' .
                       '</div>';

            case 'default':
            default:
                return $this->render_accordion_html($this->get_default_sop_data(), array('id' => $sop_id));
        }
    }

    private function get_default_sop_data() {
        return array(
            'title' => 'SOP Not Found',
            'description' => 'The requested SOP content could not be loaded.',
            'sections' => array(
                array(
                    'title' => 'Content Unavailable',
                    'content' => 'The SOP content you are looking for is currently unavailable. Please try again later or contact your administrator.'
                )
            )
        );
    }

    private function render_accordion_html($sop_data, $atts) {
        // Add analytics jika diperlukan
        if (apply_filters('sjp_enable_analytics', false)) {
            add_action('wp_footer', function() use ($atts) {
                echo $this->render_analytics_code($atts['id']);
            });
        }

        ob_start();
        ?>
        <div class="sop-json-viewer <?php echo esc_attr($atts['class']); ?>"
             data-sop-id="<?php echo esc_attr($atts['id']); ?>"
             data-default-visibility="<?php echo esc_attr($atts['default_visibility']); ?>">

            <?php if (!empty($sop_data['title'])): ?>
                <h2 class="sop-title"><?php echo esc_html($sop_data['title']); ?></h2>
            <?php endif; ?>

            <?php if (!empty($sop_data['description'])): ?>
                <p class="sop-description"><?php echo esc_html($sop_data['description']); ?></p>
            <?php endif; ?>

            <div class="sop-accordion" role="tablist" aria-multiselectable="true">
                <?php echo $this->render_sections($sop_data['sections'], $atts['default_visibility']); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_analytics_code($sop_id) {
        return "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const accordion = document.querySelector('.sop-json-viewer[data-sop-id=\"{$sop_id}\"]');
                if (accordion) {
                    accordion.addEventListener('sop:sectionOpened', function(e) {
                        const header = e.detail.header;
                        const sectionTitle = header.querySelector('.sop-section-title');
                        if (typeof gtag !== 'undefined' && sectionTitle) {
                            gtag('event', 'sop_section_opened', {
                                'sop_id': '{$sop_id}',
                                'section_title': sectionTitle.textContent
                            });
                        }
                    });
                }
            });
        </script>";
    }

    private function render_sections($sections, $default_visibility = 'hidden', $section_index = 0) {
        if (empty($sections)) {
            return '';
        }

        $output = '';
        foreach ($sections as $index => $section) {
            $section_id = 'section-' . ($section_index + $index);

            // Check for per-section expanded property first, then fall back to default_visibility
            $section_expanded = isset($section['expanded']) ? $section['expanded'] : null;

            if ($section_expanded !== null) {
                // Per-section setting takes precedence
                $should_be_expanded = (bool) $section_expanded;
            } else {
                // Use global default_visibility logic
                $is_first_section = ($section_index === 0 && $index === 0);
                $should_be_expanded = ($default_visibility === 'shown' && $is_first_section);
            }

            $output .= '<div class="sop-section">';

            // Section header
            $output .= '<button class="sop-section-header"
                              id="header-' . $section_id . '"
                              aria-controls="content-' . $section_id . '"
                              aria-expanded="' . ($should_be_expanded ? 'true' : 'false') . '"
                              role="tab">
                <span class="sop-section-title">' . esc_html($section['title']) . '</span>
                <span class="sop-toggle-icon" aria-hidden="true">' . ($should_be_expanded ? '−' : '+') . '</span>
            </button>';

            // Section content
            $output .= '<div class="sop-section-content"
                             id="content-' . $section_id . '"
                             aria-labelledby="header-' . $section_id . '"
                             role="tabpanel"'
                             . ($should_be_expanded ? '' : ' hidden') . '>';

            if (!empty($section['content'])) {
                // Extract sort options from section
                $sort_options = array(
                    'sort' => isset($section['sort']) ? $section['sort'] : true,
                    'sort_by' => isset($section['sort_by']) ? $section['sort_by'] : 'title',
                    'sort_order' => isset($section['sort_order']) ? $section['sort_order'] : 'asc'
                );

                $output .= $this->render_content($section['content'], $sort_options);
            }

            // Render subsections jika ada
            if (!empty($section['subsections'])) {
                $output .= '<div class="sop-subsections">';
                $output .= $this->render_sections($section['subsections'], $default_visibility);
                $output .= '</div>';
            }

            $output .= '</div></div>';
        }

        return $output;
    }

    private function render_content($content, $sort_options = array()) {
        $output = '<div class="sop-content">';
        
        if (is_string($content)) {
            // Regular HTML content
            $output .= wp_kses_post($content);
        } else if (is_array($content)) {
            // Array of content objects
            $output .= $this->render_content_items($content, $sort_options);
        }
        
        $output .= '</div>';
        return $output;
    }

    private function render_content_items($content_items, $sort_options = array()) {
        $output = '';
        
        // Check if sorting is enabled
        $sort_enabled = isset($sort_options['sort']) ? $sort_options['sort'] : true;
        $sort_by = isset($sort_options['sort_by']) ? $sort_options['sort_by'] : 'title';
        $sort_order = isset($sort_options['sort_order']) ? $sort_options['sort_order'] : 'asc';
        
        // Sort link items if enabled
        if ($sort_enabled && is_array($content_items)) {
            usort($content_items, function($a, $b) use ($sort_by, $sort_order) {
                $a_value = isset($a[$sort_by]) ? $a[$sort_by] : '';
                $b_value = isset($b[$sort_by]) ? $b[$sort_by] : '';
                
                $comparison = strcasecmp($a_value, $b_value);
                return $sort_order === 'desc' ? -$comparison : $comparison;
            });
        }
        
        foreach ($content_items as $content_item) {
            $output .= $this->render_content_item($content_item);
        }
        
        return $output;
    }

    private function render_content_item($content_item, $sort_options = array()) {
        $output = '';
        
        if (!isset($content_item['type'])) {
            return $output;
        }
        
        switch ($content_item['type']) {
            case 'link':
                $output .= $this->render_link_item($content_item);
                break;
                
            default:
                // For unknown types, try to render as basic content
                if (isset($content_item['title'])) {
                    $output .= '<h4>' . esc_html($content_item['title']) . '</h4>';
                }
                if (isset($content_item['content'])) {
                    if (is_array($content_item['content'])) {
                        $output .= $this->render_content_items($content_item['content'], $sort_options);
                    } else {
                        $output .= '<div>' . wp_kses_post($content_item['content']) . '</div>';
                    }
                }
                break;
        }
        
        return $output;
    }

    private function render_link_item($link_item) {
        $title = isset($link_item['title']) ? esc_html($link_item['title']) : '';
        $url = isset($link_item['url']) ? esc_url($link_item['url']) : '#';
        $target = isset($link_item['target']) ? esc_attr($link_item['target']) : '_self';
        
        $output = '<div class="sop-link-item">';
        $output .= '<a href="' . $url . '" target="' . $target . '" class="sop-link">';
        $output .= '<span class="sop-link-title">' . $title . '</span>';
        $output .= '<span class="sop-link-icon" aria-hidden="true">→</span>';
        $output .= '</a>';
        $output .= '</div>';
        
        return $output;
    }

    private function get_sop_data($sop_id) {
        $default_data = $this->get_default_sop_data();

        // Try to get from database first
        $saved_data = get_option('sjp_sop_data_' . $sop_id, array());

        if (!empty($saved_data)) {
            return wp_parse_args($saved_data, $default_data);
        }

        return $default_data;
    }

    public function ajax_save_sop_data() {
        // Check nonce untuk security
        if (!wp_verify_nonce($_POST['nonce'], 'sjp_nonce')) {
            error_log('[SOP JSON Viewer] Save failed: Security check failed for SOP data save attempt');
            wp_send_json_error('Security check failed');
        }

        // Check user capability
        if (!current_user_can('manage_options')) {
            error_log('[SOP JSON Viewer] Save failed: Insufficient permissions for user ' . get_current_user_id());
            wp_send_json_error('Insufficient permissions');
        }

        $sop_id = sanitize_text_field($_POST['sop_id']);
        $sop_data = wp_unslash($_POST['sop_data']);

        // Log save attempt details
        $data_size = strlen($sop_data);
        error_log(sprintf(
            '[SOP JSON Viewer] Save attempt - SOP ID: %s, Data size: %d bytes, User ID: %d',
            $sop_id,
            $data_size,
            get_current_user_id()
        ));

        // Validate JSON
        $decoded_data = json_decode($sop_data, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error_msg = sprintf(
                'Invalid JSON format for SOP ID "%s": %s (Error code: %d)',
                $sop_id,
                json_last_error_msg(),
                json_last_error()
            );
            error_log('[SOP JSON Viewer] ' . $error_msg);
            wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
        }

        // Sanitize data sebelum save
        $sanitized_data = $this->sanitize_sop_data($decoded_data);

        // Log sanitized data structure for debugging
        $sanitized_sections = isset($sanitized_data['sections']) ? count($sanitized_data['sections']) : 0;
        error_log(sprintf(
            '[SOP JSON Viewer] Sanitized data - SOP ID: %s, Sections count: %d, Title: %s',
            $sop_id,
            $sanitized_sections,
            isset($sanitized_data['title']) ? $sanitized_data['title'] : 'No title'
        ));

        // Save ke database
        $result = update_option('sjp_sop_data_' . $sop_id, $sanitized_data);

        if ($result) {
            // Clear cache jika ada
            wp_cache_delete('sjp_sop_data_' . $sop_id);

            error_log(sprintf(
                '[SOP JSON Viewer] Save successful - SOP ID: %s, Data size: %d bytes',
                $sop_id,
                $data_size
            ));
            wp_send_json_success('SOP data saved successfully');
        } else {
            // Detailed error logging for failed saves
            $error_details = sprintf(
                'Failed to save SOP data - SOP ID: %s, Data size: %d bytes, User ID: %d, Option name: %s',
                $sop_id,
                $data_size,
                get_current_user_id(),
                'sjp_sop_data_' . $sop_id
            );

            // Check if option already exists and has the same value (no actual change)
            $existing_data = get_option('sjp_sop_data_' . $sop_id);
            if ($existing_data === $sanitized_data) {
                $error_details .= ', Reason: Data identical to existing (update_option returns false for identical data)';
                error_log('[SOP JSON Viewer] ' . $error_details);
                wp_send_json_success('SOP data saved successfully (no changes detected)');
            } else {
                // Check for database errors
                global $wpdb;
                $error_details .= sprintf(
                    ', Database errors: %s, WordPress errors: %s',
                    $wpdb->last_error,
                    $wpdb->last_query
                );
                error_log('[SOP JSON Viewer] ' . $error_details);
                wp_send_json_error('Failed to save SOP data - check debug log for details');
            }
        }
    }

    public function ajax_load_sop_data() {
        if (!wp_verify_nonce($_POST['nonce'], 'sjp_nonce')) {
            wp_send_json_error('Security check failed');
        }

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $sop_id = sanitize_text_field($_POST['sop_id']);
        $sop_data = get_option('sjp_sop_data_' . $sop_id, array());

        if (!empty($sop_data)) {
            wp_send_json_success($sop_data);
        } else {
            wp_send_json_error('No data found for SOP ID: ' . $sop_id);
        }
    }

    public function ajax_validate_json() {
        // Check nonce untuk security
        if (!wp_verify_nonce($_POST['nonce'], 'sjp_nonce')) {
            wp_send_json_error('Security check failed');
        }

        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $json_data = wp_unslash($_POST['json_data']);

        if (empty($json_data)) {
            wp_send_json_error('JSON data is empty');
        }

        // Basic JSON syntax check
        $decoded_data = json_decode($json_data);
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('JSON Syntax Error: ' . json_last_error_msg());
        }

        // Use the validator class for structure validation
        $validator = new SOP_JSON_Validator();
        $is_valid = $validator->validate_json($json_data);

        if ($is_valid) {
            // Check for warnings
            $warnings = $validator->get_warnings();
            if (!empty($warnings)) {
                wp_send_json_success('JSON is valid with warnings: ' . implode(', ', $warnings));
            } else {
                wp_send_json_success('JSON is valid');
            }
        } else {
            // Get errors
            $errors = $validator->get_errors();
            wp_send_json_error(implode(', ', $errors));
        }
    }

    private function sanitize_sop_data($data) {
        $sanitized = array();

        if (isset($data['title'])) {
            $sanitized['title'] = sanitize_text_field($data['title']);
        }

        if (isset($data['description'])) {
            $sanitized['description'] = sanitize_textarea_field($data['description']);
        }

        if (isset($data['sections']) && is_array($data['sections'])) {
            $sanitized['sections'] = array();
            foreach ($data['sections'] as $section) {
                $sanitized['sections'][] = $this->sanitize_section_data($section);
            }
        }

        return $sanitized;
    }

    private function sanitize_section_data($section) {
        $sanitized = array();

        if (isset($section['title'])) {
            $sanitized['title'] = sanitize_text_field($section['title']);
        }

        if (isset($section['content'])) {
            if (is_string($section['content'])) {
                // Regular HTML content
                $sanitized['content'] = wp_kses_post($section['content']);
            } else if (is_array($section['content'])) {
                // Array of content objects
                $sanitized['content'] = array();
                foreach ($section['content'] as $content_item) {
                    $sanitized['content'][] = $this->sanitize_content_item($content_item);
                }
            }
        }

        if (isset($section['subsections']) && is_array($section['subsections'])) {
            $sanitized['subsections'] = array();
            foreach ($section['subsections'] as $subsection) {
                $sanitized['subsections'][] = $this->sanitize_section_data($subsection);
            }
        }

        return $sanitized;
    }

    private function sanitize_content_item($content_item) {
        $sanitized = array();

        if (isset($content_item['type'])) {
            $sanitized['type'] = sanitize_text_field($content_item['type']);
        }

        // Sanitize based on type
        switch ($sanitized['type']) {
            case 'link':
                if (isset($content_item['title'])) {
                    $sanitized['title'] = sanitize_text_field($content_item['title']);
                }
                
                if (isset($content_item['url'])) {
                    // Sanitize URL - allow http, https, and relative URLs
                    $url = $content_item['url'];
                    if (filter_var($url, FILTER_VALIDATE_URL) || (str_starts_with($url, '/') && !str_starts_with($url, '//'))) {
                        $sanitized['url'] = esc_url_raw($url);
                    } else {
                        // If invalid, fallback to empty string
                        $sanitized['url'] = '';
                    }
                }
                
                if (isset($content_item['target'])) {
                    $valid_targets = array('_blank', '_self', '_parent', '_top');
                    if (in_array($content_item['target'], $valid_targets)) {
                        $sanitized['target'] = $content_item['target'];
                    } else {
                        $sanitized['target'] = '_self'; // Default fallback
                    }
                }
                break;
                
            default:
                // For unknown types, just sanitize the basic fields
                foreach ($content_item as $key => $value) {
                    if (is_string($value)) {
                        $sanitized[$key] = sanitize_text_field($value);
                    }
                }
                break;
        }

        return $sanitized;
    }
}