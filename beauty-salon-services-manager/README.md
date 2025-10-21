# Beauty Salon Services Manager

A comprehensive WordPress plugin that enables beauty salons and laser epilation clinics to manage and display their services in organized groups with full Elementor integration.

## Description

The Beauty Salon Services Manager plugin provides a complete solution for beauty salons, spas, and laser epilation clinics to showcase their services on their WordPress website. With seamless Elementor integration, you can create beautiful service grids that are fully customizable and responsive.

## Features

- **Custom Post Type**: Dedicated service post type with custom fields for time, price, and additional notes
- **Service Groups**: Organize services into hierarchical categories (e.g., Facial Treatments, Laser Epilation, Massage)
- **Elementor Widget**: Beautiful, customizable services grid widget with extensive styling options
- **Responsive Design**: Mobile-first approach ensures your services look great on all devices
- **Flexible Display Options**: Choose what to show/hide - images, titles, descriptions, time, price, buttons
- **Advanced Styling**: Complete control over layout, colors, typography, spacing, and more
- **Performance Optimized**: Cached queries, lazy loading, and minified assets
- **Developer Friendly**: Extensive hooks and filters for customization
- **Translation Ready**: Full support for multilingual sites (WPML, Polylang)
- **RTL Support**: Right-to-left language support built-in

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- Elementor (Free or Pro version)

## Installation

### Automatic Installation

1. Log in to your WordPress admin panel
2. Navigate to **Plugins > Add New**
3. Search for "Beauty Salon Services Manager"
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Download the plugin zip file
2. Log in to your WordPress admin panel
3. Navigate to **Plugins > Add New > Upload Plugin**
4. Choose the downloaded zip file and click **Install Now**
5. Activate the plugin

### From Source

1. Clone or download this repository
2. Upload the `beauty-salon-services-manager` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress

## Quick Start Guide

### Step 1: Add Service Groups

1. Go to **Beauty Services > Service Groups**
2. Add your service categories (e.g., "Facial Treatments", "Laser Epilation", "Massage Therapy")
3. Optionally add descriptions to each group

### Step 2: Create Services

1. Go to **Beauty Services > Add New Service**
2. Enter the service title
3. Add a detailed description in the content editor
4. Set a featured image
5. Fill in the Service Details:
   - **Time Duration**: e.g., "30 minutes" or "1 hour"
   - **Price**: e.g., "$50.00" or "€45"
   - **Additional Notes**: Any extra information
6. Assign the service to one or more Service Groups
7. Click **Publish**

### Step 3: Display Services with Elementor

1. Edit any page with Elementor
2. Search for "Beauty Services Grid" widget
3. Drag the widget to your desired location
4. Configure the widget settings:
   - **Service Selection**: Choose how to query services
   - **Display Options**: Toggle what information to show
   - **Style Tab**: Customize the appearance
5. Click **Update** to save your page

## Elementor Widget Settings

### Content Tab

#### Service Selection
- **Query Type**: All Services, By Service Group, or Manual Selection
- **Service Groups**: Select specific groups to display (when using "By Service Group")
- **Manual Services**: Pick individual services (when using "Manual Selection")
- **Number of Services**: How many services to display (-1 for all)
- **Order By**: Date, Title, Menu Order, or Random
- **Order**: Ascending or Descending

#### Display Options
- **Show Service Image**: Toggle service featured images
- **Show Service Title**: Toggle service titles
- **Show Service Description**: Toggle service descriptions
- **Description Length**: Number of words to show in excerpt
- **Show Time**: Toggle time duration display
- **Show Price**: Toggle price display
- **Show Read More Button**: Toggle the call-to-action button
- **Button Text**: Customize the button text

### Style Tab

#### Layout
- **Columns**: Responsive column settings (Desktop, Tablet, Mobile)
- **Column Gap**: Space between columns
- **Row Gap**: Space between rows

#### Service Card
- **Background Color**: Card background
- **Border**: Border style, width, and color
- **Border Radius**: Rounded corners
- **Box Shadow**: Shadow effects
- **Padding**: Inner spacing

#### Image
- **Image Height**: Fixed height for images
- **Object Fit**: How images fill their container
- **Border Radius**: Rounded corners for images

#### Title
- **Typography**: Font family, size, weight, etc.
- **Color**: Text color
- **Alignment**: Left, Center, or Right
- **Margin**: Spacing around titles

