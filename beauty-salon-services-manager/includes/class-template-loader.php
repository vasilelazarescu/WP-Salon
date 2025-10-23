<?php
/**
 * Template Loader for category archives.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Template_Loader {

    /**
     * Constructor.
     */
    public function __construct() {
        add_filter('template_include', array($this, 'load_category_template'), 99);
    }

    /**
     * Load custom category template if configured.
     *
     * @param string $template Template path.
     * @return string
     */
    public function load_category_template($template) {
        // Only apply to service group taxonomy archives
        if (!is_tax('bslm_service_group')) {
            return $template;
        }

        // Get settings
        $settings = get_option('bslm_settings', array());
        $template_page_id = isset($settings['category_template_page']) ? absint($settings['category_template_page']) : 0;

        // If no template page is set, return default template
        if (!$template_page_id) {
            return $template;
        }

        // Check if the page exists and is published
        $template_page = get_post($template_page_id);
        if (!$template_page || $template_page->post_status !== 'publish') {
            return $template;
        }

        // If Elementor Pro is active and has a template for this location, let it take precedence
        if ($this->has_elementor_pro_template()) {
            return $template;
        }

        // Load our custom template
        $custom_template = $this->get_custom_template_path();
        if ($custom_template && file_exists($custom_template)) {
            return $custom_template;
        }

        return $template;
    }

    /**
     * Check if Elementor Pro has a template for this location.
     *
     * @return bool
     */
    private function has_elementor_pro_template() {
        // Check if Elementor Pro is active
        if (!class_exists('\ElementorPro\Plugin')) {
            return false;
        }

        // Check if there's a theme builder template for archives
        if (function_exists('elementor_theme_do_location') && \ElementorPro\Modules\ThemeBuilder\Module::instance()->get_conditions_manager()->get_documents_for_location('archive')) {
            return true;
        }

        return false;
    }

    /**
     * Get custom template path.
     *
     * @return string
     */
    private function get_custom_template_path() {
        // Check theme directory first
        $theme_template = locate_template(array('bslm-category-template.php'));
        if ($theme_template) {
            return $theme_template;
        }

        // Use plugin template
        $plugin_template = BSLM_PLUGIN_DIR . 'templates/category-template.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }

        return '';
    }
}
