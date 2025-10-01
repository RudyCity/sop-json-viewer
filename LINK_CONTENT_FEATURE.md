# SOP JSON Viewer - Link Content Feature

## Overview

Fitur baru ini memungkinkan Anda menampilkan konten dalam bentuk list link yang interaktif dan menarik secara visual. Setiap section dalam SOP sekarang dapat memiliki konten berupa:

1. **HTML Content** (string) - konten HTML biasa seperti sebelumnya
2. **Link List** (array) - daftar link dengan styling khusus

## Struktur JSON

### Link List Structure

```json
{
  "title": "Judul Section",
  "content": [
    {
      "type": "link",
      "title": "Judul Link",
      "url": "https://example.com",
      "target": "_blank"
    }
  ]
}
```

### Field Properties

- `type` (string, required): Tipe konten, untuk link gunakan `"link"`
- `title` (string, required): Teks yang akan ditampilkan pada link
- `url` (string, required): URL tujuan link
- `target` (string, optional): Target pembukaan link (`_blank`, `_self`, `_parent`, `_top`)

### Sorting Options

Section dengan link list dapat memiliki opsi sorting:

- `sort` (boolean, optional): Enable/disable sorting (default: `true`)
- `sort_by` (string, optional): Field untuk sorting (default: `"title"`)
- `sort_order` (string, optional): Urutan sorting (`asc` atau `desc`, default: `"asc"`)

## Contoh Penggunaan

### Section dengan Link List

```json
{
  "title": "📚 Referensi & Dokumentasi",
  "sort": true,
  "sort_by": "title",
  "sort_order": "asc",
  "content": [
    {
      "type": "link",
      "title": "Form Laporan Insiden",
      "url": "/forms/incident-report"
    },
    {
      "type": "link",
      "title": "Manual Panduan Operasional",
      "url": "https://example.com/manual-operasional",
      "target": "_blank"
    }
  ]
}
```

### Section dengan Sorting Dinonaktifkan

```json
{
  "title": "📚 Referensi & Dokumentasi",
  "sort": false,
  "content": [
    {
      "type": "link",
      "title": "Manual Panduan Operasional",
      "url": "https://example.com/manual-operasional",
      "target": "_blank"
    },
    {
      "type": "link",
      "title": "Form Laporan Insiden",
      "url": "/forms/incident-report"
    }
  ]
}
```

### Mixed Content

Anda dapat menggabungkan section dengan link list dan section dengan HTML biasa:

```json
{
  "title": "SOP Lengkap",
  "sections": [
    {
      "title": "Referensi",
      "content": [
        {
          "type": "link",
          "title": "Documentation",
          "url": "/docs"
        }
      ]
    },
    {
      "title": "Prosedur",
      "content": "<p>Ini adalah konten HTML biasa dengan <strong>formatting</strong>.</p>"
    }
  ]
}
```

## Fitur Tambahan

### Styling Otomatis

- Link list akan otomatis mendapatkan styling modern dengan hover effects
- External link (target="_blank") akan mendapatkan indikator visual
- Responsive design untuk semua ukuran layar

### Aksesibilitas

- ARIA labels otomatis untuk external links
- Keyboard navigation support
- Screen reader friendly

### JavaScript Events

Fitur ini menyediakan custom events untuk tracking:

```javascript
// Track link clicks
document.addEventListener('sop:linkClicked', function(e) {
    console.log('Link clicked:', e.detail);
    // e.detail berisi: link, title, url, isExternal
});
```

### Loading States

External links akan menampilkan loading state saat diklik untuk memberikan feedback visual kepada user.

## Validasi & Security

- URLs otomatis divalidasi dan disanitized untuk keamanan
- Target values divalidasi untuk mencegah security issues
- Semua input melalui WordPress sanitization functions

## Tips & Best Practices

1. **Gunakan deskriptif title** - Buat judul link yang jelas dan deskriptif
2. **Pilih target yang tepat** - Gunakan `_blank` untuk external links
3. **Mix dengan HTML content** - Kombinasikan link list dengan konten HTML untuk hasil terbaik
4. **Gunakan di subsections** - Link list juga bekerja dengan baik di dalam subsections

## File Contoh

- `example-links.json` - Contoh lengkap JSON dengan link content
- `test-links.html` - Demo HTML untuk testing fitur

## Compatibility

Fitur ini backward compatible dengan existing JSON structure. SOP yang sudah ada akan terus bekerja tanpa perubahan.