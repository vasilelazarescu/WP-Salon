# CLAUDE.md - AI Assistant Guide for Beauty Salon Services Manager

**Last Updated:** 2025-11-28
**Plugin Version:** 1.0.0
**WordPress Minimum:** 6.0+
**PHP Minimum:** 7.4+

---

## Overview

This is a **WordPress plugin** for managing beauty salon and spa services. It provides:

- Hierarchical service management (parent/child services)
- Service categories with custom icons
- Elementor integration with two custom widgets
- Flexible frontend display options
- Complete admin interface for service management
- Shortcodes for custom templates
- REST API support
- Multilingual ready (WPML/Polylang)

**Plugin Prefix:** `bslm_` (Beauty SaLon Manager)
**Text Domain:** `beauty-salon-services-manager`

---

## Directory Structure

```
WP-Salon/
└── beauty-salon-services-manager/          # Main plugin directory
    ├── beauty-salon-services-manager.php   # Plugin entry point
    ├── README.md                           # User documentation
    ├── includes/                           # Core PHP classes
    │   ├── class-beauty-salon-services-manager.php  # Main orchestrator
    │   ├── class-loader.php                # Hook management system
    │   ├── class-activator.php             # Activation handler
    │   ├── class-deactivator.php           # Deactivation handler
    │   ├── class-post-types.php            # CPT registration
    │   ├── class-taxonomies.php            # Taxonomy registration
    │   ├── class-meta-boxes.php            # Admin meta fields
    │   ├── class-elementor-widget.php      # Elementor integration
    │   ├── class-template-loader.php       # Template system
    │   ├── class-shortcodes.php            # Shortcode handlers
    │   ├── class-taxonomy-meta.php         # Category icon meta
    │   ├── class-settings.php              # Admin settings page
    │   └── widgets/                        # Elementor widgets
    │       ├── class-beauty-services-grid-widget.php
    │       ├── class-service-meta-widget.php
    │       └── class-services-by-tag-widget.php
    ├── templates/                          # Frontend templates
    │   ├── single-service.php              # Service detail page
    │   ├── service-card.php                # Reusable card component
    │   └── category-template.php           # Category archive
    └── assets/                             # Static assets
        ├── css/
        │   ├── admin.css                   # Backend styles
        │   └── frontend.css                # Frontend styles
        └── js/
            ├── admin.js                    # Backend scripts
            ├── frontend.js                 # Frontend scripts
            └── taxonomy-icon-uploader.js   # Media uploader
```

---

## Key Concepts & Architecture

### Custom Post Type: `bslm_service`

**Purpose:** Represents individual salon services
**Features:**
- Hierarchical (supports parent/child relationships)
- Elementor support
- Featured image support
- REST API enabled
- Custom admin columns

**Meta Fields:**
```php
_bslm_service_time       // Service duration (e.g., "30 minutes")
_bslm_service_price      // Service price (e.g., "$50.00")
_bslm_service_notes      // Additional notes/description
_bslm_service_icon       // Icon image attachment ID
```

**URL Structure:**
- Archive: `/services/`
- Single: `/service/service-name/`
- Hierarchical: `/service/parent-service/child-service/`

**Hierarchical Structure:**
- Services can have parent services (e.g., "Facial Treatments" parent with "Anti-Aging Facial" child)
- Managed via `post_parent` field
- Quick edit support in admin
- Sortable by parent in admin list table

### Custom Taxonomy: `bslm_service_group`

**Purpose:** Categorize services into groups
**Features:**
- Hierarchical (like categories)
- Custom icon support via taxonomy meta
- REST API enabled
- Shown in admin column

**Meta Fields:**
```php
bslm_category_icon       // Icon attachment ID for the category
```

**URL Structure:**
- Archive: `/service-category/category-name/`
- Hierarchical: `/service-category/parent/child/`

### Custom Taxonomy: `bslm_service_tag`

**Purpose:** Tag services with keywords for flexible filtering
**Features:**
- Non-hierarchical (tag-style)
- REST API enabled
- Shown in admin column
- Tag cloud support
- Multiple tags per service

