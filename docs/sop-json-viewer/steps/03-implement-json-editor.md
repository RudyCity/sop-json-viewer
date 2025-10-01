# 03 - Implement JSON Editor

## Tujuan Step
Membuat admin interface untuk editing data JSON dengan validasi real-time dan user-friendly experience.

## Langkah Detail

### 3.1 Create Admin Menu
Tambahkan admin menu dan pages ke main plugin class:

```php
public function init() {
    // ... existing code ...

    // Admin menu
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_init', array($this, 'register_settings'));

    // ... existing code ...
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
}
```

### 3.2 Create Admin Interface Class
Buat file baru `includes/class-admin-interface.php`:

```php
<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Viewer_Admin {

    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    public function admin_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php _e('SOP JSON Viewer - Manage Data', 'sop-json-viewer'); ?></h1>

            <div class="sjp-admin-container">
                <div class="sjp-form-section">
                    <h2><?php _e('SOP Data Management', 'sop-json-viewer'); ?></h2>

                    <form id="sjp-sop-form" method="post" action="">
                        <?php wp_nonce_field('sjp_admin_nonce', 'sjp_admin_nonce'); ?>

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="sop_id"><?php _e('SOP ID', 'sop-json-viewer'); ?></label>
                                </th>
                                <td>
                                    <input type="text"
                                           id="sop_id"
                                           name="sop_id"
                                           value="default-sop"
                                           class="regular-text"
                                           required />
                                    <p class="description">
                                        <?php _e('Unique identifier untuk SOP ini (gunakan lowercase, hyphen-separated)', 'sop-json-viewer'); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <div class="sjp-json-editor-section">
                            <h3><?php _e('JSON Editor', 'sop-json-viewer'); ?></h3>
                            <div id="sjp-json-editor"
                                 style="border: 1px solid #ddd; min-height: 400px; padding: 10px;">
                            </div>
                            <textarea id="sjp-json-textarea"
                                      name="sop_json_data"
                                      style="display: none;"></textarea>
                        </div>

                        <div class="sjp-validation-status" id="sjp-validation-status"></div>

                        <div class="sjp-form-actions">
                            <button type="submit" class="button button-primary">
                                <?php _e('Save SOP Data', 'sop-json-viewer'); ?>
                            </button>
                            <button type="button" class="button" id="sjp-load-data">
                                <?php _e('Load Existing Data', 'sop-json-viewer'); ?>
                            </button>
                            <button type="button" class="button" id="sjp-export-data">
                                <?php _e('Export JSON', 'sop-json-viewer'); ?>
                            </button>
                        </div>
                    </form>
                </div>

                <div class="sjp-preview-section">
                    <h3><?php _e('Preview', 'sop-json-viewer'); ?></h3>
                    <div id="sjp-preview-container">
                        <p><?php _e('Preview akan muncul di sini setelah Anda memasukkan data JSON yang valid.', 'sop-json-viewer'); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php _e('SOP JSON Viewer - Settings', 'sop-json-viewer'); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('sjp_settings_group');
                do_settings_sections('sjp-settings');
                ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sjp_default_sop_id"><?php _e('Default SOP ID', 'sop-json-viewer'); ?></label>
                        </th>
                        <td>
                            <input type="text"
                                   id="sjp_default_sop_id"
                                   name="sjp_default_sop_id"
                                   value="<?php echo esc_attr(get_option('sjp_default_sop_id', 'default-sop')); ?>"
                                   class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="sjp_enable_validation"><?php _e('Enable Real-time Validation', 'sop-json-viewer'); ?></label>
                        </th>
                        <td>
                            <input type="checkbox"
                                   id="sjp_enable_validation"
                                   name="sjp_enable_validation"
                                   value="1"
                                   <?php checked(get_option('sjp_enable_validation', '1')); ?> />
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
```

### 3.3 Create JSON Validator Class
Buat file `includes/class-json-validator.php`:

