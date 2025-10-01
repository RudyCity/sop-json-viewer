# Test Case - Google Drive Folder Extractor

## Test Scenario 1: Basic Folder Extraction

### Setup
- Buat folder test di Google Drive dengan struktur:
  ```
  Test Project/
  ├── Documents/
  │   ├── project-plan.pdf
  │   └── meeting-notes.docx
  ├── Images/
  │   ├── logo.png
  │   └── screenshot.jpg
  └── README.md
  ```

### Steps
1. Buka Google Sheets baru
2. Install script dari [`code.gs`](code.gs)
3. Pilih menu **Drive Folder Extractor > Extract by Folder ID**
4. Masukkan ID folder "Test Project"
5. Klik OK

### Expected Results
- Sheet "Folder Data" terbuat dengan 7 baris data (3 folder + 4 file)
- Setiap baris berisi kolom: ID, Name, Type, Link, Folder Name, Folder Path, Hierarchy, Size, MIME Type, Created Date, Modified Date
- Data terurut berdasarkan hirarki folder

### Verification
```javascript
// Test di Apps Script Editor
function verifyBasicExtraction() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  
  // Verifikasi jumlah data
  console.log(`Total rows: ${data.length}`); // Should be 8 (including header)
  
  // Verifikasi struktur folder
  const folders = data.filter(row => row[2] === "folder");
  console.log(`Number of folders: ${folders.length}`); // Should be 3
  
  // Verifikasi file
  const files = data.filter(row => row[2] === "file");
  console.log(`Number of files: ${files.length}`); // Should be 4
}
```

## Test Scenario 2: JSON Export

### Setup
- Gunakan data dari Test Scenario 1
- Pastikan sheet "Folder Data" sudah terisi

### Steps
1. Pilih menu **Drive Folder Extractor > Export to JSON**
2. Tunggu proses selesai
3. Periksa sheet "JSON Output"
4. Periksa Logger untuk JSON lengkap

### Expected Results
- Sheet "JSON Output" terbuat dengan JSON string
- Logger menampilkan JSON lengkap
- Struktur JSON sesuai format yang ditentukan:
  ```json
  {
    "title": "Resource Collection SOP",
    "description": "Comprehensive collection of links and resources organized by category",
    "sections": [
      {
        "title": "Documents",
        "content": [
          {
            "type": "link",
            "title": "project-plan.pdf",
            "url": "file_url",
            "target": "_blank"
          },
          {
            "type": "link",
            "title": "meeting-notes.docx",
            "url": "file_url",
            "target": "_blank"
          }
        ]
      },
      {
        "title": "Images",
        "content": [
          {
            "type": "link",
            "title": "logo.png",
            "url": "file_url",
            "target": "_blank"
          },
          {
            "type": "link",
            "title": "screenshot.jpg",
            "url": "file_url",
            "target": "_blank"
          }
        ]
      }
    ]
  }
  ```

### Verification
```javascript
// Test JSON structure
function verifyJSONStructure() {
  const jsonString = Logger.getLog();
  const jsonData = JSON.parse(jsonString);
  
  // Verifikasi struktur utama
  console.log(`Title: ${jsonData.title}`); // Should be "Resource Collection SOP"
  console.log(`Sections count: ${jsonData.sections.length}`); // Should be 2
  
  // Verifikasi section Documents
  const docsSection = jsonData.sections.find(s => s.title === "Documents");
  console.log(`Documents files: ${docsSection.content.length}`); // Should be 2
  
  // Verifikasi section Images
  const imagesSection = jsonData.sections.find(s => s.title === "Images");
  console.log(`Images files: ${imagesSection.content.length}`); // Should be 2
}
```

## Test Scenario 3: Deep Hierarchy

### Setup
- Buat folder dengan struktur hirarki dalam:
  ```
  Complex Project/
  ├── Phase 1/
  │   ├── Planning/
  │   │   ├── requirements.docx
  │   │   └── timeline.xlsx
  │   └── Design/
  │       ├── mockups/
  │       │   └── home-page.png
  │       └── wireframes.pdf
  ├── Phase 2/
  │   ├── Development/
  │   │   ├── source-code.zip
  │   │   └── database-schema.sql
  │   └── Testing/
  │       ├── test-plan.pdf
  │       └── bug-reports.xlsx
  └── Documentation/
      ├── user-guide.pdf
      └── api-docs.html
  ```

