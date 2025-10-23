<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @package Beauty_Salon_Services_Manager
 */

class Beauty_Salon_Services_Manager {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @var BSLM_Loader
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @var string
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @var string
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->version = BSLM_VERSION;
        $this->plugin_name = 'beauty-salon-services-manager';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once BSLM_PLUGIN_DIR . 'includes/class-loader.php';
        require_once BSLM_PLUGIN_DIR . 'includes/class-post-types.php';
        require_once BSLM_PLUGIN_DIR . 'includes/class-meta-boxes.php';
        require_once BSLM_PLUGIN_DIR . 'includes/class-elementor-widget.php';
        require_once BSLM_PLUGIN_DIR . 'includes/class-shortcodes.php';

        $this->loader = new BSLM_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     */
    private function define_admin_hooks() {
        // Register custom post types
        $post_types = new BSLM_Post_Types();
        $this->loader->add_action('init', $post_types, 'register_post_types');

        // Register meta boxes
        $meta_boxes = new BSLM_Meta_Boxes();
        $this->loader->add_action('add_meta_boxes', $meta_boxes, 'add_meta_boxes');
        $this->loader->add_action('save_post', $meta_boxes, 'save_meta_boxes');

        // Enqueue admin styles and scripts
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_styles');
        $this->loader->add_action('admin_enqueue_scripts', $this, 'enqueue_admin_scripts');

        // Add custom columns to services list
        $this->loader->add_filter('manage_bslm_service_posts_columns', $this, 'add_custom_columns');
        $this->loader->add_action('manage_bslm_service_posts_custom_column', $this, 'custom_column_content', 10, 2);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        // Enqueue public styles and scripts
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_styles');
        $this->loader->add_action('wp_enqueue_scripts', $this, 'enqueue_public_scripts');

        // Register Elementor widgets
        $elementor_widget = new BSLM_Elementor_Widget();
        $this->loader->add_action('elementor/widgets/register', $elementor_widget, 'register_widgets');
        $this->loader->add_action('elementor/elements/categories_registered', $elementor_widget, 'add_elementor_widget_categories');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * Enqueue admin styles.
     */
    public function enqueue_admin_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue admin scripts.
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Enqueue public styles.
     */
    public function enqueue_public_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Enqueue public scripts.
     */
    public function enqueue_public_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            $this->version,
            false
        );
    }

    /**
     * Add custom columns to services list.
     */
    public function add_custom_columns($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            $new_columns[$key] = $value;
            if ($key === 'title') {
                $new_columns['thumbnail'] = __('Image', 'beauty-salon-services-manager');
                $new_columns['service_groups'] = __('Service Groups', 'beauty-salon-services-manager');
                $new_columns['time'] = __('Time', 'beauty-salon-services-manager');
                $new_columns['price'] = __('Price', 'beauty-salon-services-manager');
            }
        }
        return $new_columns;
    }

    /**
     * Display custom column content.
     */
    public function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '—';
                }
                break;
            case 'service_groups':
                $terms = get_the_terms($post_id, 'bslm_service_group');
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term_names = array_map(function($term) {
                        return $term->name;
                    }, $terms);
                    echo esc_html(implode(', ', $term_names));
                } else {
                    echo '—';
                }
                break;
            case 'time':
                $time = get_post_meta($post_id, '_bslm_service_time', true);
                echo $time ? esc_html($time) : '—';
                break;
            case 'price':
                $price = get_post_meta($post_id, '_bslm_service_price', true);
                echo $price ? esc_html($price) : '—';
                break;
        }
    }
}
