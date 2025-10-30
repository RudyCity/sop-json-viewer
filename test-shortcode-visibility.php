<?php
/**
 * Test file for shortcode default visibility feature
 * This file demonstrates how to use the new default_visibility parameter
 */

// Include the plugin files for testing
require_once 'sop-json-viewer.php';

// Test data - simulate saved SOP data with per-section visibility
$sop_data = array(
    'title' => 'Test Per-Section Visibility SOP',
    'description' => 'Testing the new per-section visibility feature for accordion sections',
    'sections' => array(
        array(
            'title' => 'Introduction Section (Always Expanded)',
            'content' => 'This section is set to expanded=true, so it will always be open regardless of global settings.',
            'expanded' => true
        ),
        array(
            'title' => 'Second Section (Always Collapsed)',
            'content' => 'This section is set to expanded=false, so it will always be closed regardless of global settings.',
            'expanded' => false
        ),
        array(
            'title' => 'Third Section (Default Behavior)',
            'content' => 'This section has no expanded property, so it follows the global default_visibility setting.'
        ),
        array(
            'title' => 'Fourth Section with Links',
            'content' => array(
                array(
                    'type' => 'link',
                    'title' => 'Documentation',
                    'url' => 'https://example.com/docs',
                    'target' => '_blank'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Support',
                    'url' => 'https://example.com/support',
                    'target' => '_blank'
                )
            ),
            'expanded' => true
        )
    )
);

// Save test data
update_option('sjp_sop_data_test-visibility', $sop_data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Shortcode Default Visibility - SOP JSON Viewer</title>
    <link rel="stylesheet" href="assets/css/sop-accordion.css">
    <style>
        .test-section {
            margin: 40px 0;
            padding: 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
        }
        .test-section h2 {
            color: #1f2937;
            margin-top: 0;
        }
        .shortcode-example {
            background: #f9fafb;
            padding: 12px;
            border-radius: 4px;
            font-family: monospace;
            margin: 10px 0;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <h1>Test Shortcode Default Visibility Feature</h1>

    <div class="test-section">
        <h2>Default Hidden (Collapsed) - Using Setting</h2>
        <p>This uses the default setting from admin (should be hidden).</p>
        <div class="shortcode-example">[sop-accordion id="test-visibility"]</div>
        <?php echo do_shortcode('[sop-accordion id="test-visibility"]'); ?>
    </div>

    <div class="test-section">
        <h2>Explicitly Hidden</h2>
        <p>This explicitly sets default_visibility to "hidden".</p>
        <div class="shortcode-example">[sop-accordion id="test-visibility" default_visibility="hidden"]</div>
        <?php echo do_shortcode('[sop-accordion id="test-visibility" default_visibility="hidden"]'); ?>
    </div>

    <div class="test-section">
        <h2>Explicitly Shown (Expanded)</h2>
        <p>This explicitly sets default_visibility to "shown" - first section should be expanded.</p>
        <div class="shortcode-example">[sop-accordion id="test-visibility" default_visibility="shown"]</div>
        <?php echo do_shortcode('[sop-accordion id="test-visibility" default_visibility="shown"]'); ?>
    </div>

    <div class="test-section">
        <h2>With Custom Class</h2>
        <p>This shows how to combine default_visibility with custom CSS classes.</p>
        <div class="shortcode-example">[sop-accordion id="test-visibility" default_visibility="shown" class="custom-accordion"]</div>
        <?php echo do_shortcode('[sop-accordion id="test-visibility" default_visibility="shown" class="custom-accordion"]'); ?>
    </div>

    <div class="test-section">
        <h2>Per-Section Visibility Override</h2>
        <p>This demonstrates how per-section expanded properties override the global default_visibility setting.</p>
        <div class="shortcode-example">[sop-accordion id="test-visibility" default_visibility="hidden"]</div>
        <p><strong>Note:</strong> Even though default_visibility is "hidden", sections with expanded=true will still be open, and sections with expanded=false will still be closed.</p>
        <?php echo do_shortcode('[sop-accordion id="test-visibility" default_visibility="hidden"]'); ?>
    </div>

    <script src="assets/js/sop-accordion.js"></script>
</body>
</html>