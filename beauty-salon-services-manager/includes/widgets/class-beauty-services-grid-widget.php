<?php
/**
 * Beauty Services Grid Widget for Elementor.
 *
 * @package Beauty_Salon_Services_Manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BSLM_Beauty_Services_Grid_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'beauty-services-grid';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return __('Beauty Services Grid', 'beauty-salon-services-manager');
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-gallery-grid';
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
        return array('beauty', 'services', 'salon', 'grid', 'laser');
    }

    /**
     * Get style dependencies.
     */
    public function get_style_depends() {
        return array('beauty-salon-services-manager');
    }

    /**
     * Get script dependencies.
     */
    public function get_script_depends() {
        return array('beauty-salon-services-manager');
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Content Tab - Service Selection
        $this->start_controls_section(
            'section_service_selection',
            array(
                'label' => __('Service Selection', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'query_type',
            array(
                'label' => __('Query Type', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => array(
                    'all' => __('All Services', 'beauty-salon-services-manager'),
                    'by_parent' => __('By Parent Service', 'beauty-salon-services-manager'),
                    'parent_only' => __('Parent Services Only', 'beauty-salon-services-manager'),
                    'children_only' => __('Child Services Only', 'beauty-salon-services-manager'),
                    'by_tag' => __('By Tag', 'beauty-salon-services-manager'),
                    'manual' => __('Manual Selection', 'beauty-salon-services-manager'),
                ),
            )
        );

        // Get all parent services (services with no parent)
        $parent_services = get_posts(array(
            'post_type' => 'bslm_service',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'post_parent' => 0,
            'orderby' => 'title',
            'order' => 'ASC',
        ));

        $parent_options = array();
        if (!empty($parent_services)) {
            foreach ($parent_services as $service) {
                $parent_options[$service->ID] = $service->post_title;
            }
        }

        $this->add_control(
            'parent_service',
            array(
                'label' => __('Select Parent Service', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $parent_options,
                'condition' => array(
                    'query_type' => 'by_parent',
                ),
            )
        );

        // Get all service tags
        $service_tags = get_terms(array(
            'taxonomy' => 'bslm_service_tag',
            'hide_empty' => false,
        ));

        $tag_options = array();
        if (!empty($service_tags) && !is_wp_error($service_tags)) {
            foreach ($service_tags as $tag) {
                $tag_options[$tag->term_id] = $tag->name;
            }
        }

        $this->add_control(
            'selected_tags',
            array(
                'label' => __('Select Tags', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $tag_options,
                'label_block' => true,
                'condition' => array(
                    'query_type' => 'by_tag',
                ),
            )
        );

        $this->add_control(
            'tag_operator',
            array(
                'label' => __('Tag Operator', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'IN',
                'options' => array(
                    'IN' => __('Match ANY (OR)', 'beauty-salon-services-manager'),
                    'AND' => __('Match ALL (AND)', 'beauty-salon-services-manager'),
                ),
                'description' => __('Match services with ANY or ALL selected tags.', 'beauty-salon-services-manager'),
                'condition' => array(
                    'query_type' => 'by_tag',
                ),
            )
        );

        // Get all services for manual selection
        $services = get_posts(array(
            'post_type' => 'bslm_service',
            'posts_per_page' => -1,
            'post_status' => 'publish',
        ));

        $service_options = array();
        if (!empty($services)) {
            foreach ($services as $service) {
                $service_options[$service->ID] = $service->post_title;
            }
        }

        $this->add_control(
            'manual_services',
            array(
                'label' => __('Select Services', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $service_options,
                'condition' => array(
                    'query_type' => 'manual',
                ),
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label' => __('Number of Services', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => -1,
                'description' => __('Set to -1 to display all services', 'beauty-salon-services-manager'),
                'condition' => array(
                    'query_type!' => 'manual',
                ),
            )
        );

        $this->add_control(
            'orderby',
            array(
                'label' => __('Order By', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => array(
                    'date' => __('Date', 'beauty-salon-services-manager'),
                    'title' => __('Title', 'beauty-salon-services-manager'),
                    'menu_order' => __('Menu Order', 'beauty-salon-services-manager'),
                    'rand' => __('Random', 'beauty-salon-services-manager'),
                ),
            )
        );

        $this->add_control(
            'order',
            array(
                'label' => __('Order', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'DESC',
                'options' => array(
                    'ASC' => __('Ascending', 'beauty-salon-services-manager'),
                    'DESC' => __('Descending', 'beauty-salon-services-manager'),
                ),
            )
        );

        $this->end_controls_section();

        // Content Tab - Display Options
        $this->start_controls_section(
            'section_display_options',
            array(
                'label' => __('Display Options', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'show_image',
            array(
                'label' => __('Show Service Image', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'card_layout',
            array(
                'label' => __('Card Layout', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'vertical',
                'options' => array(
                    'vertical' => __('Vertical (Image Top)', 'beauty-salon-services-manager'),
                    'horizontal-left' => __('Horizontal (Image Left)', 'beauty-salon-services-manager'),
                    'horizontal-right' => __('Horizontal (Image Right)', 'beauty-salon-services-manager'),
                    'two-column-grid' => __('Button Style (Icon/Image + Title)', 'beauty-salon-services-manager'),
                ),
                'condition' => array(
                    'show_image' => 'yes',
                ),
            )
        );

        $this->add_control(
            'vertical_alignment',
            array(
                'label' => __('Content Vertical Alignment', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top',
                'options' => array(
                    'top' => __('Top', 'beauty-salon-services-manager'),
                    'middle' => __('Middle', 'beauty-salon-services-manager'),
                    'bottom' => __('Bottom', 'beauty-salon-services-manager'),
                ),
                'condition' => array(
                    'show_image' => 'yes',
                    'card_layout!' => 'vertical',
                ),
            )
        );

        $this->add_control(
            'image_display_mode',
            array(
                'label' => __('Image Display Mode', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'full',
                'options' => array(
                    'full' => __('Full Image', 'beauty-salon-services-manager'),
                    'small' => __('Small Image with Padding', 'beauty-salon-services-manager'),
                    'icon' => __('Icon/Small Icon', 'beauty-salon-services-manager'),
                ),
                'condition' => array(
                    'show_image' => 'yes',
                ),
                'separator' => 'before',
            )
        );

        $this->add_control(
            'use_service_icon',
            array(
                'label' => __('Use Service Icon', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Display the service icon instead of featured image (if icon is set)', 'beauty-salon-services-manager'),
                'condition' => array(
                    'show_image' => 'yes',
                    'image_display_mode' => 'icon',
                ),
            )
        );

        $this->add_control(
            'image_padding',
            array(
                'label' => __('Image/Icon Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image.display-small img, {{WRAPPER}} .bslm-service-image.display-icon img' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
                'condition' => array(
                    'show_image' => 'yes',
                    'image_display_mode!' => 'full',
                ),
            )
        );

        $this->add_responsive_control(
            'button_image_width',
            array(
                'label' => __('Image Column Width', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('%', 'px'),
                'range' => array(
                    '%' => array(
                        'min' => 10,
                        'max' => 50,
                        'step' => 1,
                    ),
                    'px' => array(
                        'min' => 50,
                        'max' => 300,
                        'step' => 1,
                    ),
                ),
                'default' => array(
                    'unit' => '%',
                    'size' => 25,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-grid-card.layout-two-column-grid .bslm-service-image' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bslm-grid-card.layout-two-column-grid .bslm-service-content' => 'width: calc(100% - {{SIZE}}{{UNIT}});',
                ),
                'condition' => array(
                    'show_image' => 'yes',
                    'card_layout' => 'two-column-grid',
                ),
            )
        );

        $this->add_control(
            'button_keep_mobile',
            array(
                'label' => __('Keep Button Style on Mobile', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('By default, button style switches to vertical layout on mobile. Enable this to keep the button style on mobile devices.', 'beauty-salon-services-manager'),
                'condition' => array(
                    'show_image' => 'yes',
                    'card_layout' => 'two-column-grid',
                ),
            )
        );

        $this->add_control(
            'show_title',
            array(
                'label' => __('Show Service Title', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_description',
            array(
                'label' => __('Show Service Description', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'description_length',
            array(
                'label' => __('Description Length (words)', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'condition' => array(
                    'show_description' => 'yes',
                ),
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
            'show_button',
            array(
                'label' => __('Show Read More Button', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'button_text',
            array(
                'label' => __('Button Text', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Learn More', 'beauty-salon-services-manager'),
                'condition' => array(
                    'show_button' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Layout
        $this->start_controls_section(
            'section_layout_style',
            array(
                'label' => __('Layout', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_responsive_control(
            'columns',
            array(
                'label' => __('Columns', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '3',
                'tablet_default' => '2',
                'mobile_default' => '1',
                'options' => array(
                    '1' => '1',
                    '2' => '2',
                    '3' => '3',
                    '4' => '4',
                    '5' => '5',
                    '6' => '6',
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-services-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
                ),
            )
        );

        $this->add_responsive_control(
            'column_gap',
            array(
                'label' => __('Column Gap', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'default' => array(
                    'size' => 30,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-services-grid' => 'column-gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'row_gap',
            array(
                'label' => __('Row Gap', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 100,
                    ),
                ),
                'default' => array(
                    'size' => 30,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-services-grid' => 'row-gap: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Service Card
        $this->start_controls_section(
            'section_card_style',
            array(
                'label' => __('Service Card', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_background',
            array(
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-grid-card' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'card_border',
                'label' => __('Border', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-grid-card',
            )
        );

        $this->add_responsive_control(
            'card_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-grid-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'card_box_shadow',
                'label' => __('Box Shadow', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-grid-card',
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-grid-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'content_align',
            array(
                'label' => __('Content Alignment', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __('Right', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-content' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'content_padding',
            array(
                'label' => __('Content Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'default' => array(
                    'top' => '20',
                    'right' => '20',
                    'bottom' => '20',
                    'left' => '20',
                    'unit' => 'px',
                    'isLinked' => true,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Image
        $this->start_controls_section(
            'section_image_style',
            array(
                'label' => __('Image', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_image' => 'yes',
                ),
            )
        );

        $this->add_responsive_control(
            'image_height',
            array(
                'label' => __('Image Height', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px'),
                'range' => array(
                    'px' => array(
                        'min' => 100,
                        'max' => 500,
                    ),
                ),
                'default' => array(
                    'size' => 250,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'height: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'image_object_fit',
            array(
                'label' => __('Object Fit', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => array(
                    'cover' => __('Cover', 'beauty-salon-services-manager'),
                    'contain' => __('Contain', 'beauty-salon-services-manager'),
                    'fill' => __('Fill', 'beauty-salon-services-manager'),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'object-fit: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'image_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Title
        $this->start_controls_section(
            'section_title_style',
            array(
                'label' => __('Title', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_title' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'title_typography',
                'label' => __('Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-title',
            )
        );

        // Title Color - Normal/Hover Tabs
        $this->start_controls_tabs('title_color_tabs');

        // Normal Tab
        $this->start_controls_tab(
            'title_color_normal',
            array(
                'label' => __('Normal', 'beauty-salon-services-manager'),
            )
        );

        $this->add_control(
            'title_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bslm-service-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        // Hover Tab
        $this->start_controls_tab(
            'title_color_hover',
            array(
                'label' => __('Hover', 'beauty-salon-services-manager'),
            )
        );

        $this->add_control(
            'title_color_hover',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#007cba',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bslm-service-title a:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bslm-grid-card:hover .bslm-service-title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bslm-grid-card:hover .bslm-service-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_responsive_control(
            'title_align',
            array(
                'label' => __('Alignment', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __('Right', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'title_margin',
            array(
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Description
        $this->start_controls_section(
            'section_description_style',
            array(
                'label' => __('Description', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_description' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'description_typography',
                'label' => __('Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-description',
            )
        );

        $this->add_control(
            'description_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#666666',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-description' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_responsive_control(
            'description_align',
            array(
                'label' => __('Alignment', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => array(
                    'left' => array(
                        'title' => __('Left', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-left',
                    ),
                    'center' => array(
                        'title' => __('Center', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-center',
                    ),
                    'right' => array(
                        'title' => __('Right', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-right',
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-description' => 'text-align: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Meta (Time/Price)
        $this->start_controls_section(
            'section_meta_style',
            array(
                'label' => __('Meta (Time/Price)', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'meta_typography',
                'label' => __('Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-meta span',
            )
        );

        $this->add_control(
            'meta_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta span' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'show_meta_icons',
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
            'meta_icon_color',
            array(
                'label' => __('Icon Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#999999',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-meta i' => 'color: {{VALUE}};',
                ),
                'condition' => array(
                    'show_meta_icons' => 'yes',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Button
        $this->start_controls_section(
            'section_button_style',
            array(
                'label' => __('Button', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_button' => 'yes',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'button_typography',
                'label' => __('Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-button',
            )
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab(
            'button_normal_tab',
            array(
                'label' => __('Normal', 'beauty-salon-services-manager'),
            )
        );

        $this->add_control(
            'button_text_color',
            array(
                'label' => __('Text Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_background_color',
            array(
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover_tab',
            array(
                'label' => __('Hover', 'beauty-salon-services-manager'),
            )
        );

        $this->add_control(
            'button_hover_text_color',
            array(
                'label' => __('Text Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button:hover' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'button_hover_background_color',
            array(
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#555555',
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'button_border',
                'label' => __('Border', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-button',
            )
        );

        $this->add_responsive_control(
            'button_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'button_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        // Build query arguments
        $args = array(
            'post_type' => 'bslm_service',
            'post_status' => 'publish',
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
        );

        // Handle different query types
        if ($settings['query_type'] === 'by_parent' && !empty($settings['parent_service'])) {
            // Get children of selected parent services
            $args['post_parent__in'] = $settings['parent_service'];
            $args['posts_per_page'] = $settings['posts_per_page'];
        } elseif ($settings['query_type'] === 'parent_only') {
            // Only parent services (no parent)
            $args['post_parent'] = 0;
            $args['posts_per_page'] = $settings['posts_per_page'];
        } elseif ($settings['query_type'] === 'children_only') {
            // Only child services (has parent)
            $args['post_parent__not_in'] = array(0);
            $args['posts_per_page'] = $settings['posts_per_page'];
        } elseif ($settings['query_type'] === 'by_tag' && !empty($settings['selected_tags'])) {
            // Filter by tags
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'bslm_service_tag',
                    'field' => 'term_id',
                    'terms' => $settings['selected_tags'],
                    'operator' => isset($settings['tag_operator']) ? $settings['tag_operator'] : 'IN',
                ),
            );
            $args['posts_per_page'] = $settings['posts_per_page'];
        } elseif ($settings['query_type'] === 'manual' && !empty($settings['manual_services'])) {
            $args['post__in'] = $settings['manual_services'];
            $args['posts_per_page'] = -1;
        } else {
            $args['posts_per_page'] = $settings['posts_per_page'];
        }

        // Apply filter for extensibility
        $args = apply_filters('bslm_service_query_args', $args, $settings);

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<div id="bslm-services-grid-' . esc_attr($this->get_id()) . '" class="bslm-services-grid">';

            while ($query->have_posts()) {
                $query->the_post();
                $this->render_service_card($settings);
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No services found.', 'beauty-salon-services-manager') . '</p>';
        }
    }

    /**
     * Render individual service card.
     */
    protected function render_service_card($settings) {
        $post_id = get_the_ID();
        $time = get_post_meta($post_id, '_bslm_service_time', true);
        $price_options = get_post_meta($post_id, '_bslm_service_price_options', true);

        // Get first price option for card display
        $price_display = '';
        if (is_array($price_options) && !empty($price_options)) {
            $price_display = $price_options[0]['price'];
        }

        $card_classes = array('bslm-grid-card');

        // Add layout class
        if (isset($settings['card_layout'])) {
            $card_classes[] = 'layout-' . $settings['card_layout'];
        }

        // Add vertical alignment class for horizontal layouts
        if (isset($settings['vertical_alignment']) && isset($settings['card_layout']) && $settings['card_layout'] !== 'vertical') {
            $card_classes[] = 'align-' . $settings['vertical_alignment'];
        }

        // Add mobile class for button layout if enabled
        if (isset($settings['button_keep_mobile']) && $settings['button_keep_mobile'] === 'yes' && isset($settings['card_layout']) && $settings['card_layout'] === 'two-column-grid') {
            $card_classes[] = 'button-mobile';
        }

        $card_classes = apply_filters('bslm_service_card_classes', $card_classes, $post_id);

        // Check if this is a two-column-grid layout (button-like display)
        $is_button_layout = isset($settings['card_layout']) && $settings['card_layout'] === 'two-column-grid';

        ?>
        <div class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">
            <?php
            // Action hook before content
            do_action('bslm_before_service_content', $post_id);

            // Image
            if ($settings['show_image'] === 'yes') {
                $image_classes = array('bslm-service-image');
                $display_mode = isset($settings['image_display_mode']) ? $settings['image_display_mode'] : 'full';
                $use_icon = isset($settings['use_service_icon']) && $settings['use_service_icon'] === 'yes';

                // Add display mode class
                $image_classes[] = 'display-' . $display_mode;

                // Determine which image to display
                $image_id = null;
                $image_size = 'large';

                if ($display_mode === 'icon' && $use_icon) {
                    // Try to get service icon first
                    $icon_id = get_post_meta($post_id, '_bslm_service_icon', true);
                    if ($icon_id) {
                        $image_id = $icon_id;
                        $image_size = 'thumbnail';
                    }
                }

                // Fallback to featured image
                if (!$image_id && has_post_thumbnail()) {
                    $image_id = get_post_thumbnail_id($post_id);
                    if ($display_mode === 'small' || $display_mode === 'icon') {
                        $image_size = 'medium';
                    }
                }

                if ($image_id) {
                    ?>
                    <div class="<?php echo esc_attr(implode(' ', $image_classes)); ?>">
                        <a href="<?php the_permalink(); ?>">
                            <?php echo wp_get_attachment_image($image_id, $image_size); ?>
                        </a>
                    </div>
                    <?php
                }
            }
            ?>

            <div class="bslm-service-content">
                <?php
                // Title
                if ($settings['show_title'] === 'yes') {
                    ?>
                    <h3 class="bslm-service-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <?php
                }

                // Description
                if ($settings['show_description'] === 'yes') {
                    $excerpt_length = $settings['description_length'];
                    $excerpt = wp_trim_words(get_the_excerpt(), $excerpt_length, '...');
                    ?>
                    <div class="bslm-service-description">
                        <?php echo wp_kses_post($excerpt); ?>
                    </div>
                    <?php
                }

                // Meta (Time/Price)
                if (($settings['show_time'] === 'yes' && $time) || ($settings['show_price'] === 'yes' && $price_display)) {
                    ?>
                    <div class="bslm-service-meta">
                        <?php
                        do_action('bslm_service_meta_display', $post_id);

                        if ($settings['show_time'] === 'yes' && $time) {
                            ?>
                            <span class="bslm-service-time">
                                <?php if ($settings['show_meta_icons'] === 'yes') : ?>
                                    <i class="far fa-clock"></i>
                                <?php endif; ?>
                                <?php echo esc_html($time); ?>
                            </span>
                            <?php
                        }

                        if ($settings['show_price'] === 'yes' && $price_display) {
                            $price_display = apply_filters('bslm_service_price_format', $price_display, $post_id);
                            ?>
                            <span class="bslm-service-price">
                                <?php if ($settings['show_meta_icons'] === 'yes') : ?>
                                    <i class="fas fa-tag"></i>
                                <?php endif; ?>
                                <?php echo esc_html($price_display); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }

                // Button (hidden for button layout)
                if ($settings['show_button'] === 'yes' && !$is_button_layout) {
                    ?>
                    <a href="<?php the_permalink(); ?>" class="bslm-service-button">
                        <?php echo esc_html($settings['button_text']); ?>
                    </a>
                    <?php
                }
                ?>
            </div>

            <?php
            // Action hook after content
            do_action('bslm_after_service_content', $post_id);
            ?>
        </div>
        <?php
    }
}
