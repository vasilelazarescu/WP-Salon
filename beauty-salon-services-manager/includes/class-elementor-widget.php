<?php
/**
 * Elementor Widget Integration.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Elementor_Widget {

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
}