#### Description
- **Typography**: Font settings
- **Color**: Text color
- **Alignment**: Text alignment

#### Meta (Time/Price)
- **Typography**: Font settings
- **Color**: Text and icon color
- **Show Icons**: Toggle FontAwesome icons

#### Button
- **Typography**: Font settings
- **Normal State**: Text and background colors
- **Hover State**: Hover effects
- **Border**: Border settings
- **Border Radius**: Rounded corners
- **Padding**: Button spacing

## Hooks & Filters for Developers

### Action Hooks

```php
// Before service card content
do_action('bslm_before_service_content', $post_id);

// After service card content
do_action('bslm_after_service_content', $post_id);

// In service meta area
do_action('bslm_service_meta_display', $post_id);

// After single service content
do_action('bslm_after_single_service', $post_id);
```

### Filter Hooks

```php
// Modify WP_Query arguments
$args = apply_filters('bslm_service_query_args', $args, $settings);

// Add custom CSS classes to service cards
$classes = apply_filters('bslm_service_card_classes', $classes, $post_id);

// Change excerpt length
$length = apply_filters('bslm_service_excerpt_length', 20, $post_id);

// Format price display
$price = apply_filters('bslm_service_price_format', $price, $post_id);

// Customize button text
$text = apply_filters('bslm_service_button_text', $text, $post_id);
```

### Example: Custom Price Format

```php
add_filter('bslm_service_price_format', 'my_custom_price_format', 10, 2);
function my_custom_price_format($price, $post_id) {
    // Add "Starting at" prefix
    return 'Starting at ' . $price;
}
```

### Example: Add Custom Meta

```php
add_action('bslm_service_meta_display', 'my_custom_service_meta', 10, 1);
function my_custom_service_meta($post_id) {
    $custom_field = get_post_meta($post_id, '_my_custom_field', true);
    if ($custom_field) {
        echo '<span class="my-custom-meta">' . esc_html($custom_field) . '</span>';
    }
}
```

## Template Override

You can override plugin templates by copying them to your theme:

**Plugin Template Location:**
```
wp-content/plugins/beauty-salon-services-manager/templates/service-card.php
```

**Your Theme Override Location:**
```
wp-content/themes/your-theme/beauty-salon-services-manager/service-card.php
```

## Styling Customization

### Custom CSS Classes

All service cards have the following classes you can target:

```css
.bslm-service-card { }           /* Service card container */
.bslm-service-image { }          /* Image wrapper */
.bslm-service-content { }        /* Content wrapper */
.bslm-service-title { }          /* Title */
.bslm-service-description { }    /* Description */
.bslm-service-meta { }           /* Meta container */
.bslm-service-time { }           /* Time display */
.bslm-service-price { }          /* Price display */
.bslm-service-button { }         /* CTA button */
```

### Example: Custom Card Hover Effect

```css
.bslm-service-card:hover {
    transform: scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}
```

## Frequently Asked Questions

### Can I use this plugin without Elementor?

While the plugin provides an Elementor widget for easy display, you can still use the custom post type and create your own templates or use WordPress blocks to display services.

### How do I add icons to time and price?

Icons are automatically added when you enable "Show Icons" in the Meta styling section. The plugin uses FontAwesome icons by default.

### Can I translate the plugin?

Yes! The plugin is fully translation-ready. You can use WPML, Polylang, or standard .po/.mo files for translation.

### How do I change the number of columns on mobile?

In the Elementor widget Style > Layout section, you'll find responsive controls for Columns. Click the device icons to set different column counts for Desktop, Tablet, and Mobile.

### Can I add custom fields to services?

Yes! You can use WordPress custom fields or add your own meta boxes. Use the provided hooks to display custom data.

## Changelog

### Version 1.0.0
- Initial release
- Custom service post type
- Service groups taxonomy
- Elementor widget integration
- Responsive grid layout
- Comprehensive styling options
- Admin interface
- Template system
- Hooks and filters

## Support

For bug reports and feature requests, please use the [GitHub issue tracker](https://github.com/yourusername/beauty-salon-services-manager/issues).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This plugin is licensed under the GPL v2 or later.

```
Copyright (C) 2025

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

## Credits

Developed by [Your Name]

## Links

- [Documentation](https://yourwebsite.com/docs)
- [Support Forum](https://yourwebsite.com/support)
- [GitHub Repository](https://github.com/yourusername/beauty-salon-services-manager)