**URL Structure:**
- Archive: `/service-tag/tag-name/`

**Use Cases:**
- Filter services by characteristics (e.g., "anti-aging", "relaxing", "quick")
- Cross-category filtering
- Tag-based Elementor widget filtering

### Plugin Architecture Pattern

The plugin uses a **modular class-based architecture**:

```
Entry Point (beauty-salon-services-manager.php)
    ↓
Main Class (Beauty_Salon_Services_Manager)
    ↓
Loader (BSLM_Loader) - Hook Registry
    ↓
Component Classes (Post Types, Meta Boxes, Widgets, etc.)
    ↓
WordPress Hooks Execution
```

**Key Design Patterns:**
1. **Singleton-like bootstrap** - Single plugin instance
2. **Dependency injection** - Loader passed to components
3. **Hook registry** - Centralized action/filter management
4. **Template hierarchy** - WordPress template loading system
5. **Namespace avoidance** - Prefix-based naming for PHP 5.6 compatibility

---

## Constants & Globals

**Defined in:** `beauty-salon-services-manager.php`

```php
BSLM_VERSION         // Plugin version: '1.0.0'
BSLM_PLUGIN_DIR      // Full path to plugin directory
BSLM_PLUGIN_URL      // URL to plugin directory
BSLM_PLUGIN_BASENAME // Plugin basename for hooks
```

**WordPress Options:**
```php
bslm_version             // Stored version number
bslm_activated_time      // Timestamp of activation
bslm_settings            // Array of plugin settings
  ['category_template_page'] // Page ID for category template
```

---

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| **Classes** | `BSLM_ClassName` | `BSLM_Post_Types` |
| **Functions** | `bslm_function_name()` | `bslm_get_service_price()` |
| **Hooks** | `bslm_hook_name` | `bslm_before_service_content` |
| **Meta Keys** | `_bslm_meta_key` | `_bslm_service_price` |
| **CSS Classes** | `.bslm-class-name` | `.bslm-service-card` |
| **HTML IDs** | `#bslm-element-id` | `#bslm-service-grid` |
| **Taxonomy** | `bslm_taxonomy` | `bslm_service_group` |
| **Post Type** | `bslm_type` | `bslm_service` |

**Private meta fields** start with underscore (`_bslm_`) - hidden from custom fields UI
**Public meta fields** omit underscore - visible in custom fields UI

---

## Elementor Integration

### Widget 1: Beauty Services Grid

**File:** `includes/widgets/class-beauty-services-grid-widget.php`
**Widget Name:** `beauty-services-grid`
**Category:** Beauty Salon
**Use Case:** Display grids of services anywhere

**Key Features:**
- Multiple query types: All, By Parent, Parent Only, Children Only, Manual
- Responsive column controls
- Image display modes: Full, Small, Icon
- Card layouts: Vertical, Horizontal (Left/Right), Button-style
- Extensive styling controls
- Show/hide: Image, Title, Description, Time, Price, Button
- Custom button text and links

**Important Controls:**
```php
query_type          // all|by_parent|parent_only|children_only|manual
parent_id           // Parent service ID (conditional)
selected_services   // Manual service selection (conditional)
posts_per_page      // Number of services
columns             // Responsive (desktop/tablet/mobile)
image_mode          // full|small|icon
card_layout         // vertical|horizontal_left|horizontal_right|button
```

### Widget 2: Service Price & Time

**File:** `includes/widgets/class-service-meta-widget.php`
**Widget Name:** `service-meta`
**Category:** Beauty Salon
**Use Case:** Display price/time on single service pages

**Features:**
- Context-aware (single service pages only)
- Layout options: Horizontal, Vertical, Inline
- Show/hide price and time independently
- Icon and label controls
- Comprehensive styling options

**Important:** This widget only displays on `is_singular('bslm_service')` pages.

### Widget 3: Services By Tag

