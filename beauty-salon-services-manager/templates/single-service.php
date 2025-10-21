<?php
/**
 * Template for displaying a single service page
 *
 * This template can be overridden by copying it to yourtheme/beauty-salon-services-manager/single-service.php
 * or by creating a single-bslm_service.php in your theme
 *
 * @package Beauty_Salon_Services_Manager
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header();

while (have_posts()) :
    the_post();

    $service_id = get_the_ID();
    $time = get_post_meta($service_id, '_bslm_service_time', true);
    $price = get_post_meta($service_id, '_bslm_service_price', true);
    $notes = get_post_meta($service_id, '_bslm_service_notes', true);
    ?>

    <article id="service-<?php the_ID(); ?>" <?php post_class('bslm-single-service'); ?>>

        <header class="entry-header">
            <h1 class="entry-title"><?php the_title(); ?></h1>

            <?php
            // Get service groups
            $groups = get_the_terms($service_id, 'bslm_service_group');
            if (!empty($groups) && !is_wp_error($groups)) :
                ?>
                <div class="bslm-service-groups">
                    <?php
                    foreach ($groups as $group) {
                        echo '<span class="bslm-group-tag">' . esc_html($group->name) . '</span>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <div class="bslm-service-featured-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>

        <div class="bslm-service-details">
            <h3><?php esc_html_e('Service Details', 'beauty-salon-services-manager'); ?></h3>

            <?php if ($time) : ?>
                <div class="bslm-detail-item">
                    <i class="far fa-clock" aria-hidden="true"></i>
                    <strong><?php esc_html_e('Duration:', 'beauty-salon-services-manager'); ?></strong>
                    <?php echo esc_html($time); ?>
                </div>
            <?php endif; ?>

            <?php if ($price) : ?>
                <div class="bslm-detail-item">
                    <i class="fas fa-tag" aria-hidden="true"></i>
                    <strong><?php esc_html_e('Price:', 'beauty-salon-services-manager'); ?></strong>
                    <?php
                    $formatted_price = apply_filters('bslm_service_price_format', $price, $service_id);
                    echo esc_html($formatted_price);
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="entry-content">
            <?php
            the_content();

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'beauty-salon-services-manager'),
                'after'  => '</div>',
            ));
            ?>
        </div>

        <?php if ($notes) : ?>
            <div class="bslm-service-notes">
                <h4><?php esc_html_e('Additional Information', 'beauty-salon-services-manager'); ?></h4>
                <?php echo wpautop(esc_html($notes)); ?>
            </div>
        <?php endif; ?>

        <footer class="entry-footer">
            <?php
            // Add booking button or contact form here if needed
            do_action('bslm_after_single_service', $service_id);
            ?>
        </footer>

    </article>

    <?php
endwhile;

get_footer();
