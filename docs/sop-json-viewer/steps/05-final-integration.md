# 05 - Final Integration

## Tujuan Step
Finalisasi implementasi, comprehensive testing, dan persiapan deployment plugin.

## Langkah Detail

### 5.1 Complete Database Integration
Update main plugin class dengan proper database operations:

```php
// Dalam class SOP_JSON_Viewer
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
        wp_send_json_error('Security check failed');
    }

    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    $sop_id = sanitize_text_field($_POST['sop_id']);
    $sop_data = wp_unslash($_POST['sop_data']);

    // Validate JSON
    $decoded_data = json_decode($sop_data, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON format: ' . json_last_error_msg());
    }

    // Sanitize data sebelum save
    $sanitized_data = $this->sanitize_sop_data($decoded_data);

    // Save ke database
    $result = update_option('sjp_sop_data_' . $sop_id, $sanitized_data);

    if ($result) {
        // Clear cache jika ada
        wp_cache_delete('sjp_sop_data_' . $sop_id);

        wp_send_json_success('SOP data saved successfully');
    } else {
        wp_send_json_error('Failed to save SOP data');
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
```

### 5.2 Add Import/Export Functionality
Tambahkan comprehensive import/export features:

```php
public function ajax_export_sop_data() {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }

    $sop_id = sanitize_text_field($_GET['sop_id']);
    $sop_data = get_option('sjp_sop_data_' . $sop_id, array());

    if (empty($sop_data)) {
        wp_die('No data found to export');
    }

    $filename = 'sop-' . $sop_id . '-' . date('Y-m-d') . '.json';

    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen(json_encode($sop_data, JSON_PRETTY_PRINT)));

    echo json_encode($sop_data, JSON_PRETTY_PRINT);
    exit;
}

public function ajax_import_sop_data() {
    if (!wp_verify_nonce($_POST['nonce'], 'sjp_nonce')) {
        wp_send_json_error('Security check failed');
    }

    if (!current_user_can('manage_options')) {
        wp_send_json_error('Insufficient permissions');
    }

    if (empty($_FILES['import_file']['tmp_name'])) {
        wp_send_json_error('No file uploaded');
    }

    $file_content = file_get_contents($_FILES['import_file']['tmp_name']);

    if (!$file_content) {
        wp_send_json_error('Could not read file');
    }

    $data = json_decode($file_content, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        wp_send_json_error('Invalid JSON file: ' . json_last_error_msg());
    }

    $sanitized_data = $this->sanitize_sop_data($data);
    $sop_id = sanitize_text_field($_POST['sop_id']);

    $result = update_option('sjp_sop_data_' . $sop_id, $sanitized_data);

    if ($result) {
        wp_send_json_success('Data imported successfully');
    } else {
        wp_send_json_error('Failed to import data');
    }
}
```

### 5.3 Add Plugin Settings dan Options
Buat comprehensive settings page:

```php
public function settings_page_callback() {
    ?>
    <div class="wrap">
        <h1><?php _e('SOP JSON Viewer - Settings', 'sop-json-viewer'); ?></h1>

        <form method="post" action="options.php">
            <?php settings_fields('sjp_settings_group'); ?>

            <h2><?php _e('General Settings', 'sop-json-viewer'); ?></h2>
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
                        <p class="description">
                            <?php _e('Default SOP ID to use when none specified in shortcode', 'sop-json-viewer'); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sjp_animation_duration"><?php _e('Animation Duration (ms)', 'sop-json-viewer'); ?></label>
                    </th>
                    <td>
                        <input type="number"
                               id="sjp_animation_duration"
                               name="sjp_animation_duration"
                               value="<?php echo esc_attr(get_option('sjp_animation_duration', '300')); ?>"
                               min="0"
                               max="1000"
                               step="50" />
                    </td>
                </tr>
            </table>

            <h2><?php _e('Performance Settings', 'sop-json-viewer'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sjp_enable_caching"><?php _e('Enable Caching', 'sop-json-viewer'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox"
                               id="sjp_enable_caching"
                               name="sjp_enable_caching"
                               value="1"
                               <?php checked(get_option('sjp_enable_caching', '1')); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sjp_cache_duration"><?php _e('Cache Duration (seconds)', 'sop-json-viewer'); ?></label>
                    </th>
                    <td>
                        <input type="number"
                               id="sjp_cache_duration"
                               name="sjp_cache_duration"
                               value="<?php echo esc_attr(get_option('sjp_cache_duration', '3600')); ?>"
                               min="300"
                               max="86400" />
                    </td>
                </tr>
            </table>

            <h2><?php _e('Accessibility Settings', 'sop-json-viewer'); ?></h2>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="sjp_respect_reduced_motion"><?php _e('Respect Reduced Motion', 'sop-json-viewer'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox"
                               id="sjp_respect_reduced_motion"
                               name="sjp_respect_reduced_motion"
                               value="1"
                               <?php checked(get_option('sjp_respect_reduced_motion', '1')); ?> />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="sjp_high_contrast_support"><?php _e('High Contrast Support', 'sop-json-viewer'); ?></label>
                    </th>
                    <td>
                        <input type="checkbox"
                               id="sjp_high_contrast_support"
                               name="sjp_high_contrast_support"
                               value="1"
                               <?php checked(get_option('sjp_high_contrast_support', '1')); ?> />
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
```

