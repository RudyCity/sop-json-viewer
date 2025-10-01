/**
 * Google Drive Folder Extractor
 * Extracts folder contents and creates hierarchical JSON structure
 */

// Global variables
const SCRIPT_NAME = "Drive Folder Extractor";
const SHEET_NAME = "Folder Data";
const JSON_SHEET_NAME = "JSON Output";

/**
 * Creates custom menu when spreadsheet opens
 */
function onOpen() {
  SpreadsheetApp.getUi()
    .createMenu(SCRIPT_NAME)
    .addItem("Extract by Folder ID", "extractByFolderId")
    .addItem("Extract with Filter", "extractWithFilter")
    .addItem("Export to JSON", "exportToJSON")
    .addSeparator()
    .addItem("Clear Data", "clearData")
    .addToUi();
}

/**
 * Main function to extract folder data by ID
 */
function extractByFolderId() {
  const ui = SpreadsheetApp.getUi();
  
  // Prompt for folder ID
  const response = ui.prompt(
    "Extract Folder Data",
    "Enter Google Drive Folder ID:",
    ui.ButtonSet.OK_CANCEL
  );
  
  if (response.getSelectedButton() == ui.Button.OK) {
    const folderId = response.getResponseText().trim();
    
    if (!folderId) {
      ui.alert("Please enter a valid folder ID");
      return;
    }
    
    try {
      // Get folder and extract data
      const folder = DriveApp.getFolderById(folderId);
      const folderData = extractFolderData(folder);
      
      // Store data in spreadsheet
      storeDataInSheet(folderData);
      
      ui.alert(`Successfully extracted ${folderData.length} files and folders from "${folder.getName()}"`);
    } catch (error) {
      ui.alert(`Error: ${error.message}`);
    }
  }
}

/**
 * Extract folder data with file type filter
 */
function extractWithFilter() {
  const ui = SpreadsheetApp.getUi();
  
  // Prompt for folder ID
  const folderResponse = ui.prompt(
    "Extract with Filter",
    "Enter Google Drive Folder ID:",
    ui.ButtonSet.OK_CANCEL
  );
  
  if (folderResponse.getSelectedButton() != ui.Button.OK) {
    return;
  }
  
  const folderId = folderResponse.getResponseText().trim();
  if (!folderId) {
    ui.alert("Please enter a valid folder ID");
    return;
  }
  
  // Prompt for file type filter
  const filterResponse = ui.prompt(
    "Select File Type Filter",
    "Enter file types to extract (comma-separated):\n" +
    "Examples: pdf, xls, xlsx, doc, docx\n" +
    "Or enter 'all' for all files",
    ui.ButtonSet.OK_CANCEL
  );
  
  if (filterResponse.getSelectedButton() != ui.Button.OK) {
    return;
  }
  
  const filterInput = filterResponse.getResponseText().trim().toLowerCase();
  if (!filterInput) {
    ui.alert("Please enter a valid filter");
    return;
  }
  
  // Prompt for folder inclusion option
  const folderResponse2 = ui.prompt(
    "Include Empty Folders?",
    "Include folders that don't contain matching files?\n" +
    "Enter 'yes' to include all folders, 'no' to exclude empty folders",
    ui.ButtonSet.OK_CANCEL
  );
  
  if (folderResponse2.getSelectedButton() != ui.Button.OK) {
    return;
  }
  
  const includeEmptyFolders = folderResponse2.getResponseText().trim().toLowerCase() === 'yes';
  
  try {
    // Parse filter
    let fileTypes = [];
    if (filterInput !== 'all') {
      fileTypes = filterInput.split(',').map(type => type.trim());
    }
    
    // Get folder and extract data with filter
    const folder = DriveApp.getFolderById(folderId);
    const folderData = extractFolderDataWithFilter(folder, fileTypes, includeEmptyFolders);
    
    // Store data in spreadsheet
    storeDataInSheet(folderData);
    
    const filterText = filterInput === 'all' ? 'all files' : `${fileTypes.join(', ')} files`;
    const folderText = includeEmptyFolders ? 'including empty folders' : 'excluding empty folders';
    ui.alert(`Successfully extracted ${folderData.length} items (${filterText}, ${folderText}) from "${folder.getName()}"`);
    
  } catch (error) {
    ui.alert(`Error: ${error.message}`);
  }
}

