# SOP JSON Viewer - Templates

This directory contains templates for creating different types of SOP content.

## Available Templates

### 1. Link Content Template (`link-content-template.php`)

This template demonstrates how to create SOPs with interactive link lists and resource collections.

#### Features

- Organized link collections by category
- Mixed content (links and HTML)
- Nested subsections with link lists
- External and internal link support
- Proper accessibility attributes

#### Usage

1. **In Admin Interface:**
   - Go to SOP JSON Viewer admin page
   - Click on "Templates" tab
   - Select "Link Collection" template
   - Customize as needed

2. **Programmatically:**
   ```php
   // Get the template
   $template = get_sop_link_content_template();
   
   // Modify if needed
   $template['title'] = 'My Custom SOP';
   
   // Save as SOP data
   update_option('sjp_sop_data_my-sop', $template);
   ```

3. **With Shortcode:**
   ```php
   // Display the template directly
   echo do_shortcode('[sop-link-template title="My Custom Title"]');
   ```

#### Structure

```json
{
  "title": "Resource Collection SOP",
  "description": "Comprehensive collection of links and resources",
  "sections": [
    {
      "title": "Section Title",
      "content": [
        {
          "type": "link",
          "title": "Link Title",
          "url": "https://example.com",
          "target": "_blank"
        }
      ]
    }
  ]
}
```

## Best Practices

### 1. Link Organization

- Group related links in logical sections
- Use descriptive titles for easy navigation
- Organize by priority or frequency of use

### 2. Mixed Content

- Combine link lists with HTML content for context
- Use subsections for complex hierarchies
- Provide descriptions for link categories

### 3. Accessibility

- Always include meaningful link titles
- Use appropriate target attributes
- Ensure color contrast for link text

### 4. URL Management

- Use absolute URLs for external resources
- Use relative URLs for internal resources
- Validate URLs before adding to template

## Customization

### Adding New Sections

```php
// Add a new section to the template
$template['sections'][] = array(
    'title' => 'New Section',
    'content' => array(
        array(
            'type' => 'link',
            'title' => 'New Link',
            'url' => '/new-page'
        )
    )
);
```

### Modifying Existing Links

```php
// Modify a specific link
$template['sections'][0]['content'][0]['title'] = 'Updated Title';
```

### Adding Subsections

```php
// Add subsections to a section
$template['sections'][0]['subsections'] = array(
    array(
        'title' => 'Subsection Title',
        'content' => array(
            array(
                'type' => 'link',
                'title' => 'Subsection Link',
                'url' => '/sub-page'
            )
        )
    )
);
```

## Integration with WordPress

### Custom Post Types

You can integrate these templates with custom post types:

```php
function sop_template_meta_box() {
    add_meta_box(
        'sop_template',
        'SOP Template',
        'display_sop_template_meta_box',
        'sop_post_type'
    );
}
add_action('add_meta_boxes', 'sop_template_meta_box');
```

### REST API

Expose templates via REST API:

```php
function register_sop_template_routes() {
    register_rest_route('sop-json-viewer/v1', '/templates/(?P<type>[a-zA-Z0-9-_]+)', array(
        'methods' => 'GET',
        'callback' => 'get_sop_template_api',
        'permission_callback' => '__return_true'
    ));
}
add_action('rest_api_init', 'register_sop_template_routes');
```

## Troubleshooting

### Common Issues

1. **Links Not Displaying:**
   - Check JSON structure is correct
   - Verify content is an array, not string
   - Ensure each item has "type": "link"

2. **External Links Not Working:**
   - Verify target attribute is set to "_blank"
   - Check URL is properly formatted
   - Ensure no security restrictions are blocking

3. **Styling Issues:**
   - Make sure CSS files are loaded
   - Check for conflicting styles
   - Verify class names are correct

### Debug Mode

Enable debug mode to troubleshoot:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check debug log for errors related to SOP templates.