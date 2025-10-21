<?php
/**
 * Fired during plugin deactivation.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Deactivator {

    /**
     * Short Description.
     *
     * Long Description.
     */
    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Note: We don't delete options or data on deactivation
        // This should only be done on uninstall
    }
}
