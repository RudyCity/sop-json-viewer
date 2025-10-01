# Google Drive Folder Extractor

Google Apps Script untuk mengekstrak konten folder dari Google Drive dan mengubahnya menjadi struktur JSON yang terorganisir secara hierarkis.

## Fitur

- **Extract by Folder ID**: Menarik data file dan folder berdasarkan ID folder Google Drive
- **Extract with Filter**: Menarik data dengan filter tipe file (PDF, Excel, Docs, dll)
- **Export to JSON**: Mengubah data yang diekstrak menjadi format JSON hierarkis
- **Hierarchical Organization**: Mengorganisir file dan folder berdasarkan struktur hirarki
- **Complete File Information**: Menyimpan informasi lengkap termasuk nama, link, tipe file, ukuran, dll

## Cara Setup

1. Buka Google Sheets baru
2. Pergi ke `Extensions > Apps Script`
3. Salin dan tempel kode dari [`code.gs`](code.gs)
4. Simpan project dengan nama "Drive Folder Extractor"
5. Refresh spreadsheet - menu kustom akan muncul

## Cara Penggunaan

### 1. Extract by Folder ID

1. Buka menu custom **Drive Folder Extractor**
2. Pilih **Extract by Folder ID**
3. Masukkan ID folder Google Drive yang ingin diekstrak
4. Klik OK

**Cara mendapatkan Folder ID:**
- Buka folder di Google Drive
- Lihat URL: `https://drive.google.com/drive/folders/FOLDER_ID`
- Salin bagian FOLDER_ID

### 2. Extract with Filter

1. Buka menu custom **Drive Folder Extractor**
2. Pilih **Extract with Filter**
3. Masukkan ID folder Google Drive
4. Masukkan tipe file yang ingin diekstrak (comma-separated):
   - `pdf` untuk file PDF
   - `xls, xlsx` untuk file Excel
   - `doc, docx` untuk file Word
   - `ppt, pptx` untuk file PowerPoint
   - `pdf, docx, xlsx` untuk kombinasi
   - `all` untuk semua file
5. Pilih apakah ingin menyertakan folder kosong:
   - `yes` - Sertakan semua folder (default)
   - `no` - Hanya folder yang mengandung file yang sesuai filter
6. Klik OK

**Contoh penggunaan filter:**
- `pdf` + `no` - Hanya file PDF dan folder yang mengandung PDF
- `docx, pdf` + `yes` - File Word dan PDF dengan semua folder
- `xls, xlsx, csv` + `no` - File Excel dan CSV tanpa folder kosong
- `all` + `yes` - Semua file dan folder

### 3. Export to JSON

1. Setelah mengekstrak data, pilih **Export to JSON** dari menu
2. Script akan mengubah data menjadi format JSON hierarkis
3. Hasil akan ditampilkan di sheet "JSON Output" dan Logger

### 4. Clear Data

- Pilih **Clear Data** untuk membersihkan semua data yang diekstrak

## Jenis File yang Didukung

### Format Office:
- **PDF**: `.pdf`
- **Word**: `.doc`, `.docx`, Google Docs
- **Excel**: `.xls`, `.xlsx`, Google Sheets
- **PowerPoint**: `.ppt`, `.pptx`, Google Slides

### Format lain:
- Script akan otomatis mendeteksi berdasarkan ekstensi file dan MIME type
- Support untuk Google Workspace files dan Office files

## Struktur Data yang Diekstrak

Setiap file dan folder akan memiliki informasi:
- ID: Unique identifier
- Name: Nama file/folder
- Type: Tipe (file/folder)
- Link: URL langsung ke file/folder
- Folder Name: Nama folder induk
- Folder Path: Path lengkap folder
- Hierarchy: Struktur hirarki
- Size: Ukuran file (untuk file)
- MIME Type: Tipe MIME
- Created Date: Tanggal dibuat
- Modified Date: Tanggal dimodifikasi

## Format JSON Output

Script menghasilkan JSON dengan struktur:

```json
{
  "title": "Resource Collection SOP",
  "description": "Comprehensive collection of links and resources organized by category",
  "sections": [
    {
      "title": "📁 Folder Name",
      "content": [
        {
          "type": "link",
          "title": "File Name",
          "url": "file_url",
          "target": "_blank"
        }
      ],
      "subsections": [
        {
          "title": "📁 Subfolder Name",
          "content": [...]
        }
      ]
    }
  ]
}
```

## Contoh Penggunaan

1. **Ekstrak folder proyek**:
   - Masukkan ID folder proyek
   - Script akan mengekstrak semua file dan subfolder
   - Data ditampilkan di sheet "Folder Data"

2. **Generate JSON untuk dokumentasi**:
   - Setelah ekstraksi, pilih "Export to JSON"
   - Salin JSON dari Logger atau sheet "JSON Output"
   - Gunakan untuk dokumentasi atau integrasi dengan sistem lain

## Troubleshooting

### Error: "Invalid folder ID"
- Pastikan ID folder benar dan Anda memiliki akses
- Coba buka folder di browser untuk verifikasi

### Error: "No data found"
- Pastikan sudah menjalankan "Extract by Folder ID" terlebih dahulu
- Periksa apakah folder memiliki isi

### Performance Issues
- Untuk folder dengan banyak file (>1000), proses mungkin memerlukan waktu lebih lama
- Pertimbangkan untuk memecah folder besar menjadi subfolder yang lebih kecil

## Permissions Required

Script memerlukan izin berikut:
- Akses ke Google Drive (membaca file dan folder)
- Akses ke Google Sheets (menulis data)

## Tips & Best Practices

1. **Organize folders before extraction**: Struktur folder yang baik akan menghasilkan JSON yang lebih terorganisir
2. **Use descriptive names**: Nama folder dan file yang deskriptif akan menghasilkan output yang lebih baik
3. **Test with small folders first**: Coba dengan folder kecil sebelum memproses folder besar
4. **Regular backup**: Backup spreadsheet secara berkala untuk menjaga data

## Advanced Usage

### Custom JSON Structure
Modifikasi fungsi [`convertToJSON()`](code.gs:154) untuk mengubah format output JSON sesuai kebutuhan.

### Additional File Properties
Tambahkan properti tambahan di fungsi [`extractFolderData()`](code.gs:42) untuk mengambil informasi lebih detail.

### Integration with Other Services
Gunakan [`UrlFetchApp`](code.gs) untuk mengirim JSON ke API eksternal atau layanan lain.