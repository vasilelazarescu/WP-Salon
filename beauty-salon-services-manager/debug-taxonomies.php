<?php
/**
 * Debug file to check if taxonomies are registered.
 * Access this file via: your-site.com/wp-content/plugins/beauty-salon-services-manager/debug-taxonomies.php
 */

// Load WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('You must be an administrator to view this page.');
}

echo '<h1>Beauty Salon Services Manager - Taxonomy Debug</h1>';

// Check if taxonomies are registered
$taxonomies = get_taxonomies(array(), 'objects');

echo '<h2>All Registered Taxonomies:</h2>';
echo '<ul>';
foreach ($taxonomies as $taxonomy) {
    echo '<li><strong>' . esc_html($taxonomy->name) . '</strong> - ' . esc_html($taxonomy->label) . '</li>';
}
echo '</ul>';

echo '<h2>Service-Related Taxonomies:</h2>';

// Check for our specific taxonomies
$service_group = get_taxonomy('bslm_service_group');
$service_tag = get_taxonomy('bslm_service_tag');

echo '<h3>bslm_service_group:</h3>';
if ($service_group) {
    echo '<pre>';
    echo 'Name: ' . esc_html($service_group->name) . "\n";
    echo 'Label: ' . esc_html($service_group->label) . "\n";
    echo 'Show UI: ' . ($service_group->show_ui ? 'Yes' : 'No') . "\n";
    echo 'Show Admin Column: ' . ($service_group->show_admin_column ? 'Yes' : 'No') . "\n";
    echo 'Hierarchical: ' . ($service_group->hierarchical ? 'Yes' : 'No') . "\n";
    echo '</pre>';
} else {
    echo '<p style="color: red;">NOT REGISTERED</p>';
}

echo '<h3>bslm_service_tag:</h3>';
if ($service_tag) {
    echo '<pre>';
    echo 'Name: ' . esc_html($service_tag->name) . "\n";
    echo 'Label: ' . esc_html($service_tag->label) . "\n";
    echo 'Show UI: ' . ($service_tag->show_ui ? 'Yes' : 'No') . "\n";
    echo 'Show Admin Column: ' . ($service_tag->show_admin_column ? 'Yes' : 'No') . "\n";
    echo 'Hierarchical: ' . ($service_tag->hierarchical ? 'Yes' : 'No') . "\n";
    echo '</pre>';
} else {
    echo '<p style="color: red;">NOT REGISTERED</p>';
}

// Check which post types they're registered to
echo '<h2>Post Type Associations:</h2>';
$service_post_type = get_post_type_object('bslm_service');
if ($service_post_type) {
    echo '<h3>bslm_service post type:</h3>';
    $object_taxonomies = get_object_taxonomies('bslm_service', 'objects');
    echo '<ul>';
    foreach ($object_taxonomies as $tax) {
        echo '<li>' . esc_html($tax->name) . ' - ' . esc_html($tax->label) . '</li>';
    }
    echo '</ul>';
} else {
    echo '<p style="color: red;">bslm_service post type NOT REGISTERED</p>';
}

// Check if main class is loaded
echo '<h2>Class Status:</h2>';
echo '<p>BSLM_Taxonomies class exists: ' . (class_exists('BSLM_Taxonomies') ? 'Yes' : 'No') . '</p>';
echo '<p>Beauty_Salon_Services_Manager class exists: ' . (class_exists('Beauty_Salon_Services_Manager') ? 'Yes' : 'No') . '</p>';

// Check constants
echo '<h2>Plugin Constants:</h2>';
echo '<pre>';
echo 'BSLM_VERSION: ' . (defined('BSLM_VERSION') ? BSLM_VERSION : 'NOT DEFINED') . "\n";
echo 'BSLM_PLUGIN_DIR: ' . (defined('BSLM_PLUGIN_DIR') ? BSLM_PLUGIN_DIR : 'NOT DEFINED') . "\n";
echo '</pre>';
