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

        // Set default options
        add_option('bslm_version', BSLM_VERSION);
        add_option('bslm_activated_time', time());
    }
}
