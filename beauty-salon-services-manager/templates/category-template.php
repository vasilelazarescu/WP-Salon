<?php
/**
 * Category Archive Template
 *
 * This template displays the selected page's content for category archives.
 *
 * @package Beauty_Salon_Services_Manager
 */

get_header();

// Get settings
$settings = get_option('bslm_settings', array());
$template_page_id = isset($settings['category_template_page']) ? absint($settings['category_template_page']) : 0;

if ($template_page_id) {
    $template_page = get_post($template_page_id);

    if ($template_page) {
        // Check if Elementor is active and the page was built with Elementor
        $is_elementor = get_post_meta($template_page_id, '_elementor_edit_mode', true);

        if ($is_elementor === 'builder' && class_exists('\Elementor\Plugin')) {
            // Use Elementor to render the page
            echo \Elementor\Plugin::instance()->frontend->get_builder_content($template_page_id);
        } else {
            // Use standard WordPress content
            ?>
            <div class="bslm-category-template-wrapper">
                <?php
                // Apply content filters (shortcodes, etc.)
                $content = apply_filters('the_content', $template_page->post_content);
                echo $content;
                ?>
            </div>
            <?php
        }
    }
} else {
    // Fallback: Display default archive
    ?>
    <div class="bslm-category-archive-default">
        <header class="page-header">
            <?php
            the_archive_title('<h1 class="page-title">', '</h1>');
            the_archive_description('<div class="archive-description">', '</div>');
            ?>
        </header>

        <div class="bslm-services-grid">
            <?php
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    ?>
                    <div class="bslm-service-card">
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="bslm-service-image">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('large'); ?>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="bslm-service-content">
                            <h3 class="bslm-service-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h3>

                            <?php if (has_excerpt()) : ?>
                                <div class="bslm-service-description">
                                    <?php echo wp_kses_post(get_the_excerpt()); ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?php the_permalink(); ?>" class="bslm-service-button">
                                <?php _e('Read More', 'beauty-salon-services-manager'); ?>
                            </a>
                        </div>
                    </div>
                    <?php
                }
                wp_reset_postdata();
            } else {
                echo '<p>' . __('No services found in this category.', 'beauty-salon-services-manager') . '</p>';
            }
            ?>
        </div>
    </div>
    <?php
}

get_footer();
