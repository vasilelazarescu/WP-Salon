<?php
/**
 * AJAX Handlers for Bulk Price Editor.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Bulk_Price_Ajax {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('wp_ajax_bslm_update_service_prices', array($this, 'update_service_prices'));
        add_action('wp_ajax_bslm_bulk_apply_percentage', array($this, 'bulk_apply_percentage'));
        add_action('wp_ajax_bslm_bulk_apply_fixed', array($this, 'bulk_apply_fixed'));
        add_action('wp_ajax_bslm_save_column_visibility', array($this, 'save_column_visibility'));
    }

    /**
     * Update single service prices via AJAX.
     */
    public function update_service_prices() {
        // Verify nonce
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        // Check permissions
        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'beauty-salon-services-manager')));
        }

        // Get and validate data
        $service_id = isset($_POST['service_id']) ? absint($_POST['service_id']) : 0;
        $price_options = isset($_POST['price_options']) ? $_POST['price_options'] : array();

        if (!$service_id || !is_array($price_options)) {
            wp_send_json_error(array('message' => __('Invalid data', 'beauty-salon-services-manager')));
        }

        // Sanitize price options
        $sanitized_prices = array();
        foreach ($price_options as $option) {
            if (!isset($option['sessions']) || !isset($option['price'])) {
                continue;
            }

            $sanitized_prices[] = array(
                'sessions' => absint($option['sessions']),
                'price' => sanitize_text_field($option['price']),
            );
        }

        // Update prices
        update_post_meta($service_id, '_bslm_service_price_options', $sanitized_prices);

        wp_send_json_success(array(
            'message' => __('Prices updated successfully', 'beauty-salon-services-manager'),
            'service_id' => $service_id,
        ));
    }

    /**
     * Apply percentage increase/decrease to multiple services.
     */
    public function bulk_apply_percentage() {
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'beauty-salon-services-manager')));
        }

        $service_ids = isset($_POST['service_ids']) ? array_map('absint', $_POST['service_ids']) : array();
        $percentage = isset($_POST['percentage']) ? floatval($_POST['percentage']) : 0;

        if (empty($service_ids) || $percentage === 0.0) {
            wp_send_json_error(array('message' => __('Invalid data', 'beauty-salon-services-manager')));
        }

        $updated_count = 0;

        foreach ($service_ids as $service_id) {
            $price_options = get_post_meta($service_id, '_bslm_service_price_options', true);

            if (!is_array($price_options) || empty($price_options)) {
                continue;
            }

            $new_prices = array();

            foreach ($price_options as $option) {
                // Extract numeric value from price string
                $price_numeric = $this->extract_numeric_price($option['price']);

                if ($price_numeric <= 0) {
                    // Keep original if can't parse
                    $new_prices[] = $option;
                    continue;
                }

                // Apply percentage
                $new_price_numeric = $price_numeric * (1 + ($percentage / 100));

                // Extract currency symbol
                $currency = $this->extract_currency($option['price']);

                // Format new price
                $new_price = $this->format_price($new_price_numeric, $currency);

                $new_prices[] = array(
                    'sessions' => $option['sessions'],
                    'price' => $new_price,
                );
            }

            // Update prices
            update_post_meta($service_id, '_bslm_service_price_options', $new_prices);
            $updated_count++;
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: %d: number of services updated */
                __('Updated %d service(s)', 'beauty-salon-services-manager'),
                $updated_count
            ),
            'count' => $updated_count,
        ));
    }

    /**
     * Apply fixed amount increase/decrease to multiple services.
     */
    public function bulk_apply_fixed() {
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'beauty-salon-services-manager')));
        }

        $service_ids = isset($_POST['service_ids']) ? array_map('absint', $_POST['service_ids']) : array();
        $fixed_amount = isset($_POST['fixed_amount']) ? floatval($_POST['fixed_amount']) : 0;

        if (empty($service_ids) || $fixed_amount === 0.0) {
            wp_send_json_error(array('message' => __('Invalid data', 'beauty-salon-services-manager')));
        }

        $updated_count = 0;

        foreach ($service_ids as $service_id) {
            $price_options = get_post_meta($service_id, '_bslm_service_price_options', true);

            if (!is_array($price_options) || empty($price_options)) {
                continue;
            }

            $new_prices = array();

            foreach ($price_options as $option) {
                // Extract numeric value from price string
                $price_numeric = $this->extract_numeric_price($option['price']);

                if ($price_numeric <= 0) {
                    // Keep original if can't parse
                    $new_prices[] = $option;
                    continue;
                }

                // Apply fixed amount
                $new_price_numeric = $price_numeric + $fixed_amount;

                // Don't allow negative prices
                if ($new_price_numeric < 0) {
                    $new_price_numeric = 0;
                }

                // Extract currency symbol
                $currency = $this->extract_currency($option['price']);

                // Format new price
                $new_price = $this->format_price($new_price_numeric, $currency);

                $new_prices[] = array(
                    'sessions' => $option['sessions'],
                    'price' => $new_price,
                );
            }

            // Update prices
            update_post_meta($service_id, '_bslm_service_price_options', $new_prices);
            $updated_count++;
        }

        wp_send_json_success(array(
            'message' => sprintf(
                /* translators: %d: number of services updated */
                __('Updated %d service(s)', 'beauty-salon-services-manager'),
                $updated_count
            ),
            'count' => $updated_count,
        ));
    }

    /**
     * Extract numeric value from price string.
     *
     * @param string $price Price string with currency.
     * @return float Numeric price value.
     */
    private function extract_numeric_price($price) {
        // Remove non-numeric characters except decimal separators
        $price_clean = preg_replace('/[^0-9.,]/', '', $price);
        // Replace comma with period for consistent decimal handling
        $price_clean = str_replace(',', '.', $price_clean);
        return floatval($price_clean);
    }

    /**
     * Extract currency symbol from price string.
     *
     * @param string $price Price string with currency.
     * @return string Currency symbol.
     */
    private function extract_currency($price) {
        $currency = preg_replace('/[0-9.,\s]/', '', $price);
        if (empty($currency)) {
            $currency = '€'; // Default to Euro
        }
        return $currency;
    }

    /**
     * Format price with currency symbol.
     *
     * @param float $amount Numeric amount.
     * @param string $currency Currency symbol.
     * @return string Formatted price string.
     */
    private function format_price($amount, $currency = '€') {
        // Round to 2 decimal places
        $amount = round($amount, 2);

        // Format with decimal separator
        $formatted = number_format($amount, 2, '.', '');

        // Remove trailing zeros after decimal
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return $formatted . $currency;
    }

    /**
     * Save column visibility preferences via AJAX.
     */
    public function save_column_visibility() {
        check_ajax_referer('bslm_bulk_price_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied', 'beauty-salon-services-manager')));
        }

        $columns = isset($_POST['columns']) ? array_map('sanitize_text_field', $_POST['columns']) : array();
        $user_id = get_current_user_id();

        // Validate column names
        $allowed_columns = array('category', 'tags', 'parent', 'base_price', 'actions');
        $validated_columns = array();

        foreach ($columns as $column) {
            // Check if it's a tier column or an allowed column
            if (in_array($column, $allowed_columns) || strpos($column, 'tier_') === 0) {
                $validated_columns[] = $column;
            }
        }

        update_user_meta($user_id, 'bslm_bulk_editor_columns', $validated_columns);

        wp_send_json_success(array(
            'message' => __('Column visibility saved', 'beauty-salon-services-manager'),
            'columns' => $validated_columns,
        ));
    }
}
