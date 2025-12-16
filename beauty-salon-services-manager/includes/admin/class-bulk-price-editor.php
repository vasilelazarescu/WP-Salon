<?php
/**
 * Bulk Price Editor Admin Page.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Bulk_Price_Editor {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
    }

    /**
     * Add admin menu page.
     */
    public function add_admin_page() {
        add_submenu_page(
            'edit.php?post_type=bslm_service',
            __('Bulk Price Editor', 'beauty-salon-services-manager'),
            __('Bulk Prices', 'beauty-salon-services-manager'),
            'edit_posts',
            'bslm-bulk-prices',
            array($this, 'render_page')
        );
    }

    /**
     * Render the bulk price editor page.
     */
    public function render_page() {
        // Security check
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have permission to access this page.', 'beauty-salon-services-manager'));
        }

        // Get filter parameters
        $search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
        $category_filter = isset($_GET['category']) ? absint($_GET['category']) : 0;

        // Get all services with filters
        $services = $this->get_all_services($search, $category_filter);

        // Get all categories for filter dropdown
        $categories = get_terms(array(
            'taxonomy' => 'bslm_service_group',
            'hide_empty' => false,
        ));

        // Load view template
        include BSLM_PLUGIN_DIR . 'includes/admin/views/bulk-price-editor.php';
    }

    /**
     * Get all services with price data and filters.
     *
     * @param string $search Search term.
     * @param int $category_filter Category ID to filter by.
     * @return array Array of service data.
     */
    private function get_all_services($search = '', $category_filter = 0) {
        $args = array(
            'post_type' => 'bslm_service',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );

        // Add search
        if (!empty($search)) {
            $args['s'] = $search;
        }

        // Add category filter
        if ($category_filter > 0) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'bslm_service_group',
                    'field' => 'term_id',
                    'terms' => $category_filter,
                ),
            );
        }

        $services = get_posts($args);
        $services_data = array();

        foreach ($services as $service) {
            $price_options = get_post_meta($service->ID, '_bslm_service_price_options', true);
            $categories = wp_get_post_terms($service->ID, 'bslm_service_group');

            // Ensure price_options is an array
            if (!is_array($price_options) || empty($price_options)) {
                $price_options = array(array('sessions' => '1', 'price' => ''));
            }

            $services_data[] = array(
                'id' => $service->ID,
                'title' => $service->post_title,
                'price_options' => $price_options,
                'categories' => $categories,
            );
        }

        return $services_data;
    }

    /**
     * Enqueue admin assets.
     *
     * @param string $hook Current admin page hook.
     */
    public function enqueue_assets($hook) {
        // Only load on our page
        if ($hook !== 'bslm_service_page_bslm-bulk-prices') {
            return;
        }

        wp_enqueue_style(
            'bslm-bulk-price-editor',
            BSLM_PLUGIN_URL . 'assets/css/bulk-price-editor.css',
            array(),
            BSLM_VERSION
        );

        wp_enqueue_script(
            'bslm-bulk-price-editor',
            BSLM_PLUGIN_URL . 'assets/js/bulk-price-editor.js',
            array('jquery'),
            BSLM_VERSION,
            true
        );

        // Localize script
        wp_localize_script('bslm-bulk-price-editor', 'bslmBulkEditor', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bslm_bulk_price_nonce'),
            'strings' => array(
                'saving' => __('Saving...', 'beauty-salon-services-manager'),
                'saved' => __('Saved!', 'beauty-salon-services-manager'),
                'error' => __('Error saving prices', 'beauty-salon-services-manager'),
                'confirmBulk' => __('Apply changes to selected services?', 'beauty-salon-services-manager'),
                'noSelection' => __('Please select at least one service.', 'beauty-salon-services-manager'),
                'invalidValue' => __('Please enter a valid percentage or amount.', 'beauty-salon-services-manager'),
            ),
        ));
    }

    /**
     * Get base price from price options.
     *
     * @param array $price_options Price options array.
     * @return string Base price string.
     */
    public static function get_base_price($price_options) {
        if (is_array($price_options) && !empty($price_options)) {
            return $price_options[0]['price'];
        }
        return '—';
    }
}