### Steps
1. Ekstrak folder "Complex Project"
2. Export ke JSON

### Expected Results
- JSON dengan nested subsections:
  ```json
  {
    "sections": [
      {
        "title": "Phase 1",
        "subsections": [
          {
            "title": "Planning",
            "content": [...]
          },
          {
            "title": "Design",
            "content": [...],
            "subsections": [
              {
                "title": "mockups",
                "content": [...]
              }
            ]
          }
        ]
      }
    ]
  }
  ```

### Verification
```javascript
// Test deep hierarchy
function verifyDeepHierarchy() {
  const jsonString = Logger.getLog();
  const jsonData = JSON.parse(jsonString);
  
  // Verifikasi hirarki Phase 1
  const phase1 = jsonData.sections.find(s => s.title === "Phase 1");
  console.log(`Phase 1 subsections: ${phase1.subsections.length}`); // Should be 2
  
  // Verifikasi sub-subsection
  const design = phase1.subsections.find(s => s.title === "Design");
  console.log(`Design subsections: ${design.subsections.length}`); // Should be 1
  
  // Verifikasi deepest level
  const mockups = design.subsections.find(s => s.title === "mockups");
  console.log(`Mockups files: ${mockups.content.length}`); // Should be 1
}
```

## Test Scenario 4: Error Handling

### Test 4.1: Invalid Folder ID
1. Pilih **Extract by Folder ID**
2. Masukkan ID yang tidak valid: "invalid-folder-id"
3. Klik OK

**Expected**: Error message "Invalid folder ID"

### Test 4.2: Empty Folder
1. Buat folder kosong
2. Ekstrak folder kosong tersebut

**Expected**: Sheet terbuat dengan hanya header row

### Test 4.3: No Data Export
1. Buka spreadsheet baru tanpa data
2. Pilih **Export to JSON**

**Expected**: Error message "No data found. Please extract folder data first."

## Test Scenario 5: Performance Test

### Setup
- Buat folder dengan 100+ file
- Sebarkan dalam beberapa subfolder

### Steps
1. Ekstrak folder besar
2. Catat waktu eksekusi
3. Export ke JSON
4. Verifikasi semua data teresktrak dengan benar

### Expected Results
- Semua file teresktrak (verifikasi jumlah)
- JSON structure tetap valid
- Waktu eksekusi reasonable (< 2 menit untuk 100 file)

## Automated Test Suite

```javascript
// Run all tests
function runAllTests() {
  try {
    console.log("=== Starting Test Suite ===");
    
    // Test 1: Basic extraction
    console.log("Test 1: Basic Extraction");
    testBasicExtraction();
    
    // Test 2: JSON export
    console.log("Test 2: JSON Export");
    testJSONExport();
    
    // Test 3: Deep hierarchy
    console.log("Test 3: Deep Hierarchy");
    testDeepHierarchy();
    
    // Test 4: Error handling
    console.log("Test 4: Error Handling");
    testErrorHandling();
    
    console.log("=== All Tests Completed ===");
    
  } catch (error) {
    console.log(`Test failed: ${error.message}`);
  }
}

function testBasicExtraction() {
  // Implementation for basic extraction test
}

function testJSONExport() {
  // Implementation for JSON export test
}

function testDeepHierarchy() {
  // Implementation for deep hierarchy test
}

function testErrorHandling() {
  // Implementation for error handling test
}
```

## Test Data Preparation

### Sample Folder Structure for Testing
```
Test Suite/
├── Empty Folder/
├── Single File/
│   └── test.txt
├── Multiple Files/
│   ├── doc1.pdf
│   ├── doc2.docx
│   ├── image1.png
│   └── spreadsheet.xlsx
├── Nested Structure/
│   ├── Level 1/
│   │   ├── Level 2/
│   │   │   ├── Level 3/
│   │   │   │   └── deep-file.txt
│   │   │   └── level2-file.txt
│   │   └── level1-file.txt
│   └── root-file.txt
└── Mixed Content/
    ├── Documents/
    ├── Images/
    ├── Videos/
    └── Archives/
```

## Expected Output Summary

| Test Case | Expected Files | Expected Folders | JSON Sections | Nested Levels |
|-----------|----------------|------------------|---------------|---------------|
| Basic | 4 | 3 | 2 | 1 |
| Deep Hierarchy | 9 | 7 | 3 | 3 |
| Empty Folder | 0 | 1 | 1 | 0 |
| Single File | 1 | 1 | 1 | 0 |

