<?php
/**
 * Handle taxonomy meta fields (category icons).
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Taxonomy_Meta {

    /**
     * Constructor.
     */
    public function __construct() {
        // Add form fields
        add_action('bslm_service_group_add_form_fields', array($this, 'add_category_icon_field'), 10, 2);
        add_action('bslm_service_group_edit_form_fields', array($this, 'edit_category_icon_field'), 10, 2);

        // Save fields
        add_action('created_bslm_service_group', array($this, 'save_category_icon'), 10, 2);
        add_action('edited_bslm_service_group', array($this, 'save_category_icon'), 10, 2);

        // Add custom column
        add_filter('manage_edit-bslm_service_group_columns', array($this, 'add_icon_column'));
        add_filter('manage_bslm_service_group_custom_column', array($this, 'add_icon_column_content'), 10, 3);

        // Enqueue media uploader
        add_action('admin_enqueue_scripts', array($this, 'enqueue_media_uploader'));
    }

    /**
     * Add icon field to category add form.
     */
    public function add_category_icon_field($taxonomy) {
        // Add nonce field for security
        wp_nonce_field('bslm_save_category_icon', 'bslm_category_icon_nonce');
        ?>
        <div class="form-field term-icon-wrap">
            <label for="bslm_category_icon"><?php _e('Category Icon', 'beauty-salon-services-manager'); ?></label>
            <div class="bslm-category-icon-upload">
                <input type="hidden" id="bslm_category_icon" name="bslm_category_icon" value="">
                <div class="bslm-icon-preview" style="margin-bottom: 10px;">
                    <img src="" style="max-width: 60px; height: auto; display: none;" />
                </div>
                <button type="button" class="button bslm-upload-icon-button">
                    <?php _e('Upload Icon', 'beauty-salon-services-manager'); ?>
                </button>
                <button type="button" class="button bslm-remove-icon-button" style="display: none;">
                    <?php _e('Remove Icon', 'beauty-salon-services-manager'); ?>
                </button>
            </div>
            <p class="description"><?php _e('Upload an icon/image for this service category (supports JPG, PNG, SVG)', 'beauty-salon-services-manager'); ?></p>
        </div>
        <?php
    }

    /**
     * Add icon field to category edit form.
     */
    public function edit_category_icon_field($term, $taxonomy) {
        $icon_id = get_term_meta($term->term_id, 'bslm_category_icon', true);
        $icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';

        // Add nonce field for security
        wp_nonce_field('bslm_save_category_icon', 'bslm_category_icon_nonce');
        ?>
        <tr class="form-field term-icon-wrap">
            <th scope="row">
                <label for="bslm_category_icon"><?php _e('Category Icon', 'beauty-salon-services-manager'); ?></label>
            </th>
            <td>
                <div class="bslm-category-icon-upload">
                    <input type="hidden" id="bslm_category_icon" name="bslm_category_icon" value="<?php echo esc_attr($icon_id); ?>">
                    <div class="bslm-icon-preview" style="margin-bottom: 10px;">
                        <?php if ($icon_url) : ?>
                            <img src="<?php echo esc_url($icon_url); ?>" style="max-width: 60px; height: auto;" />
                        <?php else : ?>
                            <img src="" style="max-width: 60px; height: auto; display: none;" />
                        <?php endif; ?>
                    </div>
                    <button type="button" class="button bslm-upload-icon-button">
                        <?php _e('Upload Icon', 'beauty-salon-services-manager'); ?>
                    </button>
                    <button type="button" class="button bslm-remove-icon-button" style="<?php echo $icon_url ? '' : 'display: none;'; ?>">
                        <?php _e('Remove Icon', 'beauty-salon-services-manager'); ?>
                    </button>
                    <p class="description"><?php _e('Upload an icon/image for this service category (supports JPG, PNG, SVG)', 'beauty-salon-services-manager'); ?></p>
                </div>
            </td>
        </tr>
        <?php
    }

    /**
     * Save category icon.
     *
     * @param int $term_id The term ID.
     */
    public function save_category_icon($term_id) {
        // Check if nonce is set
        if (!isset($_POST['bslm_category_icon_nonce'])) {
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['bslm_category_icon_nonce'], 'bslm_save_category_icon')) {
            return;
        }

        // Check user permissions
        if (!current_user_can('manage_categories')) {
            return;
        }

        // Save or delete the icon
        if (isset($_POST['bslm_category_icon'])) {
            $icon_id = absint($_POST['bslm_category_icon']);
            if ($icon_id) {
                update_term_meta($term_id, 'bslm_category_icon', $icon_id);
            } else {
                delete_term_meta($term_id, 'bslm_category_icon');
            }
        }
    }

    /**
     * Add icon column to taxonomy table.
     */
    public function add_icon_column($columns) {
        $new_columns = array();
        foreach ($columns as $key => $value) {
            if ($key === 'name') {
                $new_columns['icon'] = __('Icon', 'beauty-salon-services-manager');
            }
            $new_columns[$key] = $value;
        }
        return $new_columns;
    }

    /**
     * Display icon in column.
     */
    public function add_icon_column_content($content, $column_name, $term_id) {
        if ($column_name === 'icon') {
            $icon_id = get_term_meta($term_id, 'bslm_category_icon', true);
            if ($icon_id) {
                $icon_url = wp_get_attachment_url($icon_id);
                if ($icon_url) {
                    return '<img src="' . esc_url($icon_url) . '" style="max-width: 40px; height: auto;" />';
                }
            }
            return '—';
        }
        return $content;
    }

    /**
     * Enqueue media uploader scripts.
     */
    public function enqueue_media_uploader($hook) {
        if ('edit-tags.php' !== $hook && 'term.php' !== $hook) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || $screen->taxonomy !== 'bslm_service_group') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script(
            'bslm-taxonomy-icon-uploader',
            BSLM_PLUGIN_URL . 'assets/js/taxonomy-icon-uploader.js',
            array('jquery'),
            BSLM_VERSION,
            true
        );
    }
}