**File:** `includes/widgets/class-services-by-tag-widget.php`
**Widget Name:** `services-by-tag`
**Category:** Beauty Salon
**Use Case:** Display services filtered by tags

**Key Features:**
- Tag-based filtering with multi-select
- Tag operator: Match ANY (OR) or Match ALL (AND)
- Responsive column controls
- Show/hide: Image, Title, Description, Time, Price, Tags, Button
- Extensive styling controls
- Tag display on cards

**Important Controls:**
```php
selected_tags       // Array of tag IDs to filter
tag_operator        // IN|AND (match any or all tags)
posts_per_page      // Number of services
orderby             // date|title|menu_order|rand
columns             // Responsive (desktop/tablet/mobile)
show_tags           // Display tags on each card
```

**Usage Examples:**
- Create a "Quick Services" page showing all services tagged as "quick"
- Display "Anti-Aging Treatments" combining multiple service categories
- Show "Relaxing Services" across different service groups

### Elementor Pro Support

The plugin detects and uses Elementor Pro archive templates:
- Check for `ElementorPro\Plugin::instance()->modules_manager->get_modules('theme-builder')`
- Falls back to custom template page (from settings)
- Ultimate fallback to default category template

---

## Important Hooks & Filters

### Action Hooks

```php
// Frontend
bslm_before_service_content        // Before service card content
bslm_after_service_content         // After service card content
bslm_service_meta_display          // In service meta section
bslm_after_single_service          // After single service page

// WordPress Core (used by plugin)
init                               // CPT/taxonomy registration
add_meta_boxes                     // Meta box registration
save_post_bslm_service             // Save post meta
admin_enqueue_scripts              // Admin assets
wp_enqueue_scripts                 // Frontend assets
elementor/widgets/register         // Register Elementor widgets
```

### Filter Hooks

```php
bslm_service_query_args            // Modify WP_Query args (before query)
bslm_service_card_classes          // Add CSS classes to service cards
bslm_service_excerpt_length        // Change excerpt word count
bslm_service_price_format          // Format price display
bslm_service_button_text           // Change button text
manage_bslm_service_posts_columns  // Admin column headers
```

### Hook Usage Examples

```php
// Add custom meta to service cards
add_action('bslm_service_meta_display', function($post_id) {
    $rating = get_post_meta($post_id, '_service_rating', true);
    if ($rating) {
        echo '<div class="service-rating">' . esc_html($rating) . '</div>';
    }
}, 10, 1);

// Modify price display
add_filter('bslm_service_price_format', function($price, $post_id) {
    return 'Starting at ' . $price;
}, 10, 2);

// Add custom CSS classes
add_filter('bslm_service_card_classes', function($classes, $post_id) {
    if (has_term('featured', 'bslm_service_group', $post_id)) {
        $classes[] = 'featured-service';
    }
    return $classes;
}, 10, 2);

// Customize query
add_filter('bslm_service_query_args', function($args) {
    $args['orderby'] = 'menu_order';
    $args['order'] = 'ASC';
    return $args;
});
```

---

## Shortcodes

### Available Shortcodes

**File:** `includes/class-shortcodes.php`

```php
[bslm_category_title]
// Displays current category name
// Attributes: tag="h1|h2|h3" class="css-classes"
// Example: [bslm_category_title tag="h2" class="page-title"]

[bslm_category_description]
// Displays current category description
// Attributes: class="css-classes"
// Example: [bslm_category_description class="intro-text"]

[bslm_category_services]
// Displays services grid for current category
// Attributes:
//   columns="3"           # Number of columns
//   orderby="menu_order"  # Order by field
//   order="ASC"           # ASC or DESC
//   posts_per_page="12"   # Number of services
// Example: [bslm_category_services columns="3" orderby="title" order="ASC"]
```

### Shortcode Best Practices

1. **Use in Elementor templates** - Place shortcodes in Text Editor widgets
2. **Category context required** - These shortcodes work on category archive pages
3. **Page builder compatibility** - Works with Elementor Free (not just Pro)

---

## Development Workflows

### Adding a New Meta Field

