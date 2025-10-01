# 02 - Create Plugin Core

## Tujuan Step
Implementasi core functionality plugin termasuk shortcode dan data management dasar.

## Langkah Detail

### 2.1 Extend Main Plugin Class
Update `class-sop-json-viewer.php` dengan core functionality:

```php
<?php
class SOP_JSON_Viewer {

    private $sop_data = array();

    public function init() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Register shortcode
        add_shortcode('sop-accordion', array($this, 'render_sop_accordion'));

        // AJAX handlers untuk admin
        add_action('wp_ajax_sjp_save_sop_data', array($this, 'ajax_save_sop_data'));
        add_action('wp_ajax_sjp_load_sop_data', array($this, 'ajax_load_sop_data'));

        register_activation_hook(SJP_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(SJP_PLUGIN_FILE, array($this, 'deactivate'));
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('sjp-accordion-css', SJP_PLUGIN_URL . 'assets/css/sop-accordion.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-accordion-js', SJP_PLUGIN_URL . 'assets/js/sop-accordion.js', array('jquery'), SJP_VERSION, true);

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

        wp_enqueue_style('sjp-admin-css', SJP_PLUGIN_URL . 'assets/css/admin.css', array(), SJP_VERSION);
        wp_enqueue_script('sjp-admin-js', SJP_PLUGIN_URL . 'assets/js/admin-editor.js', array('jquery'), SJP_VERSION, true);
        wp_enqueue_code_editor(array('type' => 'application/json'));
    }
}
```

### 2.2 Implement Shortcode Functionality
Tambahkan method untuk render accordion:

```php
public function render_sop_accordion($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
        'class' => ''
    ), $atts);

    if (empty($atts['id'])) {
        return '<p>Error: SOP ID is required</p>';
    }

    $sop_data = $this->get_sop_data($atts['id']);

    if (!$sop_data) {
        return '<p>SOP data not found for ID: ' . esc_html($atts['id']) . '</p>';
    }

    ob_start();
    ?>
    <div class="sop-json-viewer <?php echo esc_attr($atts['class']); ?>"
         data-sop-id="<?php echo esc_attr($atts['id']); ?>">

        <?php if (!empty($sop_data['title'])): ?>
            <h2 class="sop-title"><?php echo esc_html($sop_data['title']); ?></h2>
        <?php endif; ?>

        <?php if (!empty($sop_data['description'])): ?>
            <p class="sop-description"><?php echo esc_html($sop_data['description']); ?></p>
        <?php endif; ?>

        <div class="sop-accordion" role="tablist" aria-multiselectable="true">
            <?php echo $this->render_sections($sop_data['sections']); ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

private function render_sections($sections) {
    if (empty($sections)) {
        return '';
    }

    $output = '';
    foreach ($sections as $index => $section) {
        $section_id = 'section-' . $index;
        $output .= '<div class="sop-section">';

        // Section header
        $output .= '<button class="sop-section-header" 
                          id="header-' . $section_id . '"
                          aria-controls="content-' . $section_id . '"
                          aria-expanded="false"
                          role="tab">
            <span class="sop-section-title">' . esc_html($section['title']) . '</span>
            <span class="sop-toggle-icon" aria-hidden="true">+</span>
        </button>';

        // Section content
        $output .= '<div class="sop-section-content"
                         id="content-' . $section_id . '"
                         aria-labelledby="header-' . $section_id . '"
                         role="tabpanel"
                         hidden>';

        if (!empty($section['content'])) {
            $output .= '<div class="sop-content">' . wp_kses_post($section['content']) . '</div>';
        }

        // Render subsections jika ada
        if (!empty($section['subsections'])) {
            $output .= '<div class="sop-subsections">';
            $output .= $this->render_sections($section['subsections']);
            $output .= '</div>';
        }

        $output .= '</div></div>';
    }

    return $output;
}
```

### 2.3 Data Management Methods
Implementasi basic data storage dan retrieval:

```php
private function get_sop_data($sop_id) {
    $default_data = array(
        'title' => 'Default SOP Title',
        'description' => 'Default SOP Description',
        'sections' => array(
            array(
                'title' => 'Section 1',
                'content' => 'Content for section 1'
            )
        )
    );

    // Untuk sementara return default data
    // Step berikutnya akan implementasi penyimpanan ke database
    return $default_data;
}

public function ajax_save_sop_data() {
    // Check nonce untuk security
    if (!wp_verify_nonce($_POST['nonce'], 'sjp_nonce')) {
        wp_die('Security check failed');
    }

    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }

    $sop_id = sanitize_text_field($_POST['sop_id']);
    $sop_data = $_POST['sop_data'];

    // Validate JSON
    $decoded_data = json_decode(stripslashes($sop_data), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
    }

    // Sanitize data sebelum save
    $sanitized_data = $this->sanitize_sop_data($decoded_data);

    // Save ke database (akan diimplementasi di step berikutnya)
    $result = update_option('sjp_sop_data_' . $sop_id, $sanitized_data);

    if ($result) {
        wp_send_json_success('SOP data saved successfully');
    } else {
        wp_send_json_error('Failed to save SOP data');
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
        $sanitized['content'] = wp_kses_post($section['content']);
    }

    if (isset($section['subsections']) && is_array($section['subsections'])) {
        $sanitized['subsections'] = array();
        foreach ($section['subsections'] as $subsection) {
            $sanitized['subsections'][] = $this->sanitize_section_data($subsection);
        }
    }

    return $sanitized;
}
```

### 2.4 Create Basic CSS
Buat file `assets/css/sop-accordion.css`:

```css
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

.sop-section-header:focus {
    outline: 2px solid #007cba;
    outline-offset: -2px;
}

.sop-section-header[aria-expanded="true"] {
    background: #007cba;
    color: white;
}

.sop-section-header[aria-expanded="true"] .sop-toggle-icon {
    transform: rotate(45deg);
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

.sop-content p {
    margin-bottom: 15px;
}

.sop-content a {
    color: #007cba;
    text-decoration: none;
}

.sop-content a:hover {
    text-decoration: underline;
}

.sop-subsections {
    margin-top: 15px;
    padding-left: 20px;
    border-left: 3px solid #007cba;
}

.sop-subsections .sop-section-header {
    background: #f1f3f4;
    font-size: 1em;
}

.sop-subsections .sop-section-header[aria-expanded="true"] {
    background: #e3f2fd;
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

    .sop-subsections {
        padding-left: 15px;
    }
}
```

## File yang Dibuat/Dimodifikasi di Step Ini
- Modified: `includes/class-sop-json-viewer.php` (tambah core functionality)
- Modified: `assets/css/sop-accordion.css` (tambah styling dasar)

## Testing Checkpoint
1. Shortcode `[sop-accordion id="test"]` dapat ditampilkan tanpa error
2. Accordion structure tampil dengan benar (header + content area)
3. CSS styling applied dengan baik
4. AJAX endpoints registered tanpa error
5. No PHP errors atau warnings di debug log

## Next Step
Lanjut ke Step 03 untuk implementasi admin interface dan JSON editor.