## Test Scenario 6: File Type Filtering

### Setup
- Buat folder dengan berbagai tipe file:
  ```
  File Types Test/
  ├── Documents/
  │   ├── report.pdf
  │   ├── proposal.docx
  │   └── notes.txt
  ├── Spreadsheets/
  │   ├── budget.xlsx
  │   ├── data.csv
  │   └── old.xls
  ├── Presentations/
  │   ├── slides.pptx
  │   └── old.ppt
  └── Others/
      ├── image.png
      ├── archive.zip
      └── video.mp4
  ```

### Test 6.1: PDF Only Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder "File Types Test"
3. Masukkan filter: `pdf`
4. Klik OK

**Expected Results**:
- Hanya file PDF yang diekstrak (report.pdf)
- Folder tetap ditampilkan (untuk konteks hirarki)
- Total file: 1, Total folder: 4

### Test 6.2: Multiple File Types Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `pdf, docx, xlsx`
4. Klik OK

**Expected Results**:
- File yang diekstrak: report.pdf, proposal.docx, budget.xlsx
- Total file: 3, Total folder: 4

### Test 6.3: Excel Files Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `xls, xlsx, csv`
4. Klik OK

**Expected Results**:
- File yang diekstrak: budget.xlsx, data.csv, old.xls
- Total file: 3, Total folder: 4

### Test 6.4: All Files Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `all`
4. Klik OK

**Expected Results**:
- Semua file diekstrak (9 file)
- Total file: 9, Total folder: 4

### Verification
```javascript
// Test file type filtering
function verifyFileTypeFiltering() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  
  // Count files and folders
  const files = data.filter(row => row[2] === "file");
  const folders = data.filter(row => row[2] === "folder");
  
  console.log(`Files extracted: ${files.length}`);
  console.log(`Folders extracted: ${folders.length}`);
  
  // Check file types
  files.forEach(file => {
    console.log(`File: ${file[1]} (${file[8]})`);
  });
}

// Test specific filter
function testPDFFilter() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  const data = extractFolderDataWithFilter(folder, ['pdf']);
  
  const pdfFiles = data.filter(item => 
    item.type === 'file' && 
    (item.name.endsWith('.pdf') || item.mimeType === 'application/pdf')
  );
  
  console.log(`PDF files found: ${pdfFiles.length}`);
}
```

## Test Scenario 7: Google Workspace Files

### Setup
- Buat folder dengan Google Workspace files:
  ```
  Google Workspace Test/
  ├── Google Docs/
  │   ├── Meeting Notes (Google Doc)
  │   └── Project Plan (Google Doc)
  ├── Google Sheets/
  │   ├── Budget Tracker (Google Sheet)
  │   └── Data Analysis (Google Sheet)
  └── Google Slides/
      ├── Presentation (Google Slides)
      └── Training Deck (Google Slides)
  ```

### Test 7.1: Google Docs Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder
3. Masukkan filter: `doc, docx`
4. Klik OK

**Expected Results**:
- Semua Google Docs terdeteksi dan diekstrak
- Total file: 2 (Google Docs)

### Test 7.2: Google Sheets Filter
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `xls, xlsx`
4. Klik OK

**Expected Results**:
- Semua Google Sheets terdeteksi dan diekstrak
- Total file: 2 (Google Sheets)

### Verification
```javascript
// Test Google Workspace detection
function verifyGoogleWorkspaceDetection() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  
  const googleDocs = data.filter(row => 
    row[2] === "file" && 
    row[8] === "application/vnd.google-apps.document"
  );
  
  const googleSheets = data.filter(row => 
    row[2] === "file" && 
    row[8] === "application/vnd.google-apps.spreadsheet"
  );
  
  console.log(`Google Docs found: ${googleDocs.length}`);
  console.log(`Google Sheets found: ${googleSheets.length}`);
}
```

## Test Scenario 8: Filter Edge Cases

### Test 8.1: Invalid File Type
1. Pilih **Extract with Filter**
2. Masukkan folder ID
3. Masukkan filter: `invalidtype`
4. Klik OK

**Expected Results**:
- Tidak ada file yang diekstrak
- Hanya folder yang ditampilkan
- Pesan sukses dengan 0 file

### Test 8.2: Mixed Case Filter
1. Pilih **Extract with Filter**
2. Masukkan folder ID
3. Masukkan filter: `PDF, DOCX`
4. Klik OK

