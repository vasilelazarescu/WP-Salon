<?php
/**
 * Shortcodes for category templates.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Shortcodes {

    /**
     * Constructor.
     */
    public function __construct() {
        add_shortcode('bslm_category_title', array($this, 'category_title_shortcode'));
        add_shortcode('bslm_category_description', array($this, 'category_description_shortcode'));
        add_shortcode('bslm_category_services', array($this, 'category_services_shortcode'));
    }

    /**
     * Category title shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function category_title_shortcode($atts) {
        $atts = shortcode_atts(array(
            'tag' => 'h1',
            'class' => 'bslm-category-title',
        ), $atts, 'bslm_category_title');

        // Check if we're on a category page
        if (!is_tax('bslm_service_group')) {
            return '';
        }

        $term = get_queried_object();
        if (!$term) {
            return '';
        }

        $tag = tag_escape($atts['tag']);
        $class = esc_attr($atts['class']);

        return sprintf(
            '<%1$s class="%2$s">%3$s</%1$s>',
            $tag,
            $class,
            esc_html($term->name)
        );
    }

    /**
     * Category description shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function category_description_shortcode($atts) {
        $atts = shortcode_atts(array(
            'class' => 'bslm-category-description',
        ), $atts, 'bslm_category_description');

        // Check if we're on a category page
        if (!is_tax('bslm_service_group')) {
            return '';
        }

        $term = get_queried_object();
        if (!$term) {
            return '';
        }

        $description = term_description($term->term_id, 'bslm_service_group');
        if (empty($description)) {
            return '';
        }

        $class = esc_attr($atts['class']);

        return sprintf(
            '<div class="%s">%s</div>',
            $class,
            wp_kses_post($description)
        );
    }

    /**
     * Category services shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function category_services_shortcode($atts) {
        $atts = shortcode_atts(array(
            'columns' => '3',
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => '-1',
        ), $atts, 'bslm_category_services');

        // Check if we're on a category page
        if (!is_tax('bslm_service_group')) {
            return '';
        }

        $term = get_queried_object();
        if (!$term) {
            return '';
        }

        // Query services in this category
        $args = array(
            'post_type' => 'bslm_service',
            'post_status' => 'publish',
            'posts_per_page' => intval($atts['posts_per_page']),
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'tax_query' => array(
                array(
                    'taxonomy' => 'bslm_service_group',
                    'field' => 'term_id',
                    'terms' => $term->term_id,
                ),
            ),
        );

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '<p>' . __('No services found in this category.', 'beauty-salon-services-manager') . '</p>';
        }

        $columns = absint($atts['columns']);
        $columns = max(1, min(4, $columns)); // Limit between 1 and 4

        ob_start();
        ?>
        <div class="bslm-services-grid" style="grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);">
            <?php
            while ($query->have_posts()) {
                $query->the_post();
                $this->render_service_card();
            }
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Render service card for shortcode.
     */
    private function render_service_card() {
        $post_id = get_the_ID();
        $time = get_post_meta($post_id, '_bslm_service_time', true);
        $price = get_post_meta($post_id, '_bslm_service_price', true);
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

                <?php if ($time || $price) : ?>
                    <div class="bslm-service-meta">
                        <?php if ($time) : ?>
                            <span class="bslm-service-time">
                                <i class="far fa-clock"></i>
                                <?php echo esc_html($time); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($price) : ?>
                            <span class="bslm-service-price">
                                <i class="fas fa-tag"></i>
                                <?php echo esc_html($price); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <a href="<?php the_permalink(); ?>" class="bslm-service-button">
                    <?php _e('Read More', 'beauty-salon-services-manager'); ?>
                </a>
            </div>
        </div>
        <?php
    }
}