1. **Add to meta boxes class** (`includes/class-meta-boxes.php`)
   ```php
   // In add_meta_boxes() method
   echo '<label>New Field:</label>';
   echo '<input type="text" name="bslm_new_field" value="' . esc_attr($value) . '">';
   ```

2. **Save the field** (in same class)
   ```php
   // In save_service_details() method
   if (isset($_POST['bslm_new_field'])) {
       update_post_meta($post_id, '_bslm_new_field', sanitize_text_field($_POST['bslm_new_field']));
   }
   ```

3. **Display in template** (`templates/single-service.php` or `service-card.php`)
   ```php
   $new_field = get_post_meta(get_the_ID(), '_bslm_new_field', true);
   if ($new_field) {
       echo '<div class="bslm-new-field">' . esc_html($new_field) . '</div>';
   }
   ```

4. **Add to Elementor widget** (optional, `includes/widgets/class-beauty-services-grid-widget.php`)
   ```php
   // In register_controls()
   $this->add_control('show_new_field', [
       'label' => __('Show New Field', 'beauty-salon-services-manager'),
       'type' => \Elementor\Controls_Manager::SWITCHER,
       'default' => 'yes',
   ]);

   // In render()
   if ($settings['show_new_field'] === 'yes') {
       // Display logic
   }
   ```

### Adding a New Elementor Widget

1. **Create widget file** in `includes/widgets/class-my-widget.php`
   ```php
   class BSLM_My_Widget extends \Elementor\Widget_Base {
       public function get_name() { return 'my-widget-id'; }
       public function get_title() { return 'My Widget Name'; }
       public function get_icon() { return 'eicon-gallery-grid'; }
       public function get_categories() { return ['beauty-salon']; }

       protected function register_controls() { /* ... */ }
       protected function render() { /* ... */ }
   }
   ```

2. **Register widget** in `includes/class-elementor-widget.php`
   ```php
   // In register_widgets() method
   require_once BSLM_PLUGIN_DIR . 'includes/widgets/class-my-widget.php';
   $widgets_manager->register(new \BSLM_My_Widget());
   ```

3. **Test in Elementor editor** - Widget appears in "Beauty Salon" category

### Adding Custom Templates

1. **Create template** in `templates/my-template.php`
2. **Load template** using `BSLM_Template_Loader::get_template()`
   ```php
   BSLM_Template_Loader::get_template('my-template.php', [
       'variable' => $value
   ]);
   ```
3. **Allow theme override** - Users can copy to `theme/beauty-salon-services-manager/my-template.php`

### Modifying Admin Columns

**File:** `includes/class-beauty-salon-services-manager.php`

```php
// Add column header
public function set_custom_columns($columns) {
    $columns['my_column'] = __('My Column', 'beauty-salon-services-manager');
    return $columns;
}

// Populate column content
public function custom_column_content($column, $post_id) {
    if ($column === 'my_column') {
        echo esc_html(get_post_meta($post_id, '_my_meta', true));
    }
}

// Make sortable
public function sortable_columns($columns) {
    $columns['my_column'] = 'my_meta_key';
    return $columns;
}
```

---

## Security Best Practices

### Input Sanitization

**Always sanitize user input:**

```php
// Text fields
$value = sanitize_text_field($_POST['field_name']);

// Textarea
$value = sanitize_textarea_field($_POST['textarea']);

// HTML content (allow safe HTML)
$value = wp_kses_post($_POST['content']);

// URLs
$value = esc_url_raw($_POST['url']);

// Integers
$value = absint($_POST['number']);

// Arrays
$value = array_map('sanitize_text_field', $_POST['array_field']);
```

### Output Escaping

**Always escape output:**

```php
// HTML content
echo esc_html($variable);

// Attributes
echo '<div class="' . esc_attr($class) . '">';

// URLs
echo '<a href="' . esc_url($url) . '">';

// Safe HTML (from wp_kses_post)
echo wp_kses_post($content);

// JavaScript
echo '<script>var value = ' . wp_json_encode($data) . ';</script>';
```

