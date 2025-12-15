<?php
/**
 * Fired during plugin activation.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Activator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function activate() {
        // Register custom post types and taxonomies
        require_once BSLM_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once BSLM_PLUGIN_DIR . 'includes/class-taxonomies.php';

        $post_types = new BSLM_Post_Types();
        $post_types->register_post_types();

        $taxonomies = new BSLM_Taxonomies();
        $taxonomies->register_taxonomies();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Run data migration
        self::migrate_price_data();

        // Set default options
        add_option('bslm_version', BSLM_VERSION);
        add_option('bslm_activated_time', time());
    }

    /**
     * Migrate old single price data to new price options format.
     */
    public static function migrate_price_data() {
        // Check if migration has already been run
        if (get_option('bslm_price_migration_done')) {
            return;
        }

        // Get all services
        $args = array(
            'post_type' => 'bslm_service',
            'posts_per_page' => -1,
            'post_status' => 'any',
        );

        $services = get_posts($args);

        foreach ($services as $service) {
            $post_id = $service->ID;

            // Check if service already has new price options
            $price_options = get_post_meta($post_id, '_bslm_service_price_options', true);

            // Only migrate if no new price options exist
            if (empty($price_options) || !is_array($price_options)) {
                // Get old price
                $old_price = get_post_meta($post_id, '_bslm_service_price', true);

                // If old price exists, convert to new format
                if (!empty($old_price)) {
                    $new_price_options = array(
                        array(
                            'sessions' => 1,
                            'price' => $old_price,
                        ),
                    );

                    update_post_meta($post_id, '_bslm_service_price_options', $new_price_options);
                }
            }
        }

        // Mark migration as done
        update_option('bslm_price_migration_done', true);
    }
}
