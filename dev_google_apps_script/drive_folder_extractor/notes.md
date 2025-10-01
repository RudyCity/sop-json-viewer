# Development Notes - Google Drive Folder Extractor

## Architecture Overview

Script ini dirancang dengan arsitektur modular untuk memudahkan maintenance dan pengembangan:

1. **Menu System**: [`onOpen()`](code.gs:15) function creates custom menu
2. **Data Extraction**: [`extractFolderData()`](code.gs:42) handles recursive folder traversal
3. **Data Storage**: [`storeDataInSheet()`](code.gs:85) manages spreadsheet operations
4. **JSON Conversion**: [`convertToJSON()`](code.gs:154) transforms data to hierarchical structure
5. **Hierarchy Building**: [`buildHierarchy()`](code.gs:192) creates nested JSON structure

## Key Implementation Details

### Recursive Folder Traversal
```javascript
function extractFolderData(folder, parentPath = "") {
  // Current path tracking for hierarchy
  const currentPath = parentPath ? `${parentPath}/${folder.getName()}` : folder.getName();
  
  // Process current folder
  // Process files
  // Recursively process subfolders
}
```

### JSON Structure Generation
Script menggunakan two-step approach:
1. **Flat mapping**: Group files by folder path
2. **Tree building**: Convert flat map to hierarchical structure

### Error Handling Strategy
- Try-catch blocks for main operations
- User-friendly error messages
- Graceful handling of edge cases (empty folders, invalid IDs)

## Performance Considerations

### Optimization Techniques
1. **Batch Operations**: Minimize API calls by processing data in batches
2. **Lazy Loading**: Only process folders when needed
3. **Memory Management**: Clear variables after use

### Limitations
- Google Drive API quota: 10,000 requests per 100 seconds
- Large folders (>1000 files) may hit timeout limits
- Spreadsheet size limits (10 million cells)

## Debugging Tips

### Common Issues & Solutions

1. **Permission Errors**
   ```
   Error: "You do not have permission to perform that action."
   Solution: Check folder sharing settings and script permissions
   ```

2. **Timeout Issues**
   ```
   Error: "Exceeded maximum execution time"
   Solution: Process smaller batches or use time-based triggers
   ```

3. **Invalid Folder ID**
   ```
   Error: "No item with the given ID could be found"
   Solution: Verify folder ID and access permissions
   ```

### Debugging Functions
```javascript
// Add to code.gs for debugging
function debugFolderStructure(folderId) {
  try {
    const folder = DriveApp.getFolderById(folderId);
    Logger.log(`Folder: ${folder.getName()}`);
    Logger.log(`Files: ${folder.getFiles().hasNext() ? 'Yes' : 'No'}`);
    Logger.log(`Subfolders: ${folder.getFolders().hasNext() ? 'Yes' : 'No'}`);
  } catch (error) {
    Logger.log(`Debug error: ${error.message}`);
  }
}

function debugDataStructure() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  Logger.log(`Total rows: ${data.length}`);
  Logger.log(`Headers: ${data[0].join(", ")}`);
}
```

## Extension Ideas

### 1. Advanced Filtering
```javascript
// Add filter options to extraction
function extractWithFilters(folderId, filters) {
  const filters = {
    fileTypes: ['pdf', 'docx'], // Only specific file types
    minSize: 1024,              // Minimum file size
    dateRange: {               // Date range filter
      start: new Date('2023-01-01'),
      end: new Date('2023-12-31')
    }
  };
  // Implementation...
}
```

### 2. Multiple Folder Processing
```javascript
// Process multiple folders at once
function extractMultipleFolders(folderIds) {
  const allData = [];
  folderIds.forEach(folderId => {
    const folder = DriveApp.getFolderById(folderId);
    const data = extractFolderData(folder);
    allData.push(...data);
  });
  return allData;
}
```

### 3. Export to Different Formats
```javascript
// Export to CSV
function exportToCSV() {
  const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
  const data = sheet.getDataRange().getValues();
  const csv = data.map(row => row.join(",")).join("\n");
  // Save CSV to Drive or return as string
}

// Export to XML
function exportToXML() {
  // Convert data to XML format
}
```

### 4. Integration with External Services
```javascript
// Send JSON to webhook
function sendToWebhook(jsonData, webhookUrl) {
  const options = {
    method: 'post',
    contentType: 'application/json',
    payload: JSON.stringify(jsonData)
  };
  UrlFetchApp.fetch(webhookUrl, options);
}

// Upload to Google Cloud Storage
function uploadToGCS(data, bucketName, fileName) {
  // Use Google Cloud Storage API
}
```

## Security Considerations

### Data Privacy
- Script hanya membaca metadata file (tidak mengunduh konten)
- Data disimpan lokal di spreadsheet
- Tidak ada data yang dikirim ke server eksternal

