<?php
/**
 * Register custom post types.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Post_Types {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter('post_type_link', array($this, 'service_permalink'), 10, 2);
    }

    /**
     * Register custom post types.
     */
    public function register_post_types() {
        $labels = array(
            'name'                  => _x('Services', 'Post Type General Name', 'beauty-salon-services-manager'),
            'singular_name'         => _x('Service', 'Post Type Singular Name', 'beauty-salon-services-manager'),
            'menu_name'             => __('Beauty Services', 'beauty-salon-services-manager'),
            'name_admin_bar'        => __('Service', 'beauty-salon-services-manager'),
            'archives'              => __('Service Archives', 'beauty-salon-services-manager'),
            'attributes'            => __('Service Attributes', 'beauty-salon-services-manager'),
            'parent_item_colon'     => __('Parent Service:', 'beauty-salon-services-manager'),
            'all_items'             => __('All Services', 'beauty-salon-services-manager'),
            'add_new_item'          => __('Add New Service', 'beauty-salon-services-manager'),
            'add_new'               => __('Add New', 'beauty-salon-services-manager'),
            'new_item'              => __('New Service', 'beauty-salon-services-manager'),
            'edit_item'             => __('Edit Service', 'beauty-salon-services-manager'),
            'update_item'           => __('Update Service', 'beauty-salon-services-manager'),
            'view_item'             => __('View Service', 'beauty-salon-services-manager'),
            'view_items'            => __('View Services', 'beauty-salon-services-manager'),
            'search_items'          => __('Search Service', 'beauty-salon-services-manager'),
            'not_found'             => __('Not found', 'beauty-salon-services-manager'),
            'not_found_in_trash'    => __('Not found in Trash', 'beauty-salon-services-manager'),
            'featured_image'        => __('Featured Image', 'beauty-salon-services-manager'),
            'set_featured_image'    => __('Set featured image', 'beauty-salon-services-manager'),
            'remove_featured_image' => __('Remove featured image', 'beauty-salon-services-manager'),
            'use_featured_image'    => __('Use as featured image', 'beauty-salon-services-manager'),
            'insert_into_item'      => __('Insert into service', 'beauty-salon-services-manager'),
            'uploaded_to_this_item' => __('Uploaded to this service', 'beauty-salon-services-manager'),
            'items_list'            => __('Services list', 'beauty-salon-services-manager'),
            'items_list_navigation' => __('Services list navigation', 'beauty-salon-services-manager'),
            'filter_items_list'     => __('Filter services list', 'beauty-salon-services-manager'),
        );

        $args = array(
            'label'                 => __('Service', 'beauty-salon-services-manager'),
            'description'           => __('Beauty and laser epilation services', 'beauty-salon-services-manager'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields', 'elementor'),
            'taxonomies'            => array('bslm_service_group'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-heart',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'services',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'services',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
            'rewrite'               => array(
                'slug'       => '%bslm_service_group%',
                'with_front' => false,
            ),
        );

        register_post_type('bslm_service', $args);

        // Enable Elementor support for this post type
        $this->enable_elementor_support();
    }

    /**
     * Enable Elementor support for the service post type.
     */
    public function enable_elementor_support() {
        // Add post type to Elementor supported post types
        add_filter('elementor/utils/get_public_post_types', array($this, 'add_elementor_cpt_support'));

        // Enable Elementor for archives
        add_action('elementor/theme/register_locations', array($this, 'register_elementor_locations'));
    }

    /**
     * Add service post type to Elementor CPT support.
     *
     * @param array $post_types Supported post types.
     * @return array
     */
    public function add_elementor_cpt_support($post_types) {
        $post_types['bslm_service'] = 'bslm_service';
        return $post_types;
    }

    /**
     * Register Elementor locations for archives.
     *
     * @param object $elementor_theme_manager Elementor theme manager.
     */
    public function register_elementor_locations($elementor_theme_manager) {
        // Archive location is already registered by Elementor
        // We just need to ensure our post type archives are supported
        // This is handled by Elementor Pro automatically when has_archive is true
    }

    /**
     * Customize service permalink to include taxonomy term.
     *
     * @param string  $post_link The post's permalink.
     * @param WP_Post $post      The post object.
     * @return string
     */
    public function service_permalink($post_link, $post) {
        // Only apply to our service post type
        if ($post->post_type !== 'bslm_service') {
            return $post_link;
        }

        // Check if the permalink contains our placeholder
        if (strpos($post_link, '%bslm_service_group%') === false) {
            return $post_link;
        }

        // Get the terms for this post
        $terms = get_the_terms($post->ID, 'bslm_service_group');

        if (!empty($terms) && !is_wp_error($terms)) {
            // Use the first term (or primary term if using Yoast SEO)
            $term = array_shift($terms);
            $post_link = str_replace('%bslm_service_group%', $term->slug, $post_link);
        } else {
            // No term assigned, use 'uncategorized'
            $post_link = str_replace('%bslm_service_group%', 'uncategorized', $post_link);
        }

        return $post_link;
    }
}