/**
 * Extracts all files and folders from a folder recursively
 * @param {Folder} folder - The folder to extract
 * @param {string} parentPath - Parent folder path (for hierarchy)
 * @returns {Array} Array of file/folder data
 */
function extractFolderData(folder, parentPath = "") {
  const data = [];
  const currentPath = parentPath ? `${parentPath}/${folder.getName()}` : folder.getName();
  
  // Add folder info
  data.push({
    id: folder.getId(),
    name: folder.getName(),
    type: "folder",
    link: folder.getUrl(),
    folderName: folder.getName(),
    folderPath: currentPath,
    hierarchy: currentPath.split("/"),
    size: "",
    mimeType: "application/vnd.google-apps.folder",
    createdDate: folder.getDateCreated(),
    modifiedDate: folder.getLastUpdated()
  });
  
  // Get all files in current folder
  const files = folder.getFiles();
  while (files.hasNext()) {
    const file = files.next();
    data.push({
      id: file.getId(),
      name: file.getName(),
      type: "file",
      link: file.getUrl(),
      folderName: folder.getName(),
      folderPath: currentPath,
      hierarchy: currentPath.split("/"),
      size: file.getSize(),
      mimeType: file.getMimeType(),
      createdDate: file.getDateCreated(),
      modifiedDate: file.getLastUpdated()
    });
  }
  
  // Recursively process subfolders
  const subfolders = folder.getFolders();
  while (subfolders.hasNext()) {
    const subfolder = subfolders.next();
    const subfolderData = extractFolderData(subfolder, currentPath);
    data.push(...subfolderData);
  }
  
  return data;
}

/**
 * Extracts files and folders from a folder recursively with file type filter
 * @param {Folder} folder - The folder to extract
 * @param {Array} fileTypes - Array of file extensions to filter (empty = all files)
 * @param {string} parentPath - Parent folder path (for hierarchy)
 * @returns {Array} Array of file/folder data
 */
function extractFolderDataWithFilter(folder, fileTypes = [], parentPath = "") {
  const data = [];
  const currentPath = parentPath ? `${parentPath}/${folder.getName()}` : folder.getName();
  
  // Add folder info
  data.push({
    id: folder.getId(),
    name: folder.getName(),
    type: "folder",
    link: folder.getUrl(),
    folderName: folder.getName(),
    folderPath: currentPath,
    hierarchy: currentPath.split("/"),
    size: "",
    mimeType: "application/vnd.google-apps.folder",
    createdDate: folder.getDateCreated(),
    modifiedDate: folder.getLastUpdated()
  });
  
  // Get all files in current folder
  const files = folder.getFiles();
  while (files.hasNext()) {
    const file = files.next();
    
    // Apply file type filter if specified
    if (fileTypes.length > 0 && !isFileTypeMatch(file, fileTypes)) {
      continue;
    }
    
    data.push({
      id: file.getId(),
      name: file.getName(),
      type: "file",
      link: file.getUrl(),
      folderName: folder.getName(),
      folderPath: currentPath,
      hierarchy: currentPath.split("/"),
      size: file.getSize(),
      mimeType: file.getMimeType(),
      createdDate: file.getDateCreated(),
      modifiedDate: file.getLastUpdated()
    });
  }
  
  // Recursively process subfolders
  const subfolders = folder.getFolders();
  while (subfolders.hasNext()) {
    const subfolder = subfolders.next();
    const subfolderData = extractFolderDataWithFilter(subfolder, fileTypes, currentPath);
    data.push(...subfolderData);
  }
  
  return data;
}

