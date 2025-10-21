<?php
/**
 * Template for displaying a single service card
 *
 * This template can be overridden by copying it to yourtheme/beauty-salon-services-manager/service-card.php
 *
 * @package Beauty_Salon_Services_Manager
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post;

$service_id = get_the_ID();
$time = get_post_meta($service_id, '_bslm_service_time', true);
$price = get_post_meta($service_id, '_bslm_service_price', true);
$notes = get_post_meta($service_id, '_bslm_service_notes', true);

// Get service groups
$groups = get_the_terms($service_id, 'bslm_service_group');
$group_classes = array();
if (!empty($groups) && !is_wp_error($groups)) {
    foreach ($groups as $group) {
        $group_classes[] = 'group-' . $group->slug;
    }
}

$card_classes = array('bslm-service-card');
$card_classes = array_merge($card_classes, $group_classes);
$card_classes = apply_filters('bslm_service_card_classes', $card_classes, $service_id);
?>

<div class="<?php echo esc_attr(implode(' ', $card_classes)); ?>" data-service-id="<?php echo esc_attr($service_id); ?>">

    <?php
    /**
     * Hook: bslm_before_service_content
     *
     * @hooked - Content before service card
     */
    do_action('bslm_before_service_content', $service_id);
    ?>

    <?php if (has_post_thumbnail()) : ?>
        <div class="bslm-service-image">
            <a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
                <?php
                the_post_thumbnail('large', array(
                    'alt' => get_the_title(),
                    'loading' => 'lazy'
                ));
                ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="bslm-service-content">

        <h3 class="bslm-service-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if (has_excerpt() || get_the_content()) : ?>
            <div class="bslm-service-description">
                <?php
                if (has_excerpt()) {
                    the_excerpt();
                } else {
                    echo wp_trim_words(get_the_content(), 20, '...');
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if ($time || $price) : ?>
            <div class="bslm-service-meta">

                <?php
                /**
                 * Hook: bslm_service_meta_display
                 *
                 * @hooked - Custom meta display
                 */
                do_action('bslm_service_meta_display', $service_id);
                ?>

                <?php if ($time) : ?>
                    <span class="bslm-service-time">
                        <i class="far fa-clock" aria-hidden="true"></i>
                        <span class="screen-reader-text"><?php esc_html_e('Duration:', 'beauty-salon-services-manager'); ?></span>
                        <?php echo esc_html($time); ?>
                    </span>
                <?php endif; ?>

                <?php if ($price) : ?>
                    <span class="bslm-service-price">
                        <i class="fas fa-tag" aria-hidden="true"></i>
                        <span class="screen-reader-text"><?php esc_html_e('Price:', 'beauty-salon-services-manager'); ?></span>
                        <?php
                        $formatted_price = apply_filters('bslm_service_price_format', $price, $service_id);
                        echo esc_html($formatted_price);
                        ?>
                    </span>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        <a href="<?php the_permalink(); ?>" class="bslm-service-button">
            <?php
            echo esc_html(
                apply_filters(
                    'bslm_service_button_text',
                    __('Learn More', 'beauty-salon-services-manager'),
                    $service_id
                )
            );
            ?>
        </a>

    </div>

    <?php
    /**
     * Hook: bslm_after_service_content
     *
     * @hooked - Content after service card
     */
    do_action('bslm_after_service_content', $service_id);
    ?>

</div>