### Access Control
- Gunakan service account untuk production environment
- Implement role-based access jika diperlukan
- Log semua operasi untuk audit trail

## Deployment Best Practices

### 1. Version Control
- Simpan versi script yang berbeda untuk fitur berbeda
- Gunakan Git untuk tracking perubahan

### 2. Testing Environment
- Buat spreadsheet terpisah untuk testing
- Gunakan folder test yang tidak mengandung data sensitif

### 3. Production Deployment
- Test thoroughly sebelum deploy ke production
- Monitor script usage dan performance
- Setup alerts untuk error monitoring

## Troubleshooting Guide

### Step-by-Step Debugging

1. **Check Permissions**
   ```javascript
   function checkPermissions() {
     const folderId = "test-folder-id";
     try {
       const folder = DriveApp.getFolderById(folderId);
       Logger.log("Folder access: OK");
     } catch (error) {
       Logger.log(`Folder access failed: ${error.message}`);
     }
   }
   ```

2. **Verify Data Structure**
   ```javascript
   function verifyDataStructure() {
     const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
     if (!sheet) {
       Logger.log("Sheet not found");
       return;
     }
     
     const data = sheet.getDataRange().getValues();
     if (data.length <= 1) {
       Logger.log("No data found");
       return;
     }
     
     Logger.log(`Data structure OK: ${data.length} rows`);
   }
   ```

3. **Test JSON Conversion**
   ```javascript
   function testJSONConversion() {
     const sheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Folder Data");
     const data = sheet.getDataRange().getValues();
     
     try {
       const jsonData = convertToJSON(data);
       Logger.log("JSON conversion: OK");
       Logger.log(`Sections: ${jsonData.sections.length}`);
     } catch (error) {
       Logger.log(`JSON conversion failed: ${error.message}`);
     }
   }
   ```

## Performance Monitoring

### Metrics to Track
- Execution time per folder
- Number of files processed
- Memory usage
- API quota consumption

### Monitoring Functions
```javascript
function logPerformance() {
  const startTime = new Date();
  
  // Run extraction
  extractByFolderId();
  
  const endTime = new Date();
  const duration = endTime - startTime;
  
  Logger.log(`Execution time: ${duration}ms`);
  
  // Log to spreadsheet for tracking
  const logSheet = SpreadsheetApp.getActiveSpreadsheet().getSheetByName("Performance Log");
  logSheet.appendRow([new Date(), duration, "Folder extraction"]);
}
```

## Future Enhancements

### Planned Features
1. **Real-time Sync**: Automatic updates when folder contents change
2. **Advanced Search**: Search within extracted data
3. **Custom Templates**: Different JSON output formats
4. **Dashboard**: Visual overview of extracted data
5. **API Integration**: Direct integration with CMS or documentation systems

### Technical Improvements
1. **Caching**: Cache folder data to reduce API calls
2. **Parallel Processing**: Process multiple folders simultaneously
3. **Incremental Updates**: Only process changed files
4. **Compression**: Compress JSON output for large datasets

## File Type Filtering Feature

### Implementation Details
Script sekarang mendukung filtering berdasarkan tipe file melalui fungsi baru:
- [`extractWithFilter()`](code.gs:58) - Menu untuk ekstrak dengan filter
- [`extractFolderDataWithFilter()`](code.gs:104) - Fungsi rekursif dengan filter
- [`isFileTypeMatch()`](code.gs:147) - Validasi tipe file

### Supported File Types
Filter mendukung berbagai format:
- **PDF**: `.pdf` files
- **Word**: `.doc`, `.docx`, Google Docs
- **Excel**: `.xls`, `.xlsx`, `.csv`, Google Sheets
- **PowerPoint**: `.ppt`, `.pptx`, Google Slides

### Filter Logic
Script menggunakan dua metode untuk deteksi tipe file:
1. **File Extension**: Mengecek ekstensi nama file
2. **MIME Type**: Mengecek MIME type untuk Google Workspace files

### Performance Considerations
- Filter mengurangi jumlah data yang diproses
- Lebih efisien untuk folder dengan banyak file
- Folder tetap diproses untuk menjaga konteks hirarki

### Extension Ideas for Filtering
```javascript
// Advanced filtering options
function extractWithAdvancedFilters(folderId, filters) {
  const filters = {
    fileTypes: ['pdf', 'docx'],
    dateRange: {
      start: new Date('2023-01-01'),
      end: new Date('2023-12-31')
    },
    sizeRange: {
      min: 1024,      // 1KB
      max: 10485760   // 10MB
    },
    namePattern: 'report' // Files containing 'report' in name
  };
  // Implementation...
}

// Regex-based filtering
function extractWithRegexFilter(folderId, pattern) {
  const regex = new RegExp(pattern, 'i');
  // Filter files based on regex pattern
}
```
