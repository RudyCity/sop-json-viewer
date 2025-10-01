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