### Nonce Verification

**Always verify nonces on form submissions:**

```php
// Create nonce
wp_nonce_field('bslm_action_name', 'bslm_nonce_name');

// Verify nonce
if (!isset($_POST['bslm_nonce_name']) ||
    !wp_verify_nonce($_POST['bslm_nonce_name'], 'bslm_action_name')) {
    return;
}
```

### Permission Checks

**Always check user capabilities:**

```php
// Before saving
if (!current_user_can('edit_post', $post_id)) {
    return;
}

// Before displaying admin UI
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have permission.'));
}
```

### AJAX Security

```php
// Check nonce
check_ajax_referer('bslm_ajax_nonce', 'nonce');

// Check permissions
if (!current_user_can('edit_posts')) {
    wp_send_json_error('Permission denied');
}

// Sanitize and process
$data = sanitize_text_field($_POST['data']);
```

---

## Common Tasks & Solutions

### Task: Get All Services for a Category

```php
$args = array(
    'post_type' => 'bslm_service',
    'posts_per_page' => -1,
    'tax_query' => array(
        array(
            'taxonomy' => 'bslm_service_group',
            'field' => 'slug',
            'terms' => 'facial-treatments',
        ),
    ),
);
$services = new WP_Query($args);
```

### Task: Get Parent and Child Services

```php
// Get parent services only
$parents = new WP_Query([
    'post_type' => 'bslm_service',
    'post_parent' => 0,
]);

// Get children of a specific service
$children = new WP_Query([
    'post_type' => 'bslm_service',
    'post_parent' => $parent_id,
]);
```

### Task: Get Service Meta Data

```php
$post_id = get_the_ID();
$time = get_post_meta($post_id, '_bslm_service_time', true);
$price = get_post_meta($post_id, '_bslm_service_price', true);
$notes = get_post_meta($post_id, '_bslm_service_notes', true);
$icon_id = get_post_meta($post_id, '_bslm_service_icon', true);

// Get icon URL
if ($icon_id) {
    $icon_url = wp_get_attachment_image_url($icon_id, 'thumbnail');
}
```

### Task: Display Service Card Manually

```php
// Include template
BSLM_Template_Loader::get_template('service-card.php', array(
    'post_id' => get_the_ID(),
    'show_image' => true,
    'show_price' => true,
    'show_time' => true,
    'show_button' => true,
    'button_text' => __('Book Now', 'beauty-salon-services-manager'),
));
```

### Task: Check if Elementor is Active

```php
if (did_action('elementor/loaded')) {
    // Elementor is active
}

// Check for Elementor Pro
if (class_exists('ElementorPro\Plugin')) {
    // Elementor Pro is active
}
```

### Task: Programmatically Create a Service

```php
$service_id = wp_insert_post([
    'post_type' => 'bslm_service',
    'post_title' => 'New Service',
    'post_content' => 'Service description here',
    'post_status' => 'publish',
    'post_parent' => 0, // Or parent service ID
]);

// Add meta data
update_post_meta($service_id, '_bslm_service_time', '45 minutes');
update_post_meta($service_id, '_bslm_service_price', '$75.00');

// Assign to category
wp_set_object_terms($service_id, ['facial-treatments'], 'bslm_service_group');
```

---

## Debugging Tips

### Enable WordPress Debug Mode

```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Common Debug Locations

```php
// Write to debug log
error_log('BSLM Debug: ' . print_r($variable, true));

// Check hook execution
add_action('init', function() {
    error_log('BSLM: Init hook fired');
}, 999);

