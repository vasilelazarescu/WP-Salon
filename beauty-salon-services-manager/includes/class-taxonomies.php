<?php
/**
 * Register custom taxonomies.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Taxonomies {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('init', array($this, 'enable_elementor_taxonomy_support'), 20);
    }

    /**
     * Register custom taxonomies.
     */
    public function register_taxonomies() {
        $labels = array(
            'name'                       => _x('Service Groups', 'Taxonomy General Name', 'beauty-salon-services-manager'),
            'singular_name'              => _x('Service Group', 'Taxonomy Singular Name', 'beauty-salon-services-manager'),
            'menu_name'                  => __('Service Groups', 'beauty-salon-services-manager'),
            'all_items'                  => __('All Service Groups', 'beauty-salon-services-manager'),
            'parent_item'                => __('Parent Service Group', 'beauty-salon-services-manager'),
            'parent_item_colon'          => __('Parent Service Group:', 'beauty-salon-services-manager'),
            'new_item_name'              => __('New Service Group Name', 'beauty-salon-services-manager'),
            'add_new_item'               => __('Add New Service Group', 'beauty-salon-services-manager'),
            'edit_item'                  => __('Edit Service Group', 'beauty-salon-services-manager'),
            'update_item'                => __('Update Service Group', 'beauty-salon-services-manager'),
            'view_item'                  => __('View Service Group', 'beauty-salon-services-manager'),
            'separate_items_with_commas' => __('Separate groups with commas', 'beauty-salon-services-manager'),
            'add_or_remove_items'        => __('Add or remove groups', 'beauty-salon-services-manager'),
            'choose_from_most_used'      => __('Choose from the most used', 'beauty-salon-services-manager'),
            'popular_items'              => __('Popular Groups', 'beauty-salon-services-manager'),
            'search_items'               => __('Search Service Groups', 'beauty-salon-services-manager'),
            'not_found'                  => __('Not Found', 'beauty-salon-services-manager'),
            'no_terms'                   => __('No groups', 'beauty-salon-services-manager'),
            'items_list'                 => __('Groups list', 'beauty-salon-services-manager'),
            'items_list_navigation'      => __('Groups list navigation', 'beauty-salon-services-manager'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => false,
            'show_in_rest'               => true,
            'rest_base'                  => 'service-groups',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'rewrite'                    => array(
                'slug'         => 'service-category',
                'with_front'   => false,
                'hierarchical' => true,
            ),
        );

        register_taxonomy('bslm_service_group', array('bslm_service'), $args);

        // Register Service Tags taxonomy (non-hierarchical)
        $tag_labels = array(
            'name'                       => _x('Service Tags', 'Taxonomy General Name', 'beauty-salon-services-manager'),
            'singular_name'              => _x('Service Tag', 'Taxonomy Singular Name', 'beauty-salon-services-manager'),
            'menu_name'                  => __('Service Tags', 'beauty-salon-services-manager'),
            'all_items'                  => __('All Service Tags', 'beauty-salon-services-manager'),
            'new_item_name'              => __('New Service Tag Name', 'beauty-salon-services-manager'),
            'add_new_item'               => __('Add New Service Tag', 'beauty-salon-services-manager'),
            'edit_item'                  => __('Edit Service Tag', 'beauty-salon-services-manager'),
            'update_item'                => __('Update Service Tag', 'beauty-salon-services-manager'),
            'view_item'                  => __('View Service Tag', 'beauty-salon-services-manager'),
            'separate_items_with_commas' => __('Separate tags with commas', 'beauty-salon-services-manager'),
            'add_or_remove_items'        => __('Add or remove tags', 'beauty-salon-services-manager'),
            'choose_from_most_used'      => __('Choose from the most used', 'beauty-salon-services-manager'),
            'popular_items'              => __('Popular Tags', 'beauty-salon-services-manager'),
            'search_items'               => __('Search Service Tags', 'beauty-salon-services-manager'),
            'not_found'                  => __('Not Found', 'beauty-salon-services-manager'),
            'no_terms'                   => __('No tags', 'beauty-salon-services-manager'),
            'items_list'                 => __('Tags list', 'beauty-salon-services-manager'),
            'items_list_navigation'      => __('Tags list navigation', 'beauty-salon-services-manager'),
        );

        $tag_args = array(
            'labels'                     => $tag_labels,
            'hierarchical'               => false, // Non-hierarchical (tag-style)
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rest_base'                  => 'service-tags',
            'rest_controller_class'      => 'WP_REST_Terms_Controller',
            'rewrite'                    => array(
                'slug'         => 'service-tag',
                'with_front'   => false,
                'hierarchical' => false,
            ),
        );

        register_taxonomy('bslm_service_tag', array('bslm_service'), $tag_args);
    }

    /**
     * Enable Elementor support for taxonomy.
     */
    public function enable_elementor_taxonomy_support() {
        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Add Elementor CPT support filter for taxonomy
        add_filter('elementor/utils/get_the_archive_titles', array($this, 'add_archive_title'), 10, 1);
    }

    /**
     * Add custom archive titles for Elementor.
     *
     * @param array $titles Archive titles.
     * @return array
     */
    public function add_archive_title($titles) {
        if (is_tax('bslm_service_group') || is_tax('bslm_service_tag')) {
            $titles[] = single_term_title('', false);
        }
        return $titles;
    }
}