```php
<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Validator {

    private $errors = array();
    private $warnings = array();

    public function validate_json($json_string) {
        $this->errors = array();
        $this->warnings = array();

        // Basic JSON syntax validation
        $data = json_decode($json_string);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->errors[] = 'JSON Syntax Error: ' . json_last_error_msg();
            return false;
        }

        // Structure validation
        return $this->validate_structure($data);
    }

    private function validate_structure($data) {
        // Must be an object
        if (!is_object($data)) {
            $this->errors[] = 'Root element must be an object';
            return false;
        }

        // Validate required fields
        if (!isset($data->title) || empty($data->title)) {
            $this->warnings[] = 'Title field is missing or empty';
        }

        if (!isset($data->sections) || !is_array($data->sections)) {
            $this->errors[] = 'Sections array is required';
            return false;
        }

        // Validate sections
        foreach ($data->sections as $index => $section) {
            if (!$this->validate_section($section, $index)) {
                return false;
            }
        }

        return true;
    }

    private function validate_section($section, $index) {
        if (!is_object($section)) {
            $this->errors[] = "Section {$index} must be an object";
            return false;
        }

        if (!isset($section->title) || empty($section->title)) {
            $this->errors[] = "Section {$index} missing or empty title";
            return false;
        }

        if (!isset($section->content) || empty($section->content)) {
            $this->warnings[] = "Section '{$section->title}' has no content";
        }

        // Validate subsections jika ada
        if (isset($section->subsections) && is_array($section->subsections)) {
            foreach ($section->subsections as $sub_index => $subsection) {
                if (!$this->validate_section($subsection, "{$index}.{$sub_index}")) {
                    return false;
                }
            }
        }

        return true;
    }

    public function get_errors() {
        return $this->errors;
    }

    public function get_warnings() {
        return $this->warnings;
    }

    public function has_errors() {
        return !empty($this->errors);
    }

    public function has_warnings() {
        return !empty($this->warnings);
    }
}
```

### 3.4 Create Admin JavaScript
Buat file `assets/js/admin-editor.js`:

