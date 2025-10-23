<?php
/**
 * Elementor Widget Integration.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Elementor_Widget {

    /**
     * Constructor.
     */
    public function __construct() {
        // Enable Elementor for archives
        add_action('init', array($this, 'enable_elementor_archive_support'));
    }

    /**
     * Register Elementor widgets.
     */
    public function register_widgets($widgets_manager) {
        // Check if Elementor is installed and activated
        if (!did_action('elementor/loaded')) {
            return;
        }

        require_once BSLM_PLUGIN_DIR . 'includes/widgets/class-beauty-services-grid-widget.php';

        $widgets_manager->register(new \BSLM_Beauty_Services_Grid_Widget());
    }

    /**
     * Add custom Elementor widget categories.
     */
    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'beauty-salon',
            array(
                'title' => __('Beauty Salon', 'beauty-salon-services-manager'),
                'icon' => 'fa fa-plug',
            )
        );
    }

    /**
     * Enable Elementor support for archive pages.
     */
    public function enable_elementor_archive_support() {
        // Check if Elementor is loaded
        if (!did_action('elementor/loaded')) {
            return;
        }

        // Add support for service post type archives
        add_filter('elementor/theme/need_override_location', array($this, 'enable_archive_override'), 10, 2);

        // Add archive templates to Elementor library
        add_action('elementor/documents/register', array($this, 'register_archive_document_type'));

        // Add taxonomy to Elementor Pro location conditions
        add_filter('elementor/theme/conditions/taxonomies', array($this, 'add_taxonomy_to_conditions'));
    }

    /**
     * Enable archive location override for service archives.
     *
     * @param bool   $need_override Whether to override the location.
     * @param string $location The location name.
     * @return bool
     */
    public function enable_archive_override($need_override, $location) {
        if ('archive' === $location) {
            if (is_post_type_archive('bslm_service') || is_tax('bslm_service_group')) {
                return true;
            }
        }
        return $need_override;
    }

    /**
     * Register custom archive document type for Elementor.
     *
     * @param object $documents_manager Elementor documents manager.
     */
    public function register_archive_document_type($documents_manager) {
        // This allows Elementor to recognize service archives
        // Archive documents are already registered by Elementor Pro
        // We just ensure our post type and taxonomy are included

        // Add support for service group taxonomy archives
        add_filter('elementor/theme/posts_archive/query_posts/query_vars', array($this, 'add_taxonomy_to_query'));
    }

    /**
     * Add taxonomy to Elementor archive query.
     *
     * @param array $query_vars Query variables.
     * @return array
     */
    public function add_taxonomy_to_query($query_vars) {
        if (is_tax('bslm_service_group')) {
            $query_vars['tax_query'] = array(
                array(
                    'taxonomy' => 'bslm_service_group',
                    'field' => 'slug',
                    'terms' => get_queried_object()->slug,
                ),
            );
        }
        return $query_vars;
    }

    /**
     * Add service group taxonomy to Elementor Pro location conditions.
     *
     * @param array $taxonomies List of taxonomies.
     * @return array
     */
    public function add_taxonomy_to_conditions($taxonomies) {
        $taxonomies['bslm_service_group'] = 'bslm_service_group';
        return $taxonomies;
    }
}
