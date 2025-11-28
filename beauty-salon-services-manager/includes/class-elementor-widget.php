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
        require_once BSLM_PLUGIN_DIR . 'includes/widgets/class-service-meta-widget.php';
        require_once BSLM_PLUGIN_DIR . 'includes/widgets/class-services-by-tag-widget.php';

        $widgets_manager->register(new \BSLM_Beauty_Services_Grid_Widget());
        $widgets_manager->register(new \BSLM_Service_Meta_Widget());
        $widgets_manager->register(new \BSLM_Services_By_Tag_Widget());
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
            if (is_post_type_archive('bslm_service')) {
                return true;
            }
        }
        return $need_override;
    }
}