// Query debugging
add_action('pre_get_posts', function($query) {
    error_log('Query: ' . print_r($query->query_vars, true));
});
```

### Elementor Widget Not Appearing

1. Clear Elementor cache: Elementor > Tools > Regenerate Files
2. Check widget registration: `error_log()` in `register_widgets()`
3. Verify widget `get_categories()` returns `['beauty-salon']`
4. Check Elementor is loaded: `did_action('elementor/loaded')`

### Template Not Loading

1. Check template file exists in `templates/` directory
2. Verify `BSLM_Template_Loader::get_template()` is called correctly
3. Check theme override: Look in `theme/beauty-salon-services-manager/`
4. Enable WP_DEBUG to see template loading errors

### Meta Not Saving

1. Verify nonce is present and valid
2. Check `current_user_can('edit_post', $post_id)`
3. Ensure not an autosave: `if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;`
4. Confirm meta key has underscore prefix for private meta
5. Check `save_post_bslm_service` hook is firing

### Rewrite Rules Not Working (404 Errors)

```php
// Flush rewrite rules manually
// wp-admin > Settings > Permalinks > Save Changes

// Or programmatically (only during development)
flush_rewrite_rules();
```

---

## Testing Guidelines

### Manual Testing Checklist

**Admin Interface:**
- [ ] Create new service with all meta fields
- [ ] Upload service icon
- [ ] Assign to service group
- [ ] Set parent service (hierarchical)
- [ ] Quick edit parent service
- [ ] Check admin columns display correctly
- [ ] Verify settings page saves

**Frontend Display:**
- [ ] View single service page
- [ ] Check category archive page
- [ ] Test service card displays
- [ ] Verify image lazy loading
- [ ] Test responsive layouts
- [ ] Check button links

**Elementor Integration:**
- [ ] Beauty Services Grid widget appears in editor
- [ ] All query types work (all, by parent, manual, etc.)
- [ ] Service Meta widget appears on single pages
- [ ] Styling controls apply correctly
- [ ] Responsive controls work

**Shortcodes:**
- [ ] `[bslm_category_title]` displays correctly
- [ ] `[bslm_category_description]` shows description
- [ ] `[bslm_category_services]` renders grid
- [ ] Shortcode attributes work

### Browser Testing

- **Chrome/Edge** (latest)
- **Firefox** (latest)
- **Safari** (latest)
- **Mobile browsers** (iOS Safari, Chrome Android)

### Responsive Breakpoints

```css
Desktop:  > 1024px
Tablet:   768px - 1024px
Mobile:   < 768px
```

---

## Code Style Guidelines

### PHP Code Style

```php
// Class declaration
class BSLM_My_Class {

    // Properties
    private $loader;
    protected $version;

    // Constructor
    public function __construct() {
        // Code here
    }

    // Methods
    public function my_method($param1, $param2) {
        if ($param1 === 'value') {
            return $param2;
        }
        return false;
    }
}

// Function declaration
function bslm_my_function($arg) {
    // Code here
}

// Hooks
add_action('init', 'bslm_my_function');
add_filter('the_content', 'bslm_filter_content', 10, 1);
```

### JavaScript Code Style

```javascript
// Use strict mode
'use strict';

// jQuery wrapper
(function($) {
    $(document).ready(function() {
        // Code here
    });
})(jQuery);

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Code here
});
```

### CSS Code Style

```css
/* Component-based naming */
.bslm-service-card {
    /* Styles */
}

.bslm-service-card__title {
    /* BEM modifier */
}

.bslm-service-card--featured {
    /* BEM state */
}

/* Use CSS custom properties */
:root {
    --bslm-primary-color: #ff6b6b;
    --bslm-spacing: 30px;
}
```

---

## Performance Optimization

### Database Queries

```php
// GOOD - Efficient query
$services = new WP_Query([
    'post_type' => 'bslm_service',
    'posts_per_page' => 12,
    'fields' => 'ids', // If only IDs needed
]);

// BAD - Inefficient
$services = get_posts(['numberposts' => -1]); // Avoid -1 on large sites
```

### Asset Loading

```php
// Only load on relevant pages
function bslm_enqueue_scripts() {
    // Only on service pages
    if (is_singular('bslm_service') || is_post_type_archive('bslm_service')) {
        wp_enqueue_style('bslm-frontend');
        wp_enqueue_script('bslm-frontend');
    }
}
```

### Image Optimization

- Always use `the_post_thumbnail()` with appropriate size
- Enable lazy loading: `loading="lazy"`
- Use `srcset` for responsive images
- Optimize images before upload (external tools)

### Caching Considerations

```php
// Use transients for expensive operations
$cached = get_transient('bslm_expensive_query');
if (false === $cached) {
    $cached = /* expensive operation */;
    set_transient('bslm_expensive_query', $cached, HOUR_IN_SECONDS);
}