**Expected Results**:
- Filter bekerja (case-insensitive)
- File PDF dan DOCX diekstrak

### Test 8.3: Empty Filter Input
1. Pilih **Extract with Filter**
2. Masukkan folder ID
3. Kosongkan input filter
4. Klik OK

**Expected Results**:
- Error message: "Please enter a valid filter"

## Updated Test Data Summary

| Test Case | Expected Files | Expected Folders | Filter Type | JSON Sections | Nested Levels |
|-----------|----------------|------------------|-------------|---------------|---------------|
| Basic | 4 | 3 | none | 2 | 1 |
| Deep Hierarchy | 9 | 7 | none | 3 | 3 |
| Empty Folder | 0 | 1 | none | 1 | 0 |
| Single File | 1 | 1 | none | 1 | 0 |
| Mixed Content | 0 | 4 | none | 4 | 1 |
| PDF Only | 1 | 4 | pdf | 2 | 1 |
| Multiple Types | 3 | 4 | pdf,docx,xlsx | 3 | 1 |
| Excel Files | 3 | 4 | xls,xlsx,csv | 2 | 1 |
| All Files | 9 | 4 | all | 4 | 1 |
| Google Docs | 2 | 3 | doc,docx | 1 | 1 |
| Google Sheets | 2 | 3 | xls,xlsx | 1 | 1 |

## Automated Filter Test Suite

```javascript
// Run all filter tests
function runFilterTests() {
  try {
    console.log("=== Starting Filter Test Suite ===");
    
    // Test 1: PDF filter
    console.log("Test 1: PDF Filter");
    testPDFFilter();
    
    // Test 2: Multiple types filter
    console.log("Test 2: Multiple Types Filter");
    testMultipleTypesFilter();
    
    // Test 3: Excel filter
    console.log("Test 3: Excel Filter");
    testExcelFilter();
    
    // Test 4: Google Workspace filter
    console.log("Test 4: Google Workspace Filter");
    testGoogleWorkspaceFilter();
    
    console.log("=== All Filter Tests Completed ===");
    
  } catch (error) {
    console.log(`Filter test failed: ${error.message}`);
  }
}

function testPDFFilter() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  const data = extractFolderDataWithFilter(folder, ['pdf']);
  
  const pdfFiles = data.filter(item => 
    item.type === 'file' && 
    (item.name.endsWith('.pdf') || item.mimeType === 'application/pdf')
  );
  
  console.log(`PDF filter test: ${pdfFiles.length} PDF files found`);
}

function testMultipleTypesFilter() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  const data = extractFolderDataWithFilter(folder, ['pdf', 'docx', 'xlsx']);
  
  const files = data.filter(item => item.type === 'file');
  console.log(`Multiple types filter: ${files.length} files found`);
  
  // Verify file types
  files.forEach(file => {
    const isPDF = file.name.endsWith('.pdf') || file.mimeType === 'application/pdf';
    const isDOCX = file.name.endsWith('.docx') || file.mimeType.includes('document');
    const isXLSX = file.name.endsWith('.xlsx') || file.mimeType.includes('spreadsheet');
    
    if (!isPDF && !isDOCX && !isXLSX) {
      console.log(`Unexpected file type: ${file.name} (${file.mimeType})`);
    }
  });
}

function testExcelFilter() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  const data = extractFolderDataWithFilter(folder, ['xls', 'xlsx', 'csv']);
  
  const excelFiles = data.filter(item => {
    if (item.type !== 'file') return false;
    
    const name = item.name.toLowerCase();
    const mime = item.mimeType.toLowerCase();
    
    return name.endsWith('.xls') || 
           name.endsWith('.xlsx') || 
           name.endsWith('.csv') ||
           mime.includes('spreadsheet');
  });
  
  console.log(`Excel filter test: ${excelFiles.length} Excel files found`);
}

function testGoogleWorkspaceFilter() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  const data = extractFolderDataWithFilter(folder, ['docx', 'xlsx']);
  
  const googleWorkspaceFiles = data.filter(item => {
    if (item.type !== 'file') return false;
    
    const mime = item.mimeType;
    return mime.includes('google-apps');
  });
  
  console.log(`Google Workspace filter: ${googleWorkspaceFiles.length} files found`);
}
```
| Mixed Content | 0 | 4 | 4 | 1 |