/**
 * Checks if file matches the specified file types
 * @param {File} file - The file to check
 * @param {Array} fileTypes - Array of file extensions
 * @returns {boolean} True if file matches
 */
function isFileTypeMatch(file, fileTypes) {
  const fileName = file.getName().toLowerCase();
  const mimeType = file.getMimeType().toLowerCase();
  
  return fileTypes.some(type => {
    // Check by file extension
    if (fileName.endsWith('.' + type)) {
      return true;
    }
    
    // Check by MIME type for Google Workspace files
    const mimeMap = {
      'pdf': 'application/pdf',
      'doc': 'application/vnd.google-apps.document',
      'docx': 'application/vnd.google-apps.document',
      'xls': 'application/vnd.google-apps.spreadsheet',
      'xlsx': 'application/vnd.google-apps.spreadsheet',
      'ppt': 'application/vnd.google-apps.presentation',
      'pptx': 'application/vnd.google-apps.presentation'
    };
    
    if (mimeMap[type] && mimeType === mimeMap[type]) {
      return true;
    }
    
    // Check standard MIME types
    const standardMimeTypes = {
      'pdf': 'application/pdf',
      'doc': 'application/msword',
      'docx': 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'xls': 'application/vnd.ms-excel',
      'xlsx': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      'ppt': 'application/vnd.ms-powerpoint',
      'pptx': 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
    };
    
    return standardMimeTypes[type] && mimeType === standardMimeTypes[type];
  });
}

/**
 * Stores extracted data in spreadsheet
 * @param {Array} data - Array of file/folder data
 */
function storeDataInSheet(data) {
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  
  // Create or get data sheet
  let sheet = ss.getSheetByName(SHEET_NAME);
  if (!sheet) {
    sheet = ss.insertSheet(SHEET_NAME);
  }
  
  // Clear existing data
  sheet.clear();
  
  // Set headers
  const headers = [
    "ID", "Name", "Type", "Link", "Folder Name", 
    "Folder Path", "Hierarchy", "Size", "MIME Type", 
    "Created Date", "Modified Date"
  ];
  sheet.appendRow(headers);
  
  // Format header row
  sheet.getRange(1, 1, 1, headers.length)
    .setFontWeight("bold")
    .setBackground("#f0f0f0");
  
  // Add data
  data.forEach(item => {
    sheet.appendRow([
      item.id,
      item.name,
      item.type,
      item.link,
      item.folderName,
      item.folderPath,
      item.hierarchy.join(" > "),
      item.size,
      item.mimeType,
      item.createdDate,
      item.modifiedDate
    ]);
  });
  
  // Auto-resize columns
  sheet.autoResizeColumns(1, headers.length);
  
  // Freeze header row
  sheet.setFrozenRows(1);
}

/**
 * Converts extracted data to hierarchical JSON structure
 */
function exportToJSON() {
  const ui = SpreadsheetApp.getUi();
  const ss = SpreadsheetApp.getActiveSpreadsheet();
  
  // Get data sheet
  const sheet = ss.getSheetByName(SHEET_NAME);
  if (!sheet) {
    ui.alert("No data found. Please extract folder data first.");
    return;
  }
  
  // Get all data
  const data = sheet.getDataRange().getValues();
  if (data.length <= 1) {
    ui.alert("No data to export. Please extract folder data first.");
    return;
  }
  
  try {
    // Convert to JSON structure
    const jsonData = convertToJSON(data);
    
    // Create JSON output sheet
    let jsonSheet = ss.getSheetByName(JSON_SHEET_NAME);
    if (!jsonSheet) {
      jsonSheet = ss.insertSheet(JSON_SHEET_NAME);
    } else {
      jsonSheet.clear();
    }
    
    // Format and display JSON
    const jsonString = JSON.stringify(jsonData, null, 2);
    
    // Split into chunks for spreadsheet display
    const chunks = jsonString.match(/.{1,32767}/g) || [];
    chunks.forEach((chunk, index) => {
      jsonSheet.getRange(index + 1, 1).setValue(chunk);
    });
    
    // Also log to console for easy copying
    Logger.log(jsonString);
    
    ui.alert("JSON structure created successfully! Check the 'JSON Output' sheet and Logger for the complete JSON.");
    
  } catch (error) {
    ui.alert(`Error creating JSON: ${error.message}`);
  }
}

