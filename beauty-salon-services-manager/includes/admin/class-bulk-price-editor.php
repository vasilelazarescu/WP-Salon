<?php
/**
 * Bulk Price Editor Admin Page - Enhanced with Advanced Filters & Column Management.
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
        $tag_filter = isset($_GET['tag']) ? absint($_GET['tag']) : 0;
        $parent_filter = isset($_GET['parent']) ? sanitize_text_field($_GET['parent']) : '';
        $min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
        $max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

        // Get all services with filters
        $services = $this->get_all_services($search, $category_filter, $tag_filter, $parent_filter, $min_price, $max_price);

        // Get all categories for filter dropdown
        $categories = get_terms(array(
            'taxonomy' => 'bslm_service_group',
            'hide_empty' => false,
        ));

        // Get all tags for filter dropdown
        $tags = get_terms(array(
            'taxonomy' => 'bslm_service_tag',
            'hide_empty' => false,
        ));

        // Get parent services for filter dropdown
        $parent_services = get_posts(array(
            'post_type' => 'bslm_service',
            'post_parent' => 0,
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        // Get all unique tier sessions for column headers
        $tier_columns = $this->get_all_tier_columns($services);

        // Get column visibility from user meta
        $user_id = get_current_user_id();
        $visible_columns = get_user_meta($user_id, 'bslm_bulk_editor_columns', true);
        if (!is_array($visible_columns)) {
            // Default visible columns
            $visible_columns = array('category', 'base_price', 'actions');
        }

        // Load view template
        include BSLM_PLUGIN_DIR . 'includes/admin/views/bulk-price-editor.php';
    }

    /**
     * Get all services with price data and filters.
     *
     * @param string $search Search term.
     * @param int $category_filter Category ID to filter by.
     * @param int $tag_filter Tag ID to filter by.
     * @param string $parent_filter Parent filter (0, parent_only, children_only, or parent ID).
     * @param float $min_price Minimum price filter.
     * @param float $max_price Maximum price filter.
     * @return array Array of service data.
     */
    private function get_all_services($search = '', $category_filter = 0, $tag_filter = 0, $parent_filter = '', $min_price = 0, $max_price = 0) {
        $args = array(
            'post_type' => 'bslm_service',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC',
        );

        // Add search - use title only for more precise results
        if (!empty($search)) {
            // Add filter to search only in post_title
            add_filter('posts_search', array($this, 'search_by_title_only'), 10, 2);
            $args['s'] = $search;
        }

        // Add tax query
        $tax_query = array();

        // Add category filter
        if ($category_filter > 0) {
            $tax_query[] = array(
                'taxonomy' => 'bslm_service_group',
                'field' => 'term_id',
                'terms' => $category_filter,
            );
        }

        // Add tag filter
        if ($tag_filter > 0) {
            $tax_query[] = array(
                'taxonomy' => 'bslm_service_tag',
                'field' => 'term_id',
                'terms' => $tag_filter,
            );
        }

        if (!empty($tax_query)) {
            $tax_query['relation'] = 'AND';
            $args['tax_query'] = $tax_query;
        }

        // Add parent filter
        if ($parent_filter === 'parent_only') {
            $args['post_parent'] = 0;
        } elseif ($parent_filter === 'children_only') {
            $args['post_parent__not_in'] = array(0);
        } elseif (!empty($parent_filter) && is_numeric($parent_filter)) {
            $args['post_parent'] = absint($parent_filter);
        }

        $services = get_posts($args);

        // Remove the filter after query
        if (!empty($search)) {
            remove_filter('posts_search', array($this, 'search_by_title_only'), 10);
        }

        $services_data = array();

        foreach ($services as $service) {
            $price_options = get_post_meta($service->ID, '_bslm_service_price_options', true);
            $categories = wp_get_post_terms($service->ID, 'bslm_service_group');
            $tags = wp_get_post_terms($service->ID, 'bslm_service_tag');
            $parent_id = $service->post_parent;
            $parent_title = '';

            if ($parent_id > 0) {
                $parent = get_post($parent_id);
                if ($parent) {
                    $parent_title = $parent->post_title;
                }
            }

            // Ensure price_options is an array
            if (!is_array($price_options) || empty($price_options)) {
                $price_options = array(array('sessions' => '1', 'price' => ''));
            }

            // Apply price range filter
            if ($min_price > 0 || $max_price > 0) {
                $has_price_in_range = false;
                foreach ($price_options as $option) {
                    $price_numeric = $this->extract_numeric_price($option['price']);

                    if ($min_price > 0 && $price_numeric < $min_price) {
                        continue;
                    }
                    if ($max_price > 0 && $price_numeric > $max_price) {
                        continue;
                    }

                    $has_price_in_range = true;
                    break;
                }

                if (!$has_price_in_range) {
                    continue;
                }
            }

            $services_data[] = array(
                'id' => $service->ID,
                'title' => $service->post_title,
                'price_options' => $price_options,
                'categories' => $categories,
                'tags' => $tags,
                'parent_id' => $parent_id,
                'parent_title' => $parent_title,
            );
        }

        return $services_data;
    }

    /**
     * Modify search query to search only in post_title with exact match.
     *
     * @param string $search Search SQL.
     * @param WP_Query $wp_query Query object.
     * @return string Modified search SQL.
     */
    public function search_by_title_only($search, $wp_query) {
        global $wpdb;

        if (empty($search)) {
            return $search;
        }

        $search_term = $wp_query->get('s');
        if (empty($search_term)) {
            return $search;
        }

        // Build custom search query for exact match in post_title only
        $search = $wpdb->prepare(" AND {$wpdb->posts}.post_title = %s ", $search_term);

        return $search;
    }

    /**
     * Get all unique tier columns from services.
     *
     * @param array $services Array of service data.
     * @return array Array of unique session counts.
     */
    private function get_all_tier_columns($services) {
        $tiers = array();

        foreach ($services as $service) {
            if (!empty($service['price_options'])) {
                foreach ($service['price_options'] as $option) {
                    $sessions = absint($option['sessions']);
                    if ($sessions > 0 && !in_array($sessions, $tiers)) {
                        $tiers[] = $sessions;
                    }
                }
            }
        }

        sort($tiers);
        return $tiers;
    }

    /**
     * Extract numeric value from price string.
     *
     * @param string $price Price string.
     * @return float Numeric price value.
     */
    private function extract_numeric_price($price) {
        $price_clean = preg_replace('/[^0-9.,]/', '', $price);
        $price_clean = str_replace(',', '.', $price_clean);
        return floatval($price_clean);
    }

    /**
     * Get price for specific session tier.
     *
     * @param array $price_options Price options array.
     * @param int $sessions Session count.
     * @return string Price string or empty.
     */
    public static function get_price_for_tier($price_options, $sessions) {
        if (!is_array($price_options)) {
            return '';
        }

        foreach ($price_options as $option) {
            if (absint($option['sessions']) === $sessions) {
                return $option['price'];
            }
        }

        return '';
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
