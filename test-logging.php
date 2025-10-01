<?php
/**
 * Simple test script to verify SOP JSON Viewer logging functionality
 * This script tests the logging implementation without requiring full WordPress setup
 */

// Mock WordPress functions for testing
function wp_verify_nonce($nonce, $action) {
    return true; // Mock successful verification
}

function current_user_can($capability) {
    return true; // Mock admin user
}

function sanitize_text_field($value) {
    return $value;
}

function wp_unslash($value) {
    return $value;
}

// json_decode is a built-in PHP function, no need to mock it

// json_last_error and json_last_error_msg are built-in PHP functions, no need to mock them

function update_option($option, $value) {
    // Simulate a save operation
    error_log("[TEST] update_option called with option: $option");
    return true; // Simulate successful save
}

function wp_cache_delete($key) {
    error_log("[TEST] wp_cache_delete called with key: $key");
}

function wp_send_json_success($data) {
    error_log("[TEST] wp_send_json_success called with: " . print_r($data, true));
    echo "SUCCESS: " . $data . "\n";
}

function wp_send_json_error($data) {
    error_log("[TEST] wp_send_json_error called with: " . print_r($data, true));
    echo "ERROR: " . $data . "\n";
}

function get_current_user_id() {
    return 1;
}

global $wpdb;
$wpdb = new stdClass();
$wpdb->last_error = 'No database error';
$wpdb->last_query = 'No query executed';

// Include the class with logging
require_once 'includes/class-sop-json-viewer.php';

// Test the logging functionality
echo "Testing SOP JSON Viewer logging functionality...\n";
echo "Check your PHP error log for detailed logging output.\n\n";

// Simulate AJAX POST data
$_POST = array(
    'nonce' => 'test_nonce',
    'sop_id' => 'test-sop-id',
    'sop_data' => json_encode(array(
        'title' => 'Test SOP Title',
        'description' => 'Test SOP Description',
        'sections' => array(
            array(
                'title' => 'Test Section',
                'content' => 'Test content'
            )
        )
    ))
);

// Create instance and test save
$sop_viewer = new SOP_JSON_Viewer();
$sop_viewer->ajax_save_sop_data();

echo "\nLogging test completed. Check your PHP error log for detailed output.\n";
echo "You should see log entries like:\n";
echo "- [SOP JSON Viewer] Save attempt - SOP ID: test-sop-id, Data size: ...\n";
echo "- [SOP JSON Viewer] Sanitized data - SOP ID: test-sop-id, Sections count: 1...\n";
echo "- [SOP JSON Viewer] Save successful - SOP ID: test-sop-id, Data size: ...\n";
?>