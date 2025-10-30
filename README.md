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
- 👁️ Default visibility control (shown/hidden) untuk sections

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

### Shortcode dengan Default Visibility
```php
[sop-accordion id="my-sop" default_visibility="shown"]
[sop-accordion id="my-sop" default_visibility="hidden"]
```

Parameter `default_visibility` mengatur apakah section pertama akan ditampilkan terbuka ("shown") atau tertutup ("hidden") saat pertama kali dimuat.

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
      "expanded": true,
      "subsections": [
        {
          "title": "Sub-bagian 1.1",
          "content": "Konten sub-bagian",
          "expanded": false
        }
      ]
    },
    {
      "title": "Bagian 2",
      "content": "Konten bagian kedua",
      "expanded": false
    }
  ]
}
```

### Section Properties

- `title` (string, required): Judul section
- `content` (string/array, required): Konten section atau array of content items
- `expanded` (boolean, optional): Mengatur apakah section terbuka secara default
  - `true`: Section akan terbuka saat pertama dimuat
  - `false`: Section akan tertutup saat pertama dimuat
  - Jika tidak dispesifikasikan, menggunakan pengaturan global atau default
- `subsections` (array, optional): Array of subsection objects dengan struktur yang sama

## Settings

### General Settings
- **Default SOP ID**: SOP ID default ketika tidak dispesifikasikan
- **Animation Duration**: Durasi animasi accordion (ms)
- **Default Section Visibility**: Mengatur apakah section pertama akan terbuka ("Shown") atau tertutup ("Hidden") secara default

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

### Version 1.0.1
- Added default section visibility control
- New `default_visibility` shortcode parameter
- Admin setting for global default visibility
- Enhanced accordion initial state handling

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