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
        require_once BSLM_PLUGIN_DIR . 'includes/class-taxonomies.php';
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

        // Register taxonomies
        $taxonomies = new BSLM_Taxonomies();
        $this->loader->add_action('init', $taxonomies, 'register_taxonomies');

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

        // Make parent column sortable
        $this->loader->add_filter('manage_edit-bslm_service_sortable_columns', $this, 'make_columns_sortable');

        // Add hierarchical sorting to services list
        $this->loader->add_filter('pre_get_posts', $this, 'hierarchical_service_sorting');

        // Add quick edit functionality for parent
        $this->loader->add_action('quick_edit_custom_box', $this, 'display_custom_quickedit', 10, 2);
        $this->loader->add_action('save_post', $this, 'save_quick_edit_data');
        $this->loader->add_action('admin_footer', $this, 'quick_edit_javascript');

        // Add hierarchical row classes
        $this->loader->add_filter('post_class', $this, 'add_hierarchical_row_class', 10, 3);
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     */
    private function define_public_hooks() {
        // Register styles and scripts early for Elementor widget dependencies
        $this->loader->add_action('wp_loaded', $this, 'register_public_assets');

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
     * Register public assets early for Elementor widget dependencies.
     */
    public function register_public_assets() {
        // Register frontend CSS
        wp_register_style(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            $this->version,
            'all'
        );

        // Register frontend JS
        wp_register_script(
            $this->plugin_name,
            BSLM_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            $this->version,
            false
        );
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
     * Assets are already registered, just enqueue them.
     */
    public function enqueue_public_styles() {
        wp_enqueue_style($this->plugin_name);
    }

    /**
     * Enqueue public scripts.
     * Assets are already registered, just enqueue them.
     */
    public function enqueue_public_scripts() {
        wp_enqueue_script($this->plugin_name);
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
                $new_columns['parent_service'] = __('Parent Service', 'beauty-salon-services-manager');
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
            case 'parent_service':
                $post = get_post($post_id);
                if ($post->post_parent) {
                    $parent = get_post($post->post_parent);
                    if ($parent) {
                        echo '<a href="' . get_edit_post_link($parent->ID) . '">' . esc_html($parent->post_title) . '</a>';
                        echo '<input type="hidden" class="parent_id" value="' . esc_attr($post->post_parent) . '" />';
                    } else {
                        echo '—';
                    }
                } else {
                    echo '<strong>' . __('— Parent —', 'beauty-salon-services-manager') . '</strong>';
                    echo '<input type="hidden" class="parent_id" value="0" />';
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

    /**
     * Make columns sortable.
     */
    public function make_columns_sortable($columns) {
        $columns['parent_service'] = 'parent';
        return $columns;
    }

    /**
     * Hierarchical sorting for services list.
     */
    public function hierarchical_service_sorting($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== 'bslm_service') {
            return;
        }

        // If user is sorting by parent column
        if ($query->get('orderby') === 'parent') {
            $query->set('orderby', 'parent');
            $query->set('order', $query->get('order'));
            return;
        }

        // Default hierarchical sorting
        if (!isset($_GET['orderby'])) {
            $query->set('orderby', 'menu_order title');
            $query->set('order', 'ASC');
        }
    }

    /**
     * Display custom quick edit box.
     */
    public function display_custom_quickedit($column_name, $post_type) {
        if ($post_type !== 'bslm_service' || $column_name !== 'parent_service') {
            return;
        }

        // Get all parent services
        $parent_services = get_posts(array(
            'post_type' => 'bslm_service',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post_parent' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        ?>
        <fieldset class="inline-edit-col-right">
            <div class="inline-edit-col">
                <label>
                    <span class="title"><?php _e('Parent Service', 'beauty-salon-services-manager'); ?></span>
                    <span class="input-text-wrap">
                        <select name="parent_id" id="parent_id">
                            <option value="0"><?php _e('— No Parent (Top Level) —', 'beauty-salon-services-manager'); ?></option>
                            <?php foreach ($parent_services as $service) : ?>
                                <option value="<?php echo esc_attr($service->ID); ?>">
                                    <?php echo esc_html($service->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                </label>
            </div>
        </fieldset>
        <?php
    }

    /**
     * Save quick edit data.
     */
    public function save_quick_edit_data($post_id) {
        // Verify this is a service post type
        if (get_post_type($post_id) !== 'bslm_service') {
            return;
        }

        // Check if this is an autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check user permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update parent if set
        if (isset($_POST['parent_id'])) {
            $parent_id = intval($_POST['parent_id']);

            // Prevent setting itself as parent
            if ($parent_id !== $post_id) {
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_parent' => $parent_id,
                ));
            }
        }
    }

    /**
     * Add JavaScript for quick edit functionality.
     */
    public function quick_edit_javascript() {
        global $current_screen;

        if ($current_screen->id !== 'edit-bslm_service') {
            return;
        }
        ?>
        <script type="text/javascript">
        (function($) {
            // Populate quick edit fields with current values
            var $wp_inline_edit = inlineEditPost.edit;
            inlineEditPost.edit = function(id) {
                $wp_inline_edit.apply(this, arguments);

                var post_id = 0;
                if (typeof(id) == 'object') {
                    post_id = parseInt(this.getId(id));
                }

                if (post_id > 0) {
                    var $row = $('#post-' + post_id);
                    var $parent_id = $row.find('.parent_id').val();

                    if ($parent_id) {
                        $('#parent_id').val($parent_id);
                    }
                }
            };
        })(jQuery);
        </script>
        <?php
    }

    /**
     * Add hierarchical row class based on post parent.
     */
    public function add_hierarchical_row_class($classes, $class, $post_id) {
        if (get_post_type($post_id) !== 'bslm_service') {
            return $classes;
        }

        $post = get_post($post_id);
        if ($post->post_parent) {
            $classes[] = 'level-1';
        } else {
            $classes[] = 'level-0';
        }

        return $classes;
    }
}