### 5.4 Create Installation dan Usage Documentation
Buat comprehensive README untuk plugin:

```markdown
# SOP JSON Viewer

Plugin WordPress untuk menampilkan konten SOP dalam format accordion interaktif menggunakan data JSON dengan editor validasi real-time.

## Features

- 🎯 Shortcode untuk menampilkan SOP dalam format accordion
- 🎨 Admin interface untuk edit JSON dengan validasi real-time
- 📱 Responsive design untuk semua device
- ♿ Full accessibility support (WCAG 2.1 AA)
- 🚀 Performance optimized dengan caching
- 📥 Import/Export functionality untuk backup
- 🎭 Support nested accordion untuk sub-procedures
- 🔗 File link integration dalam konten

## Installation

1. Upload folder `sop-json-viewer` ke `/wp-content/plugins/`
2. Aktivasi plugin melalui menu 'Plugins' di WordPress
3. Akses menu "SOP Viewer" di admin dashboard
4. Configure settings sesuai kebutuhan

## Usage

### Basic Shortcode
```php
[sop-accordion id="my-sop"]
```

### Shortcode dengan Custom Class
```php
[sop-accordion id="my-sop" class="custom-style"]
```

### Menggunakan di PHP Template
```php
<?php echo do_shortcode('[sop-accordion id="my-sop"]'); ?>
```

## Admin Interface

1. Masuk ke menu "SOP Viewer" > "Manage SOP"
2. Masukkan SOP ID (contoh: "prosedur-kerja")
3. Edit JSON data menggunakan editor yang disediakan
4. Preview hasil sebelum save
5. Save data untuk publikasi

## JSON Structure

```json
{
  "title": "Nama SOP",
  "description": "Deskripsi singkat",
  "sections": [
    {
      "title": "Bagian 1",
      "content": "Konten dengan **Markdown** support",
      "subsections": [
        {
          "title": "Sub-bagian 1.1",
          "content": "Konten sub-bagian"
        }
      ]
    }
  ]
}
```

## Settings

### General Settings
- **Default SOP ID**: SOP ID default ketika tidak dispesifikasikan
- **Animation Duration**: Durasi animasi accordion (ms)

### Performance Settings
- **Enable Caching**: Aktifkan caching untuk performance
- **Cache Duration**: Durasi cache dalam detik

### Accessibility Settings
- **Respect Reduced Motion**: Matikan animasi untuk pengguna dengan preferensi reduced motion
- **High Contrast Support**: Enhanced support untuk high contrast mode

## Troubleshooting

### Common Issues

1. **Accordion tidak muncul**
   - Pastikan SOP ID sudah benar
   - Check apakah data JSON sudah tersimpan
   - Lihat console browser untuk error messages

2. **JSON validation error**
   - Pastikan struktur JSON sesuai format
   - Check syntax error (comma, brackets)
   - Validasi menggunakan JSON validator external

3. **Performance issues**
   - Aktifkan caching di settings
   - Kurangi jumlah sections jika terlalu banyak
   - Check plugin conflicts

## Changelog

### Version 1.0.0
- Initial release
- Basic accordion functionality
- Admin JSON editor
- Import/Export features
- Full accessibility support

## Support

Untuk support dan pertanyaan, silakan hubungi:
- Email: hrudy715@gmail.com
- Documentation: [Link to documentation]

## License

This plugin is licensed under the GPL v2 or later.

> This plugin is not affiliated with or endorsed by WordPress.org
```

### 5.5 Create Deployment Script
Buat script untuk deployment dan setup:

```bash
#!/bin/bash
# SOP JSON Viewer - Deployment Script

PLUGIN_NAME="sop-json-viewer"
PLUGIN_DIR="/path/to/wp-content/plugins/$PLUGIN_NAME"
BACKUP_DIR="/path/to/backups"

# Create backup
echo "Creating backup..."
mkdir -p "$BACKUP_DIR"
tar -czf "$BACKUP_DIR/$PLUGIN_NAME-$(date +%Y%m%d-%H%M%S).tar.gz" "$PLUGIN_DIR"

# Deploy new version
echo "Deploying new version..."
# Copy new files (adjust path as needed)
# cp -r /path/to/new/version/* "$PLUGIN_DIR"

# Set proper permissions
echo "Setting permissions..."
find "$PLUGIN_DIR" -type f -name "*.php" -exec chmod 644 {} \;
find "$PLUGIN_DIR" -type f -name "*.css" -exec chmod 644 {} \;
find "$PLUGIN_DIR" -type f -name "*.js" -exec chmod 644 {} \;

# Clear WordPress cache jika ada
echo "Clearing caches..."
# wp cache flush

echo "Deployment completed successfully!"
```

### 5.6 Final Testing Checklist
Buat comprehensive testing checklist:

```markdown
# Final Testing Checklist

## Pre-deployment Testing

### Functionality Testing
- [ ] Shortcode `[sop-accordion]` berfungsi dengan benar
- [ ] Admin interface dapat diakses dan berfungsi
- [ ] JSON validation memberikan feedback yang tepat
- [ ] Import/Export functionality berjalan dengan baik
- [ ] Nested accordion berfungsi untuk sub-sections
- [ ] Error handling menampilkan pesan yang helpful

### Performance Testing
- [ ] Page load time tidak meningkat >200ms
- [ ] Caching berfungsi dengan baik
- [ ] Large JSON data tidak menyebabkan performance issues
- [ ] Animations smooth di berbagai device

### Compatibility Testing
- [ ] Compatible dengan WordPress 5.0+
- [ ] Berfungsi dengan tema default (Twenty Twenty-One)
- [ ] Compatible dengan popular plugins (ACF, Yoast, etc.)
- [ ] Cross-browser compatibility (Chrome, Firefox, Safari, Edge)

### Security Testing
- [ ] Input sanitization berfungsi dengan baik
- [ ] No XSS vulnerabilities
- [ ] User capability checks implemented
- [ ] Nonce verification untuk AJAX requests

### Accessibility Testing
- [ ] Keyboard navigation berfungsi penuh
- [ ] Screen reader compatibility
- [ ] ARIA labels dan roles yang proper
- [ ] Color contrast ratio memenuhi WCAG 2.1 AA
- [ ] Support untuk reduced motion preferences

### Mobile Testing
- [ ] Responsive design di semua screen sizes
- [ ] Touch interactions berfungsi dengan baik
- [ ] Accordion mudah digunakan di mobile device

## Post-deployment Testing

### Production Testing
- [ ] Plugin aktif tanpa error di production
- [ ] All functionality berjalan seperti di development
- [ ] Performance metrics sesuai target
- [ ] No console errors atau PHP warnings

### User Acceptance Testing
- [ ] Admin users dapat menggunakan interface dengan mudah
- [ ] End users dapat mengakses SOP tanpa kesulitan
- [ ] All user stories terpenuhi

### Monitoring Setup
- [ ] Error logging configured
- [ ] Performance monitoring active
- [ ] User feedback mechanism available

## Rollback Plan

Jika ditemukan critical issues:
1. Deactivate plugin di production
2. Restore dari backup terbaru
3. Analyze root cause
4. Fix issues di development environment
5. Re-deploy setelah testing menyeluruh

## Success Metrics

- ✅ Zero critical bugs di production
- ✅ Performance impact <200ms
- ✅ 100% accessibility compliance
- ✅ Positive user feedback
- ✅ All features berfungsi sesuai requirements
```

## File yang Dibuat/Dimodifikasi di Step Ini
- Modified: `includes/class-sop-json-viewer.php` (tambah database integration)
- Modified: `includes/class-admin-interface.php` (tambah import/export)
- New: `README.md` (comprehensive documentation)
- New: `scripts/deploy.sh` (deployment script)
- New: `FINAL-TESTING-CHECKLIST.md` (testing checklist)

## Testing Checkpoint
1. End-to-end functionality testing passed
2. Database operations berjalan dengan baik
3. Import/Export functionality tested dengan file sample
4. Settings page berfungsi dengan benar
5. Documentation lengkap dan akurat
6. Deployment script tested di environment yang aman

## Deployment Readiness
✅ Plugin siap untuk deployment ke production setelah semua testing passed dan documentation selesai.

## Next Actions (Post-Plan)
1. Execute implementation berdasarkan step-by-step di atas
2. Lakukan comprehensive testing sesuai test-plan.md
3. Deploy ke production environment
4. Monitor performance dan user feedback
5. Plan untuk future enhancements berdasarkan user input