/**
 * Converts spreadsheet data to hierarchical JSON structure
 * @param {Array} data - Spreadsheet data (including headers)
 * @returns {Object} Hierarchical JSON structure
 */
function convertToJSON(data) {
  // Remove header row
  const rows = data.slice(1);
  
  // Group by folder path
  const folderMap = {};
  rows.forEach(row => {
    const folderPath = row[5]; // Folder Path column
    const folderName = row[4]; // Folder Name column
    const fileName = row[1];   // Name column
    const fileLink = row[3];   // Link column
    const fileType = row[2];   // Type column
    
    if (!folderMap[folderPath]) {
      folderMap[folderPath] = {
        title: folderName,
        content: []
      };
    }
    
    if (fileType === "file") {
      folderMap[folderPath].content.push({
        type: "link",
        title: fileName,
        url: fileLink,
        target: "_blank"
      });
    }
  });
  
  // Build hierarchy
  const hierarchy = buildHierarchy(folderMap);
  
  // Create final JSON structure
  const result = {
    title: "Resource Collection SOP",
    description: "Comprehensive collection of links and resources organized by category",
    sections: hierarchy
  };
  
  return result;
}

/**
 * Builds hierarchical structure from flat folder map
 * @param {Object} folderMap - Flat map of folders
 * @returns {Array} Hierarchical structure
 */
function buildHierarchy(folderMap) {
  const root = {};
  const paths = Object.keys(folderMap).sort();
  
  // Build tree structure
  paths.forEach(path => {
    const parts = path.split("/");
    let current = root;
    
    parts.forEach((part, index) => {
      if (!current[part]) {
        current[part] = {
          title: part,
          content: [],
          subsections: {}
        };
      }
      
      if (index === parts.length - 1) {
        // This is the final folder, add content
        current[part].content = folderMap[path].content;
      } else {
        current = current[part].subsections;
      }
    });
  });
  
  // Convert tree to array format
  return convertTreeToArray(root);
}

/**
 * Converts tree structure to array format
 * @param {Object} tree - Tree structure
 * @returns {Array} Array format
 */
function convertTreeToArray(tree) {
  return Object.values(tree).map(node => {
    const result = {
      title: node.title,
      content: node.content
    };
    
    const subsections = convertTreeToArray(node.subsections);
    if (subsections.length > 0) {
      result.subsections = subsections;
    }
    
    return result;
  });
}

/**
 * Clears all data from sheets
 */
function clearData() {
  const ui = SpreadsheetApp.getUi();
  const response = ui.alert(
    "Clear Data",
    "Are you sure you want to clear all extracted data?",
    ui.ButtonSet.YES_NO
  );
  
  if (response == ui.Button.YES) {
    const ss = SpreadsheetApp.getActiveSpreadsheet();
    
    // Clear data sheet
    const dataSheet = ss.getSheetByName(SHEET_NAME);
    if (dataSheet) {
      dataSheet.clear();
    }
    
    // Clear JSON sheet
    const jsonSheet = ss.getSheetByName(JSON_SHEET_NAME);
    if (jsonSheet) {
      jsonSheet.clear();
    }
    
    ui.alert("All data cleared successfully.");
  }
}

/**
 * Test function for development
 */
function testExtraction() {
  // Test with a known folder ID
  const testFolderId = "1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms"; // Replace with actual test folder ID
  try {
    const folder = DriveApp.getFolderById(testFolderId);
    const data = extractFolderData(folder);
    Logger.log(`Extracted ${data.length} items`);
    Logger.log(JSON.stringify(data.slice(0, 3), null, 2)); // Log first 3 items
  } catch (error) {
    Logger.log(`Test error: ${error.message}`);
  }
}