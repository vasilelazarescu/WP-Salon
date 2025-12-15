<?php
/**
 * Register and handle meta boxes.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Meta_Boxes {

    /**
     * Add meta boxes.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'bslm_service_details',
            __('Service Details', 'beauty-salon-services-manager'),
            array($this, 'render_service_details_meta_box'),
            'bslm_service',
            'normal',
            'high'
        );

        add_meta_box(
            'bslm_service_icon',
            __('Service Icon', 'beauty-salon-services-manager'),
            array($this, 'render_service_icon_meta_box'),
            'bslm_service',
            'side',
            'default'
        );
    }

    /**
     * Render service details meta box.
     *
     * @param WP_Post $post The post object.
     */
    public function render_service_details_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('bslm_service_details_nonce', 'bslm_service_details_nonce');

        // Retrieve current values
        $time = get_post_meta($post->ID, '_bslm_service_time', true);
        $price_options = get_post_meta($post->ID, '_bslm_service_price_options', true);
        $notes = get_post_meta($post->ID, '_bslm_service_notes', true);

        // Ensure price_options is an array
        if (!is_array($price_options) || empty($price_options)) {
            $price_options = array(array('sessions' => '1', 'price' => ''));
        }

        ?>
        <div class="bslm-meta-box">
            <p>
                <label for="bslm_service_time">
                    <strong><?php _e('Time Duration', 'beauty-salon-services-manager'); ?></strong>
                </label>
                <br>
                <input
                    type="text"
                    id="bslm_service_time"
                    name="bslm_service_time"
                    value="<?php echo esc_attr($time); ?>"
                    placeholder="<?php esc_attr_e('e.g., 30 minutes', 'beauty-salon-services-manager'); ?>"
                    class="widefat"
                >
                <span class="description"><?php _e('Enter the service duration (e.g., 30 minutes, 1 hour)', 'beauty-salon-services-manager'); ?></span>
            </p>

            <div class="bslm-price-options-wrapper">
                <label><strong><?php _e('Price Options', 'beauty-salon-services-manager'); ?></strong></label>
                <span class="description" style="display: block; margin-bottom: 10px;">
                    <?php _e('Add different pricing tiers based on number of sessions/seances', 'beauty-salon-services-manager'); ?>
                </span>

                <div id="bslm-price-options-container">
                    <?php foreach ($price_options as $index => $option) : ?>
                        <div class="bslm-price-option-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                            <input
                                type="number"
                                name="bslm_price_options[<?php echo $index; ?>][sessions]"
                                value="<?php echo esc_attr($option['sessions']); ?>"
                                placeholder="<?php esc_attr_e('Number', 'beauty-salon-services-manager'); ?>"
                                min="1"
                                style="width: 100px;"
                            >
                            <span><?php _e('seance(s)', 'beauty-salon-services-manager'); ?></span>
                            <input
                                type="text"
                                name="bslm_price_options[<?php echo $index; ?>][price]"
                                value="<?php echo esc_attr($option['price']); ?>"
                                placeholder="<?php esc_attr_e('e.g., $50.00', 'beauty-salon-services-manager'); ?>"
                                style="flex: 1;"
                            >
                            <button type="button" class="button button-secondary bslm-remove-price-option">
                                <?php _e('Remove', 'beauty-salon-services-manager'); ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="button button-secondary" id="bslm-add-price-option" style="margin-top: 10px;">
                    <?php _e('+ Add Price Option', 'beauty-salon-services-manager'); ?>
                </button>
            </div>

            <p style="margin-top: 20px;">
                <label for="bslm_service_notes">
                    <strong><?php _e('Additional Notes', 'beauty-salon-services-manager'); ?></strong>
                </label>
                <br>
                <textarea
                    id="bslm_service_notes"
                    name="bslm_service_notes"
                    rows="4"
                    class="widefat"
                    placeholder="<?php esc_attr_e('Optional additional information about this service', 'beauty-salon-services-manager'); ?>"
                ><?php echo esc_textarea($notes); ?></textarea>
                <span class="description"><?php _e('Any additional information about this service (optional)', 'beauty-salon-services-manager'); ?></span>
            </p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var priceOptionIndex = <?php echo count($price_options); ?>;

            // Add new price option row
            $('#bslm-add-price-option').on('click', function() {
                var newRow = '<div class="bslm-price-option-row" style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">' +
                    '<input type="number" name="bslm_price_options[' + priceOptionIndex + '][sessions]" value="1" placeholder="<?php esc_attr_e('Number', 'beauty-salon-services-manager'); ?>" min="1" style="width: 100px;">' +
                    '<span><?php _e('seance(s)', 'beauty-salon-services-manager'); ?></span>' +
                    '<input type="text" name="bslm_price_options[' + priceOptionIndex + '][price]" value="" placeholder="<?php esc_attr_e('e.g., $50.00', 'beauty-salon-services-manager'); ?>" style="flex: 1;">' +
                    '<button type="button" class="button button-secondary bslm-remove-price-option"><?php _e('Remove', 'beauty-salon-services-manager'); ?></button>' +
                '</div>';

                $('#bslm-price-options-container').append(newRow);
                priceOptionIndex++;
            });

            // Remove price option row
            $(document).on('click', '.bslm-remove-price-option', function() {
                if ($('.bslm-price-option-row').length > 1) {
                    $(this).closest('.bslm-price-option-row').remove();
                } else {
                    alert('<?php _e('You must have at least one price option', 'beauty-salon-services-manager'); ?>');
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Save meta box data.
     *
     * @param int $post_id The post ID.
     */
    public function save_meta_boxes($post_id) {
        // Check if nonce is set
        if (!isset($_POST['bslm_service_details_nonce'])) {
            return;
        }

        // Verify nonce
        if (!wp_verify_nonce($_POST['bslm_service_details_nonce'], 'bslm_service_details_nonce')) {
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

        // Check if this is the correct post type
        if (get_post_type($post_id) !== 'bslm_service') {
            return;
        }

        // Sanitize and save data
        if (isset($_POST['bslm_service_time'])) {
            $time = sanitize_text_field($_POST['bslm_service_time']);
            update_post_meta($post_id, '_bslm_service_time', $time);
        }

        if (isset($_POST['bslm_price_options']) && is_array($_POST['bslm_price_options'])) {
            $price_options = array();
            foreach ($_POST['bslm_price_options'] as $option) {
                // Only save if both fields have values
                if (!empty($option['sessions']) && !empty($option['price'])) {
                    $price_options[] = array(
                        'sessions' => absint($option['sessions']),
                        'price' => sanitize_text_field($option['price']),
                    );
                }
            }
            update_post_meta($post_id, '_bslm_service_price_options', $price_options);
        }

        if (isset($_POST['bslm_service_notes'])) {
            $notes = sanitize_textarea_field($_POST['bslm_service_notes']);
            update_post_meta($post_id, '_bslm_service_notes', $notes);
        }

        // Save service icon
        if (isset($_POST['bslm_service_icon'])) {
            $icon_id = absint($_POST['bslm_service_icon']);
            if ($icon_id) {
                update_post_meta($post_id, '_bslm_service_icon', $icon_id);
            } else {
                delete_post_meta($post_id, '_bslm_service_icon');
            }
        }
    }

    /**
     * Render service icon meta box.
     *
     * @param WP_Post $post The post object.
     */
    public function render_service_icon_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('bslm_service_details_nonce', 'bslm_service_details_nonce');

        // Retrieve current icon
        $icon_id = get_post_meta($post->ID, '_bslm_service_icon', true);
        $icon_url = $icon_id ? wp_get_attachment_url($icon_id) : '';

        // Enqueue media uploader
        wp_enqueue_media();
        ?>
        <div class="bslm-service-icon-upload">
            <input type="hidden" id="bslm_service_icon" name="bslm_service_icon" value="<?php echo esc_attr($icon_id); ?>">
            <div class="bslm-icon-preview" style="margin-bottom: 10px; text-align: center;">
                <?php if ($icon_url) : ?>
                    <img src="<?php echo esc_url($icon_url); ?>" style="max-width: 100%; height: auto; max-height: 150px;" />
                <?php else : ?>
                    <img src="" style="max-width: 100%; height: auto; max-height: 150px; display: none;" />
                <?php endif; ?>
            </div>
            <p style="text-align: center;">
                <button type="button" class="button button-secondary bslm-upload-service-icon-button" style="width: 100%;">
                    <?php _e('Upload Icon', 'beauty-salon-services-manager'); ?>
                </button>
            </p>
            <p style="text-align: center;">
                <button type="button" class="button button-secondary bslm-remove-service-icon-button" style="width: 100%; <?php echo $icon_url ? '' : 'display: none;'; ?>">
                    <?php _e('Remove Icon', 'beauty-salon-services-manager'); ?>
                </button>
            </p>
            <p class="description">
                <?php _e('Upload an icon for this service. This can be displayed instead of or alongside the featured image. Supports JPG, PNG, SVG.', 'beauty-salon-services-manager'); ?>
            </p>
        </div>

        <script>
        jQuery(document).ready(function($) {
            var mediaUploader;

            $('.bslm-upload-service-icon-button').on('click', function(e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Choose Service Icon',
                    button: {
                        text: 'Use this icon'
                    },
                    library: {
                        type: ['image']
                    },
                    multiple: false
                });

                mediaUploader.on('select', function() {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#bslm_service_icon').val(attachment.id);
                    $('.bslm-icon-preview img').attr('src', attachment.url).show();
                    $('.bslm-remove-service-icon-button').show();
                });

                mediaUploader.open();
            });

            $('.bslm-remove-service-icon-button').on('click', function(e) {
                e.preventDefault();
                $('#bslm_service_icon').val('');
                $('.bslm-icon-preview img').attr('src', '').hide();
                $(this).hide();
            });
        });
        </script>
        <?php
    }
}