```javascript
(function($) {
    'use strict';

    class SOPJSONEditor {
        constructor() {
            this.editor = null;
            this.validationTimer = null;
            this.currentSopId = 'default-sop';

            this.init();
        }

        init() {
            this.initCodeMirror();
            this.bindEvents();
            this.loadDefaultData();
        }

        initCodeMirror() {
            if (typeof wp.codeEditor !== 'undefined') {
                this.editor = wp.codeEditor.initialize('sjp-json-textarea', {
                    codemirror: {
                        lineNumbers: true,
                        mode: 'application/json',
                        theme: 'default',
                        indentUnit: 2,
                        matchBrackets: true,
                        autoCloseBrackets: true,
                        foldGutter: true,
                        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter']
                    }
                });

                this.editor.codemirror.on('change', (cm) => {
                    this.scheduleValidation();
                });
            }
        }

        bindEvents() {
            $('#sjp-sop-form').on('submit', (e) => {
                e.preventDefault();
                this.saveData();
            });

            $('#sop_id').on('change', () => {
                this.currentSopId = $('#sop_id').val();
                this.loadExistingData();
            });

            $('#sjp-load-data').on('click', () => {
                this.loadExistingData();
            });

            $('#sjp-export-data').on('click', () => {
                this.exportData();
            });
        }

        scheduleValidation() {
            clearTimeout(this.validationTimer);
            this.validationTimer = setTimeout(() => {
                this.validateJSON();
            }, 1000);
        }

        validateJSON() {
            const jsonData = this.editor.codemirror.getValue();
            const $status = $('#sjp-validation-status');

            if (!jsonData.trim()) {
                $status.html('<p class="notice notice-warning">JSON editor kosong</p>');
                return;
            }

            // Basic JSON validation
            try {
                const parsed = JSON.parse(jsonData);

                // Custom validation menggunakan AJAX
                $.ajax({
                    url: sjp_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'sjp_validate_json',
                        nonce: sjp_ajax.nonce,
                        json_data: jsonData
                    },
                    success: (response) => {
                        if (response.success) {
                            $status.html('<p class="notice notice-success">✅ JSON valid dan struktur benar</p>');
                            this.updatePreview(parsed);
                        } else {
                            $status.html(`<p class="notice notice-error">❌ ${response.data}</p>`);
                        }
                    },
                    error: () => {
                        $status.html('<p class="notice notice-error">❌ Error validating JSON</p>');
                    }
                });

            } catch (error) {
                $status.html(`<p class="notice notice-error">❌ JSON Syntax Error: ${error.message}</p>`);
            }
        }

        saveData() {
            const jsonData = this.editor.codemirror.getValue();
            const sopId = $('#sop_id').val();

            if (!jsonData.trim()) {
                alert('JSON data tidak boleh kosong');
                return;
            }

            $.ajax({
                url: sjp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sjp_save_sop_data',
                    nonce: sjp_ajax.nonce,
                    sop_id: sopId,
                    sop_data: jsonData
                },
                success: (response) => {
                    if (response.success) {
                        $('#sjp-validation-status').html('<p class="notice notice-success">✅ Data berhasil disimpan</p>');
                    } else {
                        $('#sjp-validation-status').html(`<p class="notice notice-error">❌ ${response.data}</p>`);
                    }
                },
                error: () => {
                    $('#sjp-validation-status').html('<p class="notice notice-error">❌ Error saving data</p>');
                }
            });
        }

        loadExistingData() {
            const sopId = $('#sop_id').val();

            $.ajax({
                url: sjp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'sjp_load_sop_data',
                    nonce: sjp_ajax.nonce,
                    sop_id: sopId
                },
                success: (response) => {
                    if (response.success && response.data) {
                        this.editor.codemirror.setValue(JSON.stringify(response.data, null, 2));
                        this.validateJSON();
                    } else {
                        // Load default template
                        this.loadDefaultData();
                    }
                },
                error: () => {
                    this.loadDefaultData();
                }
            });
        }

        loadDefaultData() {
            const defaultData = {
                "title": "Contoh SOP",
                "description": "Deskripsi singkat tentang SOP ini",
                "sections": [
                    {
                        "title": "Bagian 1: Pengenalan",
                        "content": "Konten pengenalan dengan **formatting** dan [link](https://example.com).",
                        "subsections": [
                            {
                                "title": "Sub-bagian 1.1",
                                "content": "Konten sub-bagian pertama"
                            }
                        ]
                    },
                    {
                        "title": "Bagian 2: Proses",
                        "content": "Konten proses utama"
                    }
                ]
            };

            this.editor.codemirror.setValue(JSON.stringify(defaultData, null, 2));
            this.validateJSON();
        }

        exportData() {
            const jsonData = this.editor.codemirror.getValue();
            const sopId = $('#sop_id').val();
            const blob = new Blob([jsonData], { type: 'application/json' });
            const url = URL.createObjectURL(blob);

            const a = document.createElement('a');
            a.href = url;
            a.download = `sop-${sopId}-${new Date().toISOString().split('T')[0]}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        updatePreview(data) {
            // Simple preview generation
            let preview = '<h3>Preview: ' + (data.title || 'Untitled') + '</h3>';
            preview += '<p>' + (data.description || '') + '</p>';

            if (data.sections && data.sections.length > 0) {
                preview += '<div class="preview-accordion">';
                data.sections.forEach((section, index) => {
                    preview += `
                        <div class="preview-section">
                            <button class="preview-header">${section.title}</button>
                            <div class="preview-content">${section.content || ''}</div>
                        </div>
                    `;
                });
                preview += '</div>';
            }

            $('#sjp-preview-container').html(preview);
        }
    }

    // Initialize when document is ready
    $(document).ready(() => {
        new SOPJSONEditor();
    });

})(jQuery);
```

### 3.5 Update Main Plugin Class
Integrasikan admin class ke main plugin:

```php
// Dalam class SOP_JSON_Viewer
private $admin;

public function init() {
    // ... existing code ...

    // Initialize admin interface
    $this->admin = new SOP_JSON_Viewer_Admin($this);

    // ... existing code ...
}
```

## File yang Dibuat/Dimodifikasi di Step Ini
- New: `includes/class-admin-interface.php` (admin interface class)
- New: `includes/class-json-validator.php` (JSON validation class)
- Modified: `includes/class-sop-json-viewer.php` (integrasi admin)
- Modified: `assets/js/admin-editor.js` (admin JavaScript functionality)

## Testing Checkpoint
1. Admin menu muncul di WordPress dashboard
2. JSON editor dapat diakses dan berfungsi
3. Real-time validation memberikan feedback yang jelas
4. Save/load data functionality berjalan dengan baik
5. Preview menampilkan struktur accordion dengan benar
6. Export functionality menghasilkan file JSON yang valid

## Next Step
Lanjut ke Step 04 untuk implementasi frontend JavaScript dan interactivity.