<?php
/**
 * Plugin Settings Page.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Settings {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add settings page to WordPress admin menu.
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=bslm_service',
            __('Settings', 'beauty-salon-services-manager'),
            __('Settings', 'beauty-salon-services-manager'),
            'manage_options',
            'bslm-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Register plugin settings.
     */
    public function register_settings() {
        // Register setting
        register_setting(
            'bslm_settings_group',
            'bslm_settings',
            array($this, 'sanitize_settings')
        );

        // Add settings section for templates
        add_settings_section(
            'bslm_template_section',
            __('Category Template Settings', 'beauty-salon-services-manager'),
            array($this, 'render_template_section_desc'),
            'bslm-settings'
        );

        // Add field for category template page
        add_settings_field(
            'category_template_page',
            __('Category Archive Template Page', 'beauty-salon-services-manager'),
            array($this, 'render_category_template_field'),
            'bslm-settings',
            'bslm_template_section'
        );
    }

    /**
     * Render settings page.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">
                <?php
                settings_fields('bslm_settings_group');
                do_settings_sections('bslm-settings');
                submit_button();
                ?>
            </form>

            <div class="bslm-settings-help" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-left: 4px solid #2271b1;">
                <h2><?php _e('How to Use Category Templates (Elementor Free Compatible)', 'beauty-salon-services-manager'); ?></h2>
                <ol>
                    <li><?php _e('Create a new page and design it with Elementor', 'beauty-salon-services-manager'); ?></li>
                    <li><?php _e('Add the following shortcodes where you want content to appear:', 'beauty-salon-services-manager'); ?>
                        <ul style="margin-top: 10px;">
                            <li><code>[bslm_category_title]</code> - <?php _e('Displays the category name', 'beauty-salon-services-manager'); ?></li>
                            <li><code>[bslm_category_description]</code> - <?php _e('Displays the category description', 'beauty-salon-services-manager'); ?></li>
                            <li><code>[bslm_category_services]</code> - <?php _e('Displays services in the category', 'beauty-salon-services-manager'); ?></li>
                        </ul>
                    </li>
                    <li><?php _e('Select that page in the dropdown above', 'beauty-salon-services-manager'); ?></li>
                    <li><?php _e('Save settings - all service category pages will now use this design', 'beauty-salon-services-manager'); ?></li>
                </ol>
                <p><strong><?php _e('Note:', 'beauty-salon-services-manager'); ?></strong> <?php _e('If you have Elementor Pro, you can also use Theme Builder > Archives for more advanced control.', 'beauty-salon-services-manager'); ?></p>
            </div>
        </div>
        <?php
    }

    /**
     * Render template section description.
     */
    public function render_template_section_desc() {
        echo '<p>' . __('Configure which page to use as a template for service category archive pages. This works with Elementor Free.', 'beauty-salon-services-manager') . '</p>';
    }

    /**
     * Render category template field.
     */
    public function render_category_template_field() {
        $settings = get_option('bslm_settings', array());
        $selected_page = isset($settings['category_template_page']) ? $settings['category_template_page'] : '';

        // Get all pages
        $pages = get_pages(array(
            'post_status' => 'publish',
            'sort_column' => 'post_title',
        ));

        ?>
        <select name="bslm_settings[category_template_page]" id="category_template_page" style="min-width: 300px;">
            <option value=""><?php _e('— Select a Page —', 'beauty-salon-services-manager'); ?></option>
            <?php foreach ($pages as $page) : ?>
                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($selected_page, $page->ID); ?>>
                    <?php echo esc_html($page->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <p class="description">
            <?php _e('Select a page designed with Elementor to use as a template for all category archive pages.', 'beauty-salon-services-manager'); ?>
        </p>
        <?php
    }

    /**
     * Sanitize settings.
     *
     * @param array $input Settings input.
     * @return array Sanitized settings.
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        if (isset($input['category_template_page'])) {
            $sanitized['category_template_page'] = absint($input['category_template_page']);
        }

        return $sanitized;
    }
}