// Clear cache when needed
delete_transient('bslm_expensive_query');
```

---

## Git Workflow

### Branch Naming

- **Feature branches:** `claude/feature-description-sessionid`
- **Bug fixes:** `claude/fix-description-sessionid`
- **Current branch:** `claude/claude-md-mijhcbdb52o8hdxq-01Pao6ZwVXPm6cAmRUw5dhSS`

### Commit Messages

```
Good commit message format:
[Component] Action taken in present tense

Examples:
✅ Add Service Meta widget for single pages
✅ Fix hierarchical service query in grid widget
✅ Update category template to support Elementor Free
✅ Refactor meta box save logic for better validation

Bad examples:
❌ Updated stuff
❌ Bug fixes
❌ WIP
```

### Before Committing

1. Test changes manually
2. Check for PHP errors: `php -l filename.php`
3. Verify no debug code left (`error_log()`, `var_dump()`, etc.)
4. Ensure proper escaping and sanitization
5. Update relevant documentation

---

## Important Notes for AI Assistants

### DO:
✅ **Read files before modifying** - Never propose changes to unread code
✅ **Use existing patterns** - Follow established code structure
✅ **Maintain backwards compatibility** - Don't break existing functionality
✅ **Add hooks for extensibility** - Use actions/filters for customization points
✅ **Escape all output** - Use `esc_html()`, `esc_attr()`, `esc_url()`
✅ **Sanitize all input** - Use `sanitize_text_field()`, etc.
✅ **Verify nonces** - Check nonces on all form submissions
✅ **Check permissions** - Use `current_user_can()`
✅ **Use plugin constants** - `BSLM_PLUGIN_DIR`, `BSLM_PLUGIN_URL`, etc.
✅ **Follow naming conventions** - Prefix everything with `bslm_` or `BSLM_`
✅ **Internationalize strings** - Wrap in `__()`, `_e()`, `_x()`
✅ **Document complex logic** - Add comments explaining "why"
✅ **Test on single and archive pages** - Both contexts behave differently

### DON'T:
❌ **Don't skip security checks** - Never trust user input
❌ **Don't use global namespace** - Always prefix functions/classes
❌ **Don't hardcode URLs** - Use `BSLM_PLUGIN_URL` and WordPress functions
❌ **Don't modify WordPress core** - Work within plugin boundaries
❌ **Don't assume dependencies** - Check if Elementor/other plugins exist
❌ **Don't use short PHP tags** - Use `<?php` not `<?`
❌ **Don't commit debug code** - Remove `var_dump()`, `print_r()`, etc.
❌ **Don't forget translation** - All user-facing strings need text domain
❌ **Don't break theme compatibility** - Test with common themes
❌ **Don't use deprecated functions** - Check WordPress Codex

### When Adding Features:

1. **Check existing architecture** - Find similar features to model after
2. **Add hooks** - Allow developers to extend your feature
3. **Update templates** - If affecting frontend display
4. **Update Elementor widgets** - If relevant to widget controls
5. **Test with/without Elementor** - Ensure graceful degradation
6. **Consider performance** - Will this scale with 1000+ services?
7. **Mobile-first** - Always test responsive behavior
8. **Accessibility** - Add ARIA labels, keyboard navigation
9. **Document in code** - Add inline comments for complex logic

### Common Pitfalls:

1. **Forgetting is_singular() checks** - Some features only work on single pages
2. **Not checking post type** - Always verify `get_post_type() === 'bslm_service'`
3. **Infinite loops** - Be careful with `pre_get_posts` and WP_Query
4. **Missing theme styles** - Plugin relies on some theme CSS (normalize, etc.)
5. **Elementor caching** - Changes not appearing? Clear Elementor cache
6. **Rewrite rules** - After changing CPT/taxonomy, flush rules
7. **Nonce verification on autosave** - Check `DOING_AUTOSAVE` constant
8. **JavaScript conflicts** - Wrap in IIFE, use jQuery noConflict mode

---

## File Reference Quick Links

### Core Files (Most Frequently Modified)
- **Main class:** `includes/class-beauty-salon-services-manager.php`
- **Grid widget:** `includes/widgets/class-beauty-services-grid-widget.php`
- **Meta widget:** `includes/widgets/class-service-meta-widget.php`
- **Tag widget:** `includes/widgets/class-services-by-tag-widget.php`
- **Meta boxes:** `includes/class-meta-boxes.php`
- **Post types:** `includes/class-post-types.php`
- **Taxonomies:** `includes/class-taxonomies.php`
- **Frontend styles:** `assets/css/frontend.css`
- **Frontend scripts:** `assets/js/frontend.js`

### Template Files (User-Facing)
- **Single service:** `templates/single-service.php`
- **Service card:** `templates/service-card.php`
- **Category archive:** `templates/category-template.php`

### Configuration Files
- **Entry point:** `beauty-salon-services-manager.php`
- **Settings page:** `includes/class-settings.php`
- **Activation:** `includes/class-activator.php`

---

## Recent Changes & Architectural Decisions

**Recent Git History Highlights:**

1. **Tag-Based Taxonomy** - Added `bslm_service_tag` for flexible service tagging
2. **Services By Tag Widget** - New Elementor widget for tag-based filtering
3. **Hierarchical Service Structure** - Moved from flat to parent/child services
4. **Service Meta Widget** - Added dedicated widget for single pages (price/time display)
5. **Elementor Free Support** - Category templates work without Elementor Pro
6. **Image Display Modes** - Added flexible image sizing (full/small/icon)
7. **URL Structure Cleanup** - Removed `/services/` prefix from categories

**Key Architectural Decisions:**

- **Why hierarchical services?** - Salons offer service variations (e.g., Basic Facial vs. Anti-Aging Facial)
- **Why three Elementor widgets?** - Different use cases: Grid for general listings, Meta for single pages, Tags for cross-category filtering
- **Why tag taxonomy?** - Enables flexible cross-category filtering (e.g., "quick", "anti-aging", "relaxing")
- **Why shortcodes?** - Allow Elementor Free users to build custom category templates
- **Why custom template loader?** - Provides theme override capability
- **Why prefix with underscore on meta?** - Hides from Custom Fields UI (cleaner admin)

---

## Support & Resources

### WordPress Codex
- [Plugin API](https://developer.wordpress.org/plugins/)
- [Custom Post Types](https://developer.wordpress.org/plugins/post-types/)
- [Taxonomies](https://developer.wordpress.org/plugins/taxonomies/)
- [Metadata API](https://developer.wordpress.org/plugins/metadata/)

### Elementor Developer Docs
- [Creating Widgets](https://developers.elementor.com/docs/widgets/)
- [Widget Controls](https://developers.elementor.com/docs/controls/)
- [Dynamic Tags](https://developers.elementor.com/docs/dynamic-tags/)

### Plugin Resources
- **README:** `beauty-salon-services-manager/README.md`
- **Git Repository:** `/home/user/WP-Salon/`
- **Current Branch:** `claude/claude-md-mijhcbdb52o8hdxq-01Pao6ZwVXPm6cAmRUw5dhSS`

---

## Version History

**v1.0.0** (Current)
- Initial release
- Hierarchical services support
- Two Elementor widgets (Grid + Meta)
- Category icon support
- Shortcode system
- Elementor Free compatibility
- REST API support
- Translation ready

---

**Last Updated:** 2025-11-28
**Maintainer:** Beauty Salon Services Manager Team
**For AI Assistant Use:** This document should be referenced when working with this codebase to ensure consistency and adherence to established patterns.
