<?php
/**
 * Service Meta Widget for Elementor (Price & Time).
 *
 * @package Beauty_Salon_Services_Manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BSLM_Service_Meta_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'service-meta';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return __('Service Price & Time', 'beauty-salon-services-manager');
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-price-table';
    }

    /**
     * Get widget categories.
     */
    public function get_categories() {
        return array('beauty-salon');
    }

    /**
     * Get widget keywords.
     */
    public function get_keywords() {
        return array('service', 'price', 'time', 'meta', 'duration', 'cost');
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Content Tab - Display Options
        $this->start_controls_section(
            'section_content',
            array(
                'label' => __('Display Options', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_price',
            array(
                'label' => __('Show Price', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_time',
            array(
                'label' => __('Show Time', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'layout',
            array(
                'label' => __('Layout', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'horizontal',
                'options' => array(
                    'horizontal' => __('Horizontal', 'beauty-salon-services-manager'),
                    'vertical' => __('Vertical', 'beauty-salon-services-manager'),
                    'inline' => __('Inline', 'beauty-salon-services-manager'),
                ),
            )
        );

        $this->add_control(
            'show_icons',
            array(
                'label' => __('Show Icons', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'price_label',
            array(
                'label' => __('Price Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Price:', 'beauty-salon-services-manager'),
                'condition' => array(
                    'show_price' => 'yes',
                ),
            )
        );

        $this->add_control(
            'time_label',
            array(
                'label' => __('Time Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Duration:', 'beauty-salon-services-manager'),
                'condition' => array(
                    'show_time' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Container
        $this->start_controls_section(
            'section_container_style',
            array(
                'label' => __('Container', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'container_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta-widget' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'container_margin',
            array(
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta-widget' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'container_background',
            array(
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta-widget' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'container_border',
                'selector' => '{{WRAPPER}} .bslm-service-meta-widget',
            )
        );

        $this->add_responsive_control(
            'container_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta-widget' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'items_gap',
            array(
                'label' => __('Gap Between Items', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'default' => array(
                    'size' => 15,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta-widget.layout-horizontal' => 'gap: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bslm-service-meta-widget.layout-vertical .bslm-meta-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bslm-service-meta-widget.layout-inline' => 'gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Label
        $this->start_controls_section(
            'section_label_style',
            array(
                'label' => __('Label', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'label_typography',
                'selector' => '{{WRAPPER}} .bslm-meta-label',
            )
        );

        $this->add_control(
            'label_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-label' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'label_spacing',
            array(
                'label' => __('Spacing', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 50,
                    ),
                ),
                'default' => array(
                    'size' => 5,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-label' => 'margin-right: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Value
        $this->start_controls_section(
            'section_value_style',
            array(
                'label' => __('Value', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'value_typography',
                'selector' => '{{WRAPPER}} .bslm-meta-value',
            )
        );

        $this->add_control(
            'value_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-value' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Icon
        $this->start_controls_section(
            'section_icon_style',
            array(
                'label' => __('Icon', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_icons' => 'yes',
                ),
            )
        );

        $this->add_responsive_control(
            'icon_size',
            array(
                'label' => __('Size', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 10,
                        'max' => 50,
                    ),
                ),
                'default' => array(
                    'size' => 16,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'icon_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-icon' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'icon_spacing',
            array(
                'label' => __('Spacing', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 30,
                    ),
                ),
                'default' => array(
                    'size' => 8,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-meta-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        $settings = $this->get_settings_for_display();

        // Only display on single service pages
        if (!is_singular('bslm_service')) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<p>' . __('This widget displays service price and time. It will only show on single service pages.', 'beauty-salon-services-manager') . '</p>';
            }
            return;
        }

        $post_id = get_the_ID();
        $price = get_post_meta($post_id, '_bslm_service_price', true);
        $time = get_post_meta($post_id, '_bslm_service_time', true);

        // Check if we have data to display
        if (($settings['show_price'] !== 'yes' || empty($price)) && ($settings['show_time'] !== 'yes' || empty($time))) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<p>' . __('No price or time data available for this service.', 'beauty-salon-services-manager') . '</p>';
            }
            return;
        }

        $layout_class = 'layout-' . $settings['layout'];
        ?>
        <div class="bslm-service-meta-widget <?php echo esc_attr($layout_class); ?>">
            <?php if ($settings['show_price'] === 'yes' && !empty($price)) : ?>
                <div class="bslm-meta-item bslm-meta-price">
                    <?php if ($settings['show_icons'] === 'yes') : ?>
                        <i class="fas fa-tag bslm-meta-icon"></i>
                    <?php endif; ?>
                    <?php if (!empty($settings['price_label'])) : ?>
                        <span class="bslm-meta-label"><?php echo esc_html($settings['price_label']); ?></span>
                    <?php endif; ?>
                    <span class="bslm-meta-value"><?php echo esc_html(apply_filters('bslm_service_price_format', $price, $post_id)); ?></span>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_time'] === 'yes' && !empty($time)) : ?>
                <div class="bslm-meta-item bslm-meta-time">
                    <?php if ($settings['show_icons'] === 'yes') : ?>
                        <i class="far fa-clock bslm-meta-icon"></i>
                    <?php endif; ?>
                    <?php if (!empty($settings['time_label'])) : ?>
                        <span class="bslm-meta-label"><?php echo esc_html($settings['time_label']); ?></span>
                    <?php endif; ?>
                    <span class="bslm-meta-value"><?php echo esc_html($time); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