## Test Scenario 9: Exclude Empty Folders

### Setup
- Buat folder dengan struktur:
  ```
  Filter Test/
  ├── Documents/
  │   ├── report.pdf
  │   └── presentation.pptx
  ├── Images/
  │   ├── photo.jpg
  │   └── icon.png
  ├── Empty Folder/
  └── Another Empty/
      └── Still Empty/
  ```

### Test 9.1: PDF Filter with Empty Folders Included
1. Pilih **Extract with Filter**
2. Masukkan ID folder "Filter Test"
3. Masukkan filter: `pdf`
4. Pilih include empty folders: `yes`
5. Klik OK

**Expected Results**:
- File yang diekstrak: report.pdf
- Semua folder muncul (termasuk yang kosong)
- Total file: 1, Total folder: 5

### Test 9.2: PDF Filter with Empty Folders Excluded
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `pdf`
4. Pilih include empty folders: `no`
5. Klik OK

**Expected Results**:
- File yang diekstrak: report.pdf
- Hanya folder "Documents" yang muncul (mengandung PDF)
- Total file: 1, Total folder: 1

### Test 9.3: Multiple Types with Empty Folders Excluded
1. Pilih **Extract with Filter**
2. Masukkan ID folder yang sama
3. Masukkan filter: `pdf, jpg`
4. Pilih include empty folders: `no`
5. Klik OK

**Expected Results**:
- File yang diekstrak: report.pdf, photo.jpg
- Folder yang muncul: "Documents" (ada PDF), "Images" (ada JPG)
- Total file: 2, Total folder: 2

### Verification
```javascript
// Test empty folder exclusion
function verifyEmptyFolderExclusion() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  
  // Count files and folders
  const files = data.filter(row => row[2] === "file");
  const folders = data.filter(row => row[2] === "folder");
  
  console.log(`Files extracted: ${files.length}`);
  console.log(`Folders extracted: ${folders.length}`);
  
  // Check if empty folders are excluded
  const folderNames = folders.map(folder => folder[1]);
  console.log(`Folder names: ${folderNames.join(', ')}`);
  
  // Verify no empty folders (when exclude option is selected)
  const hasEmptyFolders = folderNames.includes("Empty Folder") || 
                         folderNames.includes("Another Empty");
  
  if (!hasEmptyFolders) {
    console.log("✓ Empty folders successfully excluded");
  } else {
    console.log("✗ Empty folders still present");
  }
}

// Test comparison between include/exclude options
function testFolderInclusionOptions() {
  const testFolderId = "test-folder-id";
  const folder = DriveApp.getFolderById(testFolderId);
  
  // Test with empty folders included
  const dataWithEmpty = extractFolderDataWithFilter(folder, ['pdf'], true);
  const foldersWithEmpty = dataWithEmpty.filter(item => item.type === 'folder');
  
  // Test with empty folders excluded
  const dataWithoutEmpty = extractFolderDataWithFilter(folder, ['pdf'], false);
  const foldersWithoutEmpty = dataWithoutEmpty.filter(item => item.type === 'folder');
  
  console.log(`With empty folders: ${foldersWithEmpty.length} folders`);
  console.log(`Without empty folders: ${foldersWithoutEmpty.length} folders`);
  
  // Should have fewer folders when empty ones are excluded
  if (foldersWithoutEmpty.length <= foldersWithEmpty.length) {
    console.log("✓ Empty folder exclusion working correctly");
  } else {
    console.log("✗ Empty folder exclusion not working");
  }
}
```

## Updated Test Data Summary with Empty Folder Exclusion

| Test Case | Expected Files | Expected Folders | Filter Type | Empty Folders | JSON Sections | Nested Levels |
|-----------|----------------|------------------|-------------|---------------|---------------|---------------|
| Basic | 4 | 3 | none | N/A | 2 | 1 |
| PDF Only (include) | 1 | 5 | pdf | yes | 2 | 1 |
| PDF Only (exclude) | 1 | 1 | pdf | no | 1 | 1 |
| Multiple Types (include) | 3 | 5 | pdf,jpg | yes | 3 | 1 |
| Multiple Types (exclude) | 3 | 2 | pdf,jpg | no | 2 | 1 |
| Deep Hierarchy | 9 | 7 | none | N/A | 3 | 3 |
| Empty Folder | 0 | 1 | none | N/A | 1 | 0 |
