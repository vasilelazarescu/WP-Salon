<?php
/**
 * Register custom post types.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Post_Types {

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
            'supports'              => array('title', 'editor', 'thumbnail', 'custom-fields'),
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
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rest_base'             => 'services',
            'rest_controller_class' => 'WP_REST_Posts_Controller',
        );

        register_post_type('bslm_service', $args);
    }
}
