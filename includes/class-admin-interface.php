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
                            <div class="sjp-setting-field">
                                <label for="sjp_animation_duration"><?php _e('Animation Duration (ms)', 'sop-json-viewer'); ?></label>
                                <input type="number"
                                       id="sjp_animation_duration"
                                       name="sjp_animation_duration"
                                       value="<?php echo esc_attr(get_option('sjp_animation_duration', '300')); ?>"
                                       class="sjp-setting-input"
                                       min="0"
                                       max="1000"
                                       step="50"
                                       placeholder="300" />
                                <p class="sjp-setting-description">
                                    <?php _e('Duration of accordion animations in milliseconds. Lower values = faster animations.', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="sjp-settings-section">
                        <h3><?php _e('Performance Settings', 'sop-json-viewer'); ?></h3>
                        <div class="sjp-settings-content">
                            <div class="sjp-setting-field">
                                <div class="sjp-checkbox-wrapper">
                                    <input type="checkbox"
                                           id="sjp_enable_caching"
                                           name="sjp_enable_caching"
                                           value="1"
                                           class="sjp-setting-checkbox"
                                           <?php checked(get_option('sjp_enable_caching', '1')); ?> />
                                    <label for="sjp_enable_caching" class="sjp-checkbox-label">
                                        <?php _e('Enable Caching', 'sop-json-viewer'); ?>
                                    </label>
                                </div>
                                <p class="sjp-setting-description">
                                    <?php _e('Cache SOP data to improve performance. Disable for development or when data changes frequently.', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                            <div class="sjp-setting-field">
                                <label for="sjp_cache_duration"><?php _e('Cache Duration (seconds)', 'sop-json-viewer'); ?></label>
                                <input type="number"
                                       id="sjp_cache_duration"
                                       name="sjp_cache_duration"
                                       value="<?php echo esc_attr(get_option('sjp_cache_duration', '3600')); ?>"
                                       class="sjp-setting-input"
                                       min="300"
                                       max="86400"
                                       step="300"
                                       placeholder="3600" />
                                <p class="sjp-setting-description">
                                    <?php _e('How long to cache SOP data (300-86400 seconds). 3600 = 1 hour, 86400 = 24 hours.', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="sjp-settings-section">
                        <h3><?php _e('Accessibility Settings', 'sop-json-viewer'); ?></h3>
                        <div class="sjp-settings-content">
                            <div class="sjp-setting-field">
                                <div class="sjp-checkbox-wrapper">
                                    <input type="checkbox"
                                           id="sjp_respect_reduced_motion"
                                           name="sjp_respect_reduced_motion"
                                           value="1"
                                           class="sjp-setting-checkbox"
                                           <?php checked(get_option('sjp_respect_reduced_motion', '1')); ?> />
                                    <label for="sjp_respect_reduced_motion" class="sjp-checkbox-label">
                                        <?php _e('Respect Reduced Motion', 'sop-json-viewer'); ?>
                                    </label>
                                </div>
                                <p class="sjp-setting-description">
                                    <?php _e('Disable animations for users who prefer reduced motion (accessibility feature).', 'sop-json-viewer'); ?>
                                </p>
                            </div>
                            <div class="sjp-setting-field">
                                <div class="sjp-checkbox-wrapper">
                                    <input type="checkbox"
                                           id="sjp_high_contrast_support"
                                           name="sjp_high_contrast_support"
                                           value="1"
                                           class="sjp-setting-checkbox"
                                           <?php checked(get_option('sjp_high_contrast_support', '1')); ?> />
                                    <label for="sjp_high_contrast_support" class="sjp-checkbox-label">
                                        <?php _e('High Contrast Support', 'sop-json-viewer'); ?>
                                    </label>
                                </div>
                                <p class="sjp-setting-description">
                                    <?php _e('Enhanced styling for high contrast display modes.', 'sop-json-viewer'); ?>
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