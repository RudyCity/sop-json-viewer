<?php
/**
 * SOP JSON Viewer - Link Content Template
 * 
 * This file contains a template for creating SOPs with link content.
 * It can be used as a starting point for creating new SOPs with link lists.
 */

// Link Content Template JSON
$link_content_template = array(
    'title' => 'Resource Collection SOP',
    'description' => 'Comprehensive collection of links and resources organized by category',
    'sections' => array(
        array(
            'title' => '📚 Documentation & References',
            'sort' => true,
            'sort_by' => 'title',
            'sort_order' => 'asc',
            'content' => array(
                array(
                    'type' => 'link',
                    'title' => 'Training Materials',
                    'url' => '/training-materials'
                ),
                array(
                    'type' => 'link',
                    'title' => 'User Manual PDF',
                    'url' => '/wp-content/uploads/user-manual.pdf',
                    'target' => '_blank'
                ),
                array(
                    'type' => 'link',
                    'title' => 'API Documentation',
                    'url' => 'https://docs.example.com/api',
                    'target' => '_blank'
                )
            )
        ),
        array(
            'title' => '🔗 Important Links',
            'content' => array(
                array(
                    'type' => 'link',
                    'title' => 'Company Website',
                    'url' => 'https://company-website.com',
                    'target' => '_blank'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Employee Portal',
                    'url' => 'https://portal.company.com',
                    'target' => '_blank'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Internal Wiki',
                    'url' => '/wiki'
                )
            )
        ),
        array(
            'title' => '🛠️ Tools & Software',
            'content' => array(
                array(
                    'type' => 'link',
                    'title' => 'Project Management Tool',
                    'url' => 'https://pm-tool.company.com',
                    'target' => '_blank'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Time Tracking System',
                    'url' => '/time-tracking'
                )
            ),
            'subsections' => array(
                array(
                    'title' => 'Access Information',
                    'content' => '<p>For tool access, contact IT support or use your company credentials.</p>'
                ),
                array(
                    'title' => 'Additional Resources',
                    'content' => array(
                        array(
                            'type' => 'link',
                            'title' => 'Video Tutorials',
                            'url' => '/tutorials'
                        ),
                        array(
                            'type' => 'link',
                            'title' => 'FAQ Section',
                            'url' => '/faq'
                        )
                    )
                )
            )
        ),
        array(
            'title' => '📞 Support & Contacts',
            'content' => array(
                array(
                    'type' => 'link',
                    'title' => 'IT Helpdesk',
                    'url' => '/helpdesk/it'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Emergency Contacts',
                    'url' => '/emergency-contacts'
                ),
                array(
                    'type' => 'link',
                    'title' => 'Submit Support Ticket',
                    'url' => '/support/ticket'
                )
            )
        )
    )
);

// Function to get the template
function get_sop_link_content_template() {
    global $link_content_template;
    return $link_content_template;
}

// Function to display the template as JSON for copying
function display_sop_link_content_template() {
    $template = get_sop_link_content_template();
    echo '<pre><code>' . esc_html(json_encode($template, JSON_PRETTY_PRINT)) . '</code></pre>';
}

// Example usage in a WordPress context
function register_sop_link_template() {
    // This function can be hooked to WordPress to register the template
    // Implementation depends on your specific WordPress setup
}
add_action('init', 'register_sop_link_template');

// Example of how to use the template in a shortcode
function sop_link_template_shortcode($atts) {
    $template = get_sop_link_content_template();
    
    // You can modify the template here based on shortcode attributes
    if (isset($atts['title'])) {
        $template['title'] = sanitize_text_field($atts['title']);
    }
    
    // Generate the SOP accordion using the template
    $sop_viewer = new SOP_JSON_Viewer();
    return $sop_viewer->render_sop_accordion(array('id' => 'link-template'));
}
add_shortcode('sop-link-template', 'sop_link_template_shortcode');

?>