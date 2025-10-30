<?php
if (!defined('ABSPATH')) {
    exit;
}

class SOP_JSON_Viewer_Admin {

    private $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }

    private function get_saved_sops() {
        global $wpdb;

        $saved_sops = array();

        // Get all options that start with 'sjp_sop_data_'
        $sop_options = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT option_name, option_value FROM {$wpdb->options}
                 WHERE option_name LIKE %s
                 ORDER BY option_name ASC",
                'sjp_sop_data_%'
            )
        );

        foreach ($sop_options as $option) {
            $sop_id = str_replace('sjp_sop_data_', '', $option->option_name);
            $sop_data = maybe_unserialize($option->option_value);

            if (is_array($sop_data) && !empty($sop_data)) {
                $saved_sops[$sop_id] = $sop_data;
            }
        }

        return $saved_sops;
    }

    public function admin_page_callback() {
        // Get all saved SOPs
        $saved_sops = $this->get_saved_sops();
        ?>
        <div class="sjp-admin-wrapper">
            <div class="sjp-admin-header">
                <div class="sjp-admin-title">
                    <h1><?php _e('SOP JSON Viewer', 'sop-json-viewer'); ?></h1>
                    <p><?php _e('Manage and preview your Standard Operating Procedures', 'sop-json-viewer'); ?></p>
                </div>
                <div class="sjp-admin-actions">
                    <button type="button" class="sjp-btn sjp-btn-help" id="sjp-help-toggle">
                        <span class="dashicons dashicons-help"></span>
                        <?php _e('Help', 'sop-json-viewer'); ?>
                    </button>
                </div>
            </div>

            <!-- Saved SOPs Table -->
            <?php if (!empty($saved_sops)): ?>
            <div class="sjp-saved-sops-section">
                <div class="sjp-panel-header">
                    <h2><?php _e('Saved SOPs', 'sop-json-viewer'); ?></h2>
                    <span class="sjp-sop-count"><?php printf(_n('%d SOP saved', '%d SOPs saved', count($saved_sops), 'sop-json-viewer'), count($saved_sops)); ?></span>
                </div>
                <div class="sjp-saved-sops-table-wrapper">
                    <table class="sjp-saved-sops-table">
                        <thead>
                            <tr>
                                <th><?php _e('SOP ID', 'sop-json-viewer'); ?></th>
                                <th><?php _e('Title', 'sop-json-viewer'); ?></th>
                                <th><?php _e('Sections', 'sop-json-viewer'); ?></th>
                                <th><?php _e('Last Modified', 'sop-json-viewer'); ?></th>
                                <th><?php _e('Actions', 'sop-json-viewer'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($saved_sops as $sop_id => $sop_data): ?>
                            <tr>
                                <td class="sjp-sop-id">
                                    <code><?php echo esc_html($sop_id); ?></code>
                                </td>
                                <td class="sjp-sop-title">
                                    <?php echo esc_html($sop_data['title'] ?? __('Untitled SOP', 'sop-json-viewer')); ?>
                                </td>
                                <td class="sjp-sop-sections">
                                    <?php
                                    $section_count = isset($sop_data['sections']) ? count($sop_data['sections']) : 0;
                                    printf(_n('%d section', '%d sections', $section_count, 'sop-json-viewer'), $section_count);
                                    ?>
                                </td>
                                <td class="sjp-sop-modified">
                                    <?php
                                    $modified_time = get_option('sjp_sop_modified_' . $sop_id);
                                    if ($modified_time) {
                                        echo esc_html(human_time_diff(strtotime($modified_time), current_time('timestamp')) . ' ' . __('ago', 'sop-json-viewer'));
                                    } else {
                                        echo __('Unknown', 'sop-json-viewer');
                                    }
                                    ?>
                                </td>
                                <td class="sjp-sop-actions">
                                    <button type="button" class="sjp-btn sjp-btn-sm sjp-btn-secondary sjp-load-sop"
                                            data-sop-id="<?php echo esc_attr($sop_id); ?>"
                                            title="<?php _e('Load this SOP for editing', 'sop-json-viewer'); ?>">
                                        <span class="dashicons dashicons-edit"></span>
                                        <?php _e('Edit', 'sop-json-viewer'); ?>
                                    </button>
                                    <button type="button" class="sjp-btn sjp-btn-sm sjp-btn-danger sjp-delete-sop"
                                            data-sop-id="<?php echo esc_attr($sop_id); ?>"
                                            title="<?php _e('Delete this SOP', 'sop-json-viewer'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                        <?php _e('Delete', 'sop-json-viewer'); ?>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="sjp-admin-layout">
                <!-- Main Editor Section -->
                <div class="sjp-editor-panel">
                    <div class="sjp-panel-header">
                        <h2><?php _e('SOP Editor', 'sop-json-viewer'); ?></h2>
                        <div class="sjp-panel-tabs">
                            <button class="sjp-tab active" data-tab="editor"><?php _e('JSON Editor', 'sop-json-viewer'); ?></button>
                            <button class="sjp-tab" data-tab="template"><?php _e('Templates', 'sop-json-viewer'); ?></button>
                        </div>
                    </div>

                    <div class="sjp-panel-content">
                        <!-- Editor Tab -->
                        <div class="sjp-tab-content active" id="editor-tab">
                            <form id="sjp-sop-form" method="post" action="">
                                <?php wp_nonce_field('sjp_admin_nonce', 'sjp_admin_nonce'); ?>

                                <div class="sjp-form-grid">
                                    <div class="sjp-form-group">
                                        <label for="sop_id" class="sjp-label">
                                            <?php _e('SOP ID', 'sop-json-viewer'); ?>
                                            <span class="sjp-required">*</span>
                                        </label>
                                        <div class="sjp-input-group">
                                            <input type="text"
                                                   id="sop_id"
                                                   name="sop_id"
                                                   value="default-sop"
                                                   class="sjp-input"
                                                   required />
                                            <span class="sjp-input-icon dashicons dashicons-text-page"></span>
                                        </div>
                                        <p class="sjp-help-text">
                                            <?php _e('Unique identifier for this SOP (use lowercase, hyphen-separated)', 'sop-json-viewer'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="sjp-editor-container">
                                    <div class="sjp-editor-header">
                                        <label class="sjp-label"><?php _e('JSON Data', 'sop-json-viewer'); ?></label>
                                        <div class="sjp-editor-tools">
                                            <button type="button" class="sjp-tool-btn" id="sjp-format-json" title="Format JSON">
                                                <span class="dashicons dashicons-editor-code"></span>
                                            </button>
                                            <button type="button" class="sjp-tool-btn" id="sjp-clear-editor" title="Clear Editor">
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="sjp-json-editor-wrapper">
                                        <div id="sjp-json-editor"></div>
                                        <textarea id="sjp-json-textarea"
                                                  name="sop_json_data"
                                                  class="sjp-json-textarea"
                                                  style="display: none;"></textarea>
                                    </div>
                                </div>

                                <div class="sjp-validation-status" id="sjp-validation-status"></div>

                                <div class="sjp-form-actions">
                                    <button type="submit" class="sjp-btn sjp-btn-primary">
                                        <span class="dashicons dashicons-saved"></span>
                                        <?php _e('Save SOP Data', 'sop-json-viewer'); ?>
                                    </button>
                                    <button type="button" class="sjp-btn sjp-btn-secondary" id="sjp-load-data">
                                        <span class="dashicons dashicons-upload"></span>
                                        <?php _e('Load Existing', 'sop-json-viewer'); ?>
                                    </button>
                                    <button type="button" class="sjp-btn sjp-btn-secondary" id="sjp-export-data">
                                        <span class="dashicons dashicons-download"></span>
                                        <?php _e('Export JSON', 'sop-json-viewer'); ?>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Templates Tab -->
                        <div class="sjp-tab-content" id="template-tab">
                            <div class="sjp-template-grid">
                                <div class="sjp-template-card" data-template="basic">
                                    <div class="sjp-template-icon">📄</div>
                                    <h3><?php _e('Basic SOP', 'sop-json-viewer'); ?></h3>
                                    <p><?php _e('Simple SOP structure with basic sections', 'sop-json-viewer'); ?></p>
                                    <button class="sjp-btn sjp-btn-sm"><?php _e('Use Template', 'sop-json-viewer'); ?></button>
                                </div>
                                <div class="sjp-template-card" data-template="advanced">
                                    <div class="sjp-template-icon">📊</div>
                                    <h3><?php _e('Advanced SOP', 'sop-json-viewer'); ?></h3>
                                    <p><?php _e('Complex SOP with nested subsections', 'sop-json-viewer'); ?></p>
                                    <button class="sjp-btn sjp-btn-sm"><?php _e('Use Template', 'sop-json-viewer'); ?></button>
                                </div>
                                <div class="sjp-template-card" data-template="safety">
                                    <div class="sjp-template-icon">🛡️</div>
                                    <h3><?php _e('Safety Protocol', 'sop-json-viewer'); ?></h3>
                                    <p><?php _e('Safety-focused SOP with emergency procedures', 'sop-json-viewer'); ?></p>
                                    <button class="sjp-btn sjp-btn-sm"><?php _e('Use Template', 'sop-json-viewer'); ?></button>
                                </div>
                                <div class="sjp-template-card" data-template="links">
                                    <div class="sjp-template-icon">🔗</div>
                                    <h3><?php _e('Link Collection', 'sop-json-viewer'); ?></h3>
                                    <p><?php _e('SOP with interactive link lists and resource collections', 'sop-json-viewer'); ?></p>
                                    <button class="sjp-btn sjp-btn-sm"><?php _e('Use Template', 'sop-json-viewer'); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Panel -->
                <div class="sjp-preview-panel">
                    <div class="sjp-panel-header">
                        <h2><?php _e('Preview', 'sop-json-viewer'); ?></h2>
                        <div class="sjp-preview-tools">
                            <button type="button" class="sjp-tool-btn" id="sjp-preview-refresh" title="Refresh Preview">
                                <span class="dashicons dashicons-update"></span>
                            </button>
                            <button type="button" class="sjp-tool-btn" id="sjp-preview-fullscreen" title="Fullscreen">
                                <span class="dashicons dashicons-fullscreen-alt"></span>
                            </button>
                        </div>
                    </div>
                    <div class="sjp-preview-content">
                        <div id="sjp-preview-container">
                            <div class="sjp-preview-placeholder">
                                <div class="sjp-preview-icon">👁️</div>
                                <h3><?php _e('Preview', 'sop-json-viewer'); ?></h3>
                                <p><?php _e('Preview will appear here after you enter valid JSON data.', 'sop-json-viewer'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Modal -->
            <div class="sjp-modal" id="sjp-help-modal">
                <div class="sjp-modal-content">
                    <div class="sjp-modal-header">
                        <h3><?php _e('How to Use SOP JSON Viewer', 'sop-json-viewer'); ?></h3>
                        <button type="button" class="sjp-modal-close" id="sjp-help-close">
                            <span class="dashicons dashicons-no-alt"></span>
                        </button>
                    </div>
                    <div class="sjp-modal-body">
                        <div class="sjp-help-section">
                            <h4><?php _e('Getting Started', 'sop-json-viewer'); ?></h4>
                            <ol>
                                <li><?php _e('Enter a unique SOP ID', 'sop-json-viewer'); ?></li>
                                <li><?php _e('Edit JSON data in the editor or use a template', 'sop-json-viewer'); ?></li>
                                <li><?php _e('Preview your changes in real-time', 'sop-json-viewer'); ?></li>
                                <li><?php _e('Save your SOP data', 'sop-json-viewer'); ?></li>
                            </ol>
                        </div>
                        <div class="sjp-help-section">
                            <h4><?php _e('JSON Structure', 'sop-json-viewer'); ?></h4>
                            <pre><code>{
  "title": "SOP Title",
  "description": "SOP Description",
  "sections": [
    {
      "title": "Section Title",
      "content": "Section content with HTML support",
      "subsections": [
        {
          "title": "Subsection Title",
          "content": "Subsection content"
        }
      ]
    }
  ]
}</code></pre>
                        </div>
                        <div class="sjp-help-section">
                            <h4><?php _e('Link Content Feature', 'sop-json-viewer'); ?></h4>
                            <p><?php _e('You can now create sections with link lists instead of regular HTML content:', 'sop-json-viewer'); ?></p>
                            <pre><code>{
  "title": "Resources",
  "content": [
    {
      "type": "link",
      "title": "Documentation",
      "url": "https://example.com/docs",
      "target": "_blank"
    }
  ]
}</code></pre>
                            <p><?php _e('Link content properties:', 'sop-json-viewer'); ?></p>
                            <ul>
                                <li><code>type</code>: Must be "link"</li>
                                <li><code>title</code>: Link text (required)</li>
                                <li><code>url</code>: Link URL (required)</li>
                                <li><code>target</code>: "_blank" for external links (optional)</li>
                            </ul>
                            <p><?php _e('Sorting options for link lists:', 'sop-json-viewer'); ?></p>
                            <ul>
                                <li><code>sort</code>: Enable/disable sorting (default: true)</li>
                                <li><code>sort_by</code>: Field to sort by (default: "title")</li>
                                <li><code>sort_order</code>: Sort order "asc" or "desc" (default: "asc")</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Credits Section -->
            <div class="sjp-admin-credits">
                <div class="sjp-credits-content">
                    <p class="sjp-credits-text">
                        <?php _e('Plugin developed by:', 'sop-json-viewer'); ?>
                        <a href="mailto:hrudy715@gmail.com" class="sjp-credits-link">Rudy Hermawan (hrudy715@gmail.com)</a>
                    </p>
                </div>
            </div>
        </div>
        <?php
    }

    public function settings_page_callback() {
        ?>
        <div class="wrap">
            <h1><?php _e('SOP JSON Viewer - Settings', 'sop-json-viewer'); ?></h1>
            <p class="sjp-settings-intro"><?php _e('Configure your SOP JSON Viewer plugin settings below. Changes are automatically saved.', 'sop-json-viewer'); ?></p>

            <form method="post" action="options.php" class="sjp-settings-form">
                <?php settings_fields('sjp_settings_group'); ?>

                <div class="sjp-settings-container">
                    <div class="sjp-settings-section">
                        <h3><?php _e('General Settings', 'sop-json-viewer'); ?></h3>
                        <div class="sjp-settings-content">
                            <div class="sjp-setting-field">
                                <label for="sjp_default_sop_id"><?php _e('Default SOP ID', 'sop-json-viewer'); ?></label>
                                <input type="text"
                                       id="sjp_default_sop_id"
                                       name="sjp_default_sop_id"
                                       value="<?php echo esc_attr(get_option('sjp_default_sop_id', 'default-sop')); ?>"
                                       class="sjp-setting-input regular-text"
                                       placeholder="default-sop" />
                                <p class="sjp-setting-description">
                                    <?php _e('Default SOP ID to use when none specified in shortcode. Use lowercase letters and hyphens only.', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                            <div class="sjp-setting-field">
                                <label for="sjp_default_section_visibility"><?php _e('Default Section Visibility', 'sop-json-viewer'); ?></label>
                                <select id="sjp_default_section_visibility"
                                        name="sjp_default_section_visibility"
                                        class="sjp-setting-input">
                                    <option value="hidden" <?php selected(get_option('sjp_default_section_visibility', 'hidden'), 'hidden'); ?>>
                                        <?php _e('Hidden (Collapsed)', 'sop-json-viewer'); ?>
                                    </option>
                                    <option value="shown" <?php selected(get_option('sjp_default_section_visibility', 'hidden'), 'shown'); ?>>
                                        <?php _e('Shown (Expanded)', 'sop-json-viewer'); ?>
                                    </option>
                                </select>
                                <p class="sjp-setting-description">
                                    <?php _e('Default visibility state for accordion sections when first loaded. "Shown" will expand the first section by default.', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sjp-settings-actions">
                    <?php submit_button(__('Save All Settings', 'sop-json-viewer'), 'primary sjp-btn sjp-btn-large', 'submit', true); ?>
                    <button type="button" class="sjp-btn sjp-btn-secondary" onclick="history.back()">
                        <?php _e('Cancel', 'sop-json-viewer'); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
    }
}