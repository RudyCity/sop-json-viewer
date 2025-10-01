# Architecture (high-level)

**Komponen Utama:**
- **Shortcode Handler**: Mengelola shortcode `[sop-accordion]` untuk menampilkan konten
- **JSON Editor Interface**: Admin page untuk mengedit data SOP dengan validasi
- **Data Storage**: Menyimpan data JSON dalam WordPress options atau custom post type
- **Frontend Renderer**: Menampilkan accordion dengan CSS dan JavaScript
- **Validation Engine**: Validasi struktur JSON secara real-time

**Integrasi eksternal:**
- WordPress Shortcode API untuk integrasi dengan editor konten
- WordPress Options API untuk penyimpanan konfigurasi
- jQuery untuk interaktivitas frontend (accordion functionality)
- CodeMirror atau Monaco Editor untuk JSON editor interface

**Diagram:**

```
┌─────────────────┐    ┌──────────────────┐
│   WordPress     │    │   Admin Panel    │
│   Frontend      │◄──►│   JSON Editor    │
│   (Shortcode)   │    │   w/ Validation  │
└─────────────────┘    └──────────────────┘
         │                       │
         ▼                       ▼
┌─────────────────┐    ┌──────────────────┐
│   Accordion     │    │   JSON Data      │
│   Renderer      │    │   Storage        │
│   (CSS/JS)      │    │   (Options API)  │
└─────────────────┘    └──────────────────┘
```

**File Structure:**
```
sop-json-viewer/
├── includes/
│   ├── class-sop-json-viewer.php
│   ├── class-json-editor.php
│   └── class-accordion-renderer.php
├── assets/
│   ├── css/
│   │   └── accordion.css
│   └── js/
│       ├── accordion.js
│       └── json-editor.js
├── templates/
│   └── accordion-template.php
└── sop-json-viewer.php (main plugin file)