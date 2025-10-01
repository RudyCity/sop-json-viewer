<?php
/**
 * SOP JSON Viewer - Admin Settings Template
 *
 * Template untuk admin settings page dengan comprehensive options
 * dan user-friendly interface.
 */
?>

<div class="wrap sjp-admin-wrap">
    <div class="sjp-header">
        <h1 class="sjp-page-title">
            <?php _e('SOP JSON Viewer - Settings', 'sop-json-viewer'); ?>
        </h1>
        <p class="sjp-page-description">
            <?php _e('Configure general settings, performance options, and accessibility preferences untuk SOP JSON Viewer plugin.', 'sop-json-viewer'); ?>
        </p>
    </div>

    <form method="post" action="options.php" class="sjp-settings-form">
        <?php settings_fields('sjp_settings_group'); ?>

        <!-- General Settings Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-admin-generic"></span>
                    <?php _e('General Settings', 'sop-json-viewer'); ?>
                </h2>
                <p class="sjp-section-description">
                    <?php _e('Basic configuration untuk plugin behavior.', 'sop-json-viewer'); ?>
                </p>
            </div>

            <div class="sjp-settings-panel">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sjp_default_sop_id" class="sjp-setting-label">
                                <?php _e('Default SOP ID', 'sop-json-viewer'); ?>
                            </label>
                            <span class="sjp-help-tip" title="<?php _e('SOP ID yang akan digunakan ketika tidak dispesifikasikan dalam shortcode.', 'sop-json-viewer'); ?>">
                                <span class="dashicons dashicons-editor-help"></span>
                            </span>
                        </th>
                        <td>
                            <input type="text"
                                   id="sjp_default_sop_id"
                                   name="sjp_default_sop_id"
                                   value="<?php echo esc_attr(get_option('sjp_default_sop_id', 'default-sop')); ?>"
                                   class="regular-text sjp-input-field"
                                   placeholder="default-sop" />
                            <p class="description">
                                <?php _e('Enter the default SOP ID to use when none is specified in the shortcode.', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_animation_duration" class="sjp-setting-label">
                                <?php _e('Animation Duration (ms)', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                   id="sjp_animation_duration"
                                   name="sjp_animation_duration"
                                   value="<?php echo esc_attr(get_option('sjp_animation_duration', '300')); ?>"
                                   min="0"
                                   max="1000"
                                   step="50"
                                   class="small-text sjp-input-field" />
                            <p class="description">
                                <?php _e('Duration of accordion animations in milliseconds. Set to 0 to disable animations.', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_max_sections" class="sjp-setting-label">
                                <?php _e('Maximum Sections', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                   id="sjp_max_sections"
                                   name="sjp_max_sections"
                                   value="<?php echo esc_attr(get_option('sjp_max_sections', '50')); ?>"
                                   min="10"
                                   max="200"
                                   step="10"
                                   class="small-text sjp-input-field" />
                            <p class="description">
                                <?php _e('Maximum number of sections allowed in a single SOP.', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Performance Settings Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-performance"></span>
                    <?php _e('Performance Settings', 'sop-json-viewer'); ?>
                </h2>
                <p class="sjp-section-description">
                    <?php _e('Optimize plugin performance dan resource usage.', 'sop-json-viewer'); ?>
                </p>
            </div>

            <div class="sjp-settings-panel">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sjp_enable_caching" class="sjp-setting-label">
                                <?php _e('Enable Caching', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_enable_caching">
                                    <input type="checkbox"
                                           id="sjp_enable_caching"
                                           name="sjp_enable_caching"
                                           value="1"
                                           <?php checked(get_option('sjp_enable_caching', '1')); ?> />
                                    <?php _e('Enable caching untuk improve performance', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_cache_duration" class="sjp-setting-label">
                                <?php _e('Cache Duration (seconds)', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                   id="sjp_cache_duration"
                                   name="sjp_cache_duration"
                                   value="<?php echo esc_attr(get_option('sjp_cache_duration', '3600')); ?>"
                                   min="300"
                                   max="86400"
                                   step="300"
                                   class="small-text sjp-input-field" />
                            <p class="description">
                                <?php _e('How long to cache SOP data (300 seconds to 24 hours).', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_lazy_load" class="sjp-setting-label">
                                <?php _e('Lazy Load Nested Sections', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_lazy_load">
                                    <input type="checkbox"
                                           id="sjp_lazy_load"
                                           name="sjp_lazy_load"
                                           value="1"
                                           <?php checked(get_option('sjp_lazy_load', '1')); ?> />
                                    <?php _e('Load nested sections only when needed', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_minify_assets" class="sjp-setting-label">
                                <?php _e('Minify Assets', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_minify_assets">
                                    <input type="checkbox"
                                           id="sjp_minify_assets"
                                           name="sjp_minify_assets"
                                           value="1"
                                           <?php checked(get_option('sjp_minify_assets', '0')); ?> />
                                    <?php _e('Minify CSS and JavaScript files untuk reduce load time', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Accessibility Settings Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-universal-access"></span>
                    <?php _e('Accessibility Settings', 'sop-json-viewer'); ?>
                </h2>
                <p class="sjp-section-description">
                    <?php _e('Configure accessibility features dan compliance options.', 'sop-json-viewer'); ?>
                </p>
            </div>

            <div class="sjp-settings-panel">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sjp_respect_reduced_motion" class="sjp-setting-label">
                                <?php _e('Respect Reduced Motion', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_respect_reduced_motion">
                                    <input type="checkbox"
                                           id="sjp_respect_reduced_motion"
                                           name="sjp_respect_reduced_motion"
                                           value="1"
                                           <?php checked(get_option('sjp_respect_reduced_motion', '1')); ?> />
                                    <?php _e('Disable animations untuk users with reduced motion preferences', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_high_contrast_support" class="sjp-setting-label">
                                <?php _e('High Contrast Support', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_high_contrast_support">
                                    <input type="checkbox"
                                           id="sjp_high_contrast_support"
                                           name="sjp_high_contrast_support"
                                           value="1"
                                           <?php checked(get_option('sjp_high_contrast_support', '1')); ?> />
                                    <?php _e('Enhanced support untuk high contrast display modes', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_aria_labels" class="sjp-setting-label">
                                <?php _e('Enhanced ARIA Labels', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_aria_labels">
                                    <input type="checkbox"
                                           id="sjp_aria_labels"
                                           name="sjp_aria_labels"
                                           value="1"
                                           <?php checked(get_option('sjp_aria_labels', '1')); ?> />
                                    <?php _e('Add enhanced ARIA labels untuk better screen reader support', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_keyboard_navigation" class="sjp-setting-label">
                                <?php _e('Enhanced Keyboard Navigation', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_keyboard_navigation">
                                    <input type="checkbox"
                                           id="sjp_keyboard_navigation"
                                           name="sjp_keyboard_navigation"
                                           value="1"
                                           <?php checked(get_option('sjp_keyboard_navigation', '1')); ?> />
                                    <?php _e('Enable enhanced keyboard navigation features', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Advanced Settings Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Advanced Settings', 'sop-json-viewer'); ?>
                </h2>
                <p class="sjp-section-description">
                    <?php _e('Advanced options untuk developers dan power users.', 'sop-json-viewer'); ?>
                </p>
            </div>

            <div class="sjp-settings-panel">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sjp_debug_mode" class="sjp-setting-label">
                                <?php _e('Debug Mode', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <fieldset>
                                <label for="sjp_debug_mode">
                                    <input type="checkbox"
                                           id="sjp_debug_mode"
                                           name="sjp_debug_mode"
                                           value="1"
                                           <?php checked(get_option('sjp_debug_mode', '0')); ?> />
                                    <?php _e('Enable debug mode untuk development dan troubleshooting', 'sop-json-viewer'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_custom_css" class="sjp-setting-label">
                                <?php _e('Custom CSS', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <textarea id="sjp_custom_css"
                                      name="sjp_custom_css"
                                      rows="5"
                                      class="large-text code sjp-input-field"
                                      placeholder="/* Enter custom CSS here */"><?php echo esc_textarea(get_option('sjp_custom_css', '')); ?></textarea>
                            <p class="description">
                                <?php _e('Add custom CSS untuk override default styling. This will be loaded after the main stylesheet.', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="sjp_custom_js" class="sjp-setting-label">
                                <?php _e('Custom JavaScript', 'sop-json-viewer'); ?>
                            </label>
                        </th>
                        <td>
                            <textarea id="sjp_custom_js"
                                      name="sjp_custom_js"
                                      rows="5"
                                      class="large-text code sjp-input-field"
                                      placeholder="// Enter custom JavaScript here"><?php echo esc_textarea(get_option('sjp_custom_js', '')); ?></textarea>
                            <p class="description">
                                <?php _e('Add custom JavaScript untuk extend functionality. Use dengan caution.', 'sop-json-viewer'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Tools Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php _e('Tools & Utilities', 'sop-json-viewer'); ?>
                </h2>
                <p class="sjp-section-description">
                    <?php _e('Utility tools untuk manage dan troubleshoot SOP data.', 'sop-json-viewer'); ?>
                </p>
            </div>

            <div class="sjp-settings-panel">
                <div class="sjp-tools-grid">
                    <div class="sjp-tool-card">
                        <div class="sjp-tool-icon">
                            <span class="dashicons dashicons-database-export"></span>
                        </div>
                        <div class="sjp-tool-content">
                            <h3><?php _e('Export All Data', 'sop-json-viewer'); ?></h3>
                            <p><?php _e('Export all SOP data ke JSON files untuk backup.', 'sop-json-viewer'); ?></p>
                            <button type="button" class="button button-secondary sjp-export-all-btn">
                                <?php _e('Export All', 'sop-json-viewer'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="sjp-tool-card">
                        <div class="sjp-tool-icon">
                            <span class="dashicons dashicons-database-import"></span>
                        </div>
                        <div class="sjp-tool-content">
                            <h3><?php _e('Import Data', 'sop-json-viewer'); ?></h3>
                            <p><?php _e('Import SOP data dari JSON files.', 'sop-json-viewer'); ?></p>
                            <button type="button" class="button button-secondary sjp-import-btn">
                                <?php _e('Import', 'sop-json-viewer'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="sjp-tool-card">
                        <div class="sjp-tool-icon">
                            <span class="dashicons dashicons-search"></span>
                        </div>
                        <div class="sjp-tool-content">
                            <h3><?php _e('Validate All SOPs', 'sop-json-viewer'); ?></h3>
                            <p><?php _e('Check all SOP data untuk JSON errors.', 'sop-json-viewer'); ?></p>
                            <button type="button" class="button button-secondary sjp-validate-all-btn">
                                <?php _e('Validate All', 'sop-json-viewer'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="sjp-tool-card">
                        <div class="sjp-tool-icon">
                            <span class="dashicons dashicons-trash"></span>
                        </div>
                        <div class="sjp-tool-content">
                            <h3><?php _e('Clear Cache', 'sop-json-viewer'); ?></h3>
                            <p><?php _e('Clear all cached SOP data.', 'sop-json-viewer'); ?></p>
                            <button type="button" class="button button-secondary sjp-clear-cache-btn">
                                <?php _e('Clear Cache', 'sop-json-viewer'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Section -->
        <div class="sjp-settings-section">
            <div class="sjp-section-header">
                <h2 class="sjp-section-title">
                    <span class="dashicons dashicons-info"></span>
                    <?php _e('System Status', 'sop-json-viewer'); ?>
                </h2>
            </div>

            <div class="sjp-settings-panel">
                <div class="sjp-status-grid">
                    <div class="sjp-status-item">
                        <span class="sjp-status-label"><?php _e('Plugin Version:', 'sop-json-viewer'); ?></span>
                        <span class="sjp-status-value"><?php echo esc_html(SJP_VERSION); ?></span>
                    </div>

                    <div class="sjp-status-item">
                        <span class="sjp-status-label"><?php _e('WordPress Version:', 'sop-json-viewer'); ?></span>
                        <span class="sjp-status-value"><?php echo esc_html(get_bloginfo('version')); ?></span>
                    </div>

                    <div class="sjp-status-item">
                        <span class="sjp-status-label"><?php _e('PHP Version:', 'sop-json-viewer'); ?></span>
                        <span class="sjp-status-value"><?php echo esc_html(PHP_VERSION); ?></span>
                    </div>

                    <div class="sjp-status-item">
                        <span class="sjp-status-label"><?php _e('Total SOP Entries:', 'sop-json-viewer'); ?></span>
                        <span class="sjp-status-value">
                            <?php
                            $sop_ids = get_option('sjp_all_sop_ids', array());
                            echo count($sop_ids);
                            ?>
                        </span>
                    </div>

                    <div class="sjp-status-item">
                        <span class="sjp-status-label"><?php _e('Cache Status:', 'sop-json-viewer'); ?></span>
                        <span class="sjp-status-value">
                            <?php echo get_option('sjp_enable_caching', '1') ? __('Enabled', 'sop-json-viewer') : __('Disabled', 'sop-json-viewer'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php submit_button(__('Save All Settings', 'sop-json-viewer'), 'primary sjp-submit-btn'); ?>
    </form>
</div>

<style>
/* Admin Settings Styles */
.sjp-admin-wrap {
    margin: 20px 0;
}

.sjp-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #e1e1e1;
}

.sjp-page-title {
    margin: 0 0 10px 0;
    font-size: 2.5em;
    font-weight: 300;
    color: #23282d;
}

.sjp-page-description {
    margin: 0;
    font-size: 1.1em;
    color: #666;
    line-height: 1.5;
}

.sjp-settings-section {
    margin-bottom: 30px;
    background: #fff;
    border: 1px solid #e1e1e1;
    border-radius: 4px;
    overflow: hidden;
}

.sjp-section-header {
    background: #f8f9fa;
    padding: 20px;
    border-bottom: 1px solid #e1e1e1;
}

.sjp-section-title {
    margin: 0 0 5px 0;
    font-size: 1.3em;
    font-weight: 600;
    color: #23282d;
    display: flex;
    align-items: center;
}

.sjp-section-title .dashicons {
    margin-right: 8px;
    color: #007cba;
}

.sjp-section-description {
    margin: 0;
    color: #666;
    font-style: italic;
}

.sjp-settings-panel {
    padding: 20px;
}

.sjp-setting-label {
    font-weight: 600;
    color: #23282d;
    display: flex;
    align-items: center;
}

.sjp-help-tip {
    margin-left: 5px;
    color: #666;
    cursor: help;
}

.sjp-help-tip .dashicons {
    font-size: 1.2em;
}

.sjp-input-field {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.sjp-input-field:focus {
    border-color: #007cba;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 124, 186, 0.2);
}

.sjp-tools-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.sjp-tool-card {
    background: #f8f9fa;
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: flex-start;
}

.sjp-tool-icon {
    margin-right: 15px;
    flex-shrink: 0;
}

.sjp-tool-icon .dashicons {
    font-size: 2em;
    color: #007cba;
    width: 2em;
    height: 2em;
}

.sjp-tool-content h3 {
    margin: 0 0 10px 0;
    font-size: 1.1em;
    color: #23282d;
}

.sjp-tool-content p {
    margin: 0 0 15px 0;
    color: #666;
    font-size: 0.9em;
    line-height: 1.4;
}

.sjp-status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.sjp-status-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #007cba;
}

.sjp-status-label {
    font-weight: 600;
    color: #23282d;
}

.sjp-status-value {
    color: #007cba;
    font-weight: 500;
}

.sjp-submit-btn {
    font-size: 1.1em;
    padding: 12px 30px;
    background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sjp-submit-btn:hover {
    background: linear-gradient(135deg, #005a87 0%, #007cba 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 124, 186, 0.3);
}

/* Responsive Design */
@media (max-width: 768px) {
    .sjp-page-title {
        font-size: 2em;
    }

    .sjp-tools-grid {
        grid-template-columns: 1fr;
    }

    .sjp-status-grid {
        grid-template-columns: 1fr;
    }

    .sjp-tool-card {
        flex-direction: column;
        text-align: center;
    }

    .sjp-tool-icon {
        margin-right: 0;
        margin-bottom: 10px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Export All functionality
    $('.sjp-export-all-btn').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to export all SOP data?", "sop-json-viewer"); ?>')) {
            window.location.href = ajaxurl + '?action=sjp_export_all_data';
        }
    });

    // Clear Cache functionality
    $('.sjp-clear-cache-btn').on('click', function() {
        if (confirm('<?php _e("Are you sure you want to clear all cached data?", "sop-json-viewer"); ?>')) {
            $.post(ajaxurl, {
                action: 'sjp_clear_cache',
                nonce: '<?php echo wp_create_nonce("sjp_admin_nonce"); ?>'
            }, function(response) {
                if (response.success) {
                    alert('<?php _e("Cache cleared successfully", "sop-json-viewer"); ?>');
                } else {
                    alert('<?php _e("Error clearing cache", "sop-json-viewer"); ?>');
                }
            });
        }
    });

    // Validate All functionality
    $('.sjp-validate-all-btn').on('click', function() {
        $(this).prop('disabled', true).text('<?php _e("Validating...", "sop-json-viewer"); ?>');

        $.post(ajaxurl, {
            action: 'sjp_validate_all_sops',
            nonce: '<?php echo wp_create_nonce("sjp_admin_nonce"); ?>'
        }, function(response) {
            $('.sjp-validate-all-btn').prop('disabled', false).text('<?php _e("Validate All", "sop-json-viewer"); ?>');

            if (response.success) {
                const results = response.data;
                let message = `<?php _e("Validation Results:", "sop-json-viewer"); ?>\n\n`;
                message += `<?php _e("Valid SOPs:", "sop-json-viewer"); ?> ${results.valid}\n`;
                message += `<?php _e("Invalid SOPs:", "sop-json-viewer"); ?> ${results.invalid}\n`;

                if (results.errors.length > 0) {
                    message += `\n<?php _e("Errors found:", "sop-json-viewer"); ?>\n`;
                    results.errors.forEach(error => {
                        message += `- ${error}\n`;
                    });
                }

                alert(message);
            } else {
                alert('<?php _e("Error during validation", "sop-json-viewer"); ?>');
            }
        });
    });
});
</script>