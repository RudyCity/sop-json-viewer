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