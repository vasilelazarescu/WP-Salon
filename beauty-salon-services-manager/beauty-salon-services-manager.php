<?php
/**
 * Plugin Name: Beauty Salon Services Manager
 * Plugin URI: https://github.com/yourusername/beauty-salon-services-manager
 * Description: A WordPress plugin that enables beauty salons and laser epilation clinics to manage and display their services in organized groups with full Elementor integration.
 * Version: 1.1.2
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: beauty-salon-services-manager
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 */
define('BSLM_VERSION', '1.1.2');
define('BSLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BSLM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BSLM_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * The code that runs during plugin activation.
 */
function activate_beauty_salon_services_manager() {
    require_once BSLM_PLUGIN_DIR . 'includes/class-activator.php';
    BSLM_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_beauty_salon_services_manager() {
    require_once BSLM_PLUGIN_DIR . 'includes/class-deactivator.php';
    BSLM_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_beauty_salon_services_manager');
register_deactivation_hook(__FILE__, 'deactivate_beauty_salon_services_manager');

/**
 * The core plugin class.
 */
require_once BSLM_PLUGIN_DIR . 'includes/class-beauty-salon-services-manager.php';

/**
 * Begins execution of the plugin.
 */
function run_beauty_salon_services_manager() {
    $plugin = new Beauty_Salon_Services_Manager();
    $plugin->run();
}
run_beauty_salon_services_manager();
