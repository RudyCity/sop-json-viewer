# Standards

## WordPress Coding Standards
- Ikuti [WordPress Coding Standards](https://codex.wordpress.org/WordPress_Coding_Standards)
- Gunakan [WordPress PHP Coding Standards](https://make.wordpress.org/core/handbook/best-practices/coding-standards/php/)
- File naming: lowercase, hyphen-separated (contoh: `sop-json-viewer.php`)
- Function naming: prefix dengan slug project (`sjp_` untuk SOP JSON Viewer)

## Struktur Plugin
```
sop-json-viewer/
├── sop-json-viewer.php (file utama plugin)
├── includes/
│   ├── class-sop-json-viewer.php (main class)
│   ├── class-admin-interface.php (admin dashboard)
│   └── class-json-validator.php (validation logic)
├── assets/
│   ├── css/
│   │   └── sop-accordion.css (frontend styles)
│   ├── js/
│   │   ├── sop-accordion.js (frontend script)
│   │   └── admin-editor.js (admin interface)
│   └── images/ (icon dan assets visual)
├── templates/
│   └── admin-settings.php (template admin interface)
└── README.md (dokumentasi plugin)
```

## JSON Structure Standard
```json
{
  "title": "Nama SOP",
  "description": "Deskripsi singkat",
  "sections": [
    {
      "title": "Bagian 1",
      "content": "Konten bagian 1",
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

## Security Standards
- Sanitize semua input menggunakan `sanitize_text_field()`
- Escape output menggunakan `esc_html()`, `esc_attr()`, `wp_kses_post()`
- Validasi nonce pada semua form submission
- Gunakan WordPress Settings API untuk options
- Cek user capability sebelum eksekusi admin actions

## Performance Standards
- Minimize database queries dengan caching
- Optimize assets (minify CSS/JS)
- Gunakan WordPress enqueue system untuk assets
- Lazy load untuk konten accordion jika diperlukan
- Monitor page load impact (target: <200ms increase)

## Accessibility Standards
- Semantic HTML structure untuk accordion
- ARIA labels dan roles yang proper
- Keyboard navigation support (Tab, Enter, Space, Arrow keys)
- Screen reader compatibility
- Color contrast ratio minimal 4.5:1

## Browser Compatibility
- Support browser: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
- Graceful degradation untuk browser lama
- Mobile responsive design (320px - 1920px)

## Testing Standards
- Unit test untuk setiap class method
- Integration test untuk shortcode functionality
- User acceptance test untuk admin interface
- Cross-browser compatibility testing
- Performance testing dengan tools seperti GTmetrix

## Documentation Standards
- PHPDoc untuk semua functions dan classes
- Inline comments untuk logic kompleks
- README.md dengan installation dan usage instructions
- Changelog untuk setiap version update
- User guide untuk admin interface