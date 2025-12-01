<?php
/**
 * Services By Tag Widget for Elementor.
 *
 * @package Beauty_Salon_Services_Manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BSLM_Services_By_Tag_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'services-by-tag';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return __('Services By Tag', 'beauty-salon-services-manager');
    }

    /**
     * Get widget icon.
     */
    public function get_icon() {
        return 'eicon-tags';
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
        return array('beauty', 'services', 'salon', 'tag', 'tags', 'filter');
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
        // Content Tab - Tag Selection
        $this->start_controls_section(
            'section_tag_selection',
            array(
                'label' => __('Tag Selection', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
                'default' => array(),
                'label_block' => true,
                'description' => __('Select one or more tags to filter services. Leave empty to show all services.', 'beauty-salon-services-manager'),
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
                'description' => __('Choose whether services should have ANY of the selected tags or ALL of them.', 'beauty-salon-services-manager'),
                'condition' => array(
                    'selected_tags!' => '',
                ),
            )
        );

        $this->add_control(
            'posts_per_page',
            array(
                'label' => __('Number of Services', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 9,
                'min' => -1,
                'step' => 1,
                'description' => __('Number of services to display. Use -1 to show all.', 'beauty-salon-services-manager'),
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
                'condition' => array(
                    'orderby!' => 'rand',
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
                'label' => __('Show Image', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            )
        );

        $this->add_control(
            'show_title',
            array(
                'label' => __('Show Title', 'beauty-salon-services-manager'),
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
                'label' => __('Show Description', 'beauty-salon-services-manager'),
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
                'min' => 5,
                'max' => 100,
                'step' => 5,
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
            'show_tags',
            array(
                'label' => __('Show Tags', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'beauty-salon-services-manager'),
                'label_off' => __('No', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'no',
                'description' => __('Display service tags on each card.', 'beauty-salon-services-manager'),
            )
        );

        $this->add_control(
            'show_button',
            array(
                'label' => __('Show Button', 'beauty-salon-services-manager'),
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
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'card_border',
                'selector' => '{{WRAPPER}} .bslm-service-card',
            )
        );

        $this->add_control(
            'card_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'card_box_shadow',
                'selector' => '{{WRAPPER}} .bslm-service-card',
            )
        );

        $this->add_responsive_control(
            'card_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'card_margin',
            array(
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'card_transition',
            array(
                'label' => __('Transition Duration (ms)', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 1000,
                    ),
                ),
                'default' => array(
                    'size' => 300,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card' => 'transition: all {{SIZE}}ms ease;',
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
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Hover Effects
        $this->start_controls_section(
            'section_card_hover',
            array(
                'label' => __('Card Hover Effects', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'card_hover_background',
            array(
                'label' => __('Hover Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card:hover' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'card_hover_border_color',
            array(
                'label' => __('Hover Border Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card:hover' => 'border-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'card_hover_box_shadow',
                'label' => __('Hover Box Shadow', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-service-card:hover',
            )
        );

        $this->add_control(
            'card_hover_transform',
            array(
                'label' => __('Hover Transform', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => array(
                    'none' => __('None', 'beauty-salon-services-manager'),
                    'translateY(-5px)' => __('Lift Up', 'beauty-salon-services-manager'),
                    'translateY(-10px)' => __('Lift Up More', 'beauty-salon-services-manager'),
                    'scale(1.03)' => __('Scale Up (Small)', 'beauty-salon-services-manager'),
                    'scale(1.05)' => __('Scale Up (Medium)', 'beauty-salon-services-manager'),
                    'scale(1.08)' => __('Scale Up (Large)', 'beauty-salon-services-manager'),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card:hover' => 'transform: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'title_hover_color',
            array(
                'label' => __('Title Hover Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-card:hover .bslm-service-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'image_hover_effect',
            array(
                'label' => __('Image Hover Effect', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => array(
                    'none' => __('None', 'beauty-salon-services-manager'),
                    'zoom' => __('Zoom In', 'beauty-salon-services-manager'),
                    'zoom-out' => __('Zoom Out', 'beauty-salon-services-manager'),
                    'opacity' => __('Opacity', 'beauty-salon-services-manager'),
                    'grayscale' => __('Remove Grayscale', 'beauty-salon-services-manager'),
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

        $this->add_control(
            'title_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bslm-service-title',
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

        $this->add_responsive_control(
            'title_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'title_text_align',
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
            'image_width',
            array(
                'label' => __('Width', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px', '%'),
                'range' => array(
                    'px' => array(
                        'min' => 50,
                        'max' => 1000,
                    ),
                    '%' => array(
                        'min' => 10,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image' => 'width: {{SIZE}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'image_height',
            array(
                'label' => __('Height', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => array('px', 'vh'),
                'range' => array(
                    'px' => array(
                        'min' => 50,
                        'max' => 800,
                    ),
                    'vh' => array(
                        'min' => 10,
                        'max' => 100,
                    ),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bslm-service-image img' => 'height: 100%;',
                ),
            )
        );

        $this->add_control(
            'image_position',
            array(
                'label' => __('Image Position', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'top',
                'options' => array(
                    'top' => __('Top (Default)', 'beauty-salon-services-manager'),
                    'left' => __('Float Left', 'beauty-salon-services-manager'),
                    'right' => __('Float Right', 'beauty-salon-services-manager'),
                ),
                'description' => __('Choose how the image is positioned relative to text. Float options allow text to wrap around the image.', 'beauty-salon-services-manager'),
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
                    'none' => __('None', 'beauty-salon-services-manager'),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'object-fit: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'image_object_position',
            array(
                'label' => __('Object Position', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'center center',
                'options' => array(
                    'center center' => __('Center Center', 'beauty-salon-services-manager'),
                    'top center' => __('Top Center', 'beauty-salon-services-manager'),
                    'top left' => __('Top Left', 'beauty-salon-services-manager'),
                    'top right' => __('Top Right', 'beauty-salon-services-manager'),
                    'center left' => __('Center Left', 'beauty-salon-services-manager'),
                    'center right' => __('Center Right', 'beauty-salon-services-manager'),
                    'bottom center' => __('Bottom Center', 'beauty-salon-services-manager'),
                    'bottom left' => __('Bottom Left', 'beauty-salon-services-manager'),
                    'bottom right' => __('Bottom Right', 'beauty-salon-services-manager'),
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'object-position: {{VALUE}};',
                ),
                'condition' => array(
                    'image_object_fit!' => 'fill',
                ),
            )
        );

        $this->add_responsive_control(
            'image_margin',
            array(
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_responsive_control(
            'image_padding',
            array(
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', 'em', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_control(
            'image_border_radius',
            array(
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => array('px', '%'),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .bslm-service-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            array(
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .bslm-service-image',
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            array(
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .bslm-service-image',
            )
        );

        $this->add_control(
            'image_opacity',
            array(
                'label' => __('Opacity', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => array(
                    'px' => array(
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.1,
                    ),
                ),
                'default' => array(
                    'size' => 1,
                ),
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-image img' => 'opacity: {{SIZE}};',
                ),
            )
        );

        $this->end_controls_section();

        // Style Tab - Tags
        $this->start_controls_section(
            'section_tags_style',
            array(
                'label' => __('Tags', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => array(
                    'show_tags' => 'yes',
                ),
            )
        );

        $this->add_control(
            'tags_color',
            array(
                'label' => __('Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-tags a' => 'color: {{VALUE}};',
                ),
            )
        );

        $this->add_control(
            'tags_background',
            array(
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => array(
                    '{{WRAPPER}} .bslm-service-tags a' => 'background-color: {{VALUE}};',
                ),
            )
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            array(
                'name' => 'tags_typography',
                'selector' => '{{WRAPPER}} .bslm-service-tags',
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
            'posts_per_page' => $settings['posts_per_page'],
            'orderby' => $settings['orderby'],
            'order' => $settings['order'],
        );

        // Add tax query if tags are selected
        if (!empty($settings['selected_tags'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'bslm_service_tag',
                    'field' => 'term_id',
                    'terms' => $settings['selected_tags'],
                    'operator' => $settings['tag_operator'],
                ),
            );
        }

        // Apply filter for extensibility
        $args = apply_filters('bslm_service_query_args', $args, $settings);

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            echo '<div class="bslm-services-grid bslm-services-by-tag">';

            while ($query->have_posts()) {
                $query->the_post();
                $this->render_service_card($settings);
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p>' . __('No services found with the selected tags.', 'beauty-salon-services-manager') . '</p>';
        }
    }

    /**
     * Render individual service card.
     */
    protected function render_service_card($settings) {
        $post_id = get_the_ID();
        $time = get_post_meta($post_id, '_bslm_service_time', true);
        $price = get_post_meta($post_id, '_bslm_service_price', true);

        $card_classes = array('bslm-service-card');

        // Add image position class
        if (isset($settings['image_position']) && $settings['image_position'] !== 'top') {
            $card_classes[] = 'image-' . $settings['image_position'];
        }

        // Add hover effect class if set
        if (isset($settings['image_hover_effect']) && $settings['image_hover_effect'] !== 'none') {
            $card_classes[] = 'hover-effect-' . $settings['image_hover_effect'];
        }

        $card_classes = apply_filters('bslm_service_card_classes', $card_classes, $post_id);
        ?>
        <div class="<?php echo esc_attr(implode(' ', $card_classes)); ?>">
            <?php
            // Action hook before content
            do_action('bslm_before_service_content', $post_id);

            // Image
            if ($settings['show_image'] === 'yes' && has_post_thumbnail()) {
                ?>
                <div class="bslm-service-image">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('large'); ?>
                    </a>
                </div>
                <?php
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

                // Tags
                if ($settings['show_tags'] === 'yes') {
                    $tags = get_the_terms($post_id, 'bslm_service_tag');
                    if ($tags && !is_wp_error($tags)) {
                        ?>
                        <div class="bslm-service-tags">
                            <?php
                            foreach ($tags as $tag) {
                                echo '<a href="' . esc_url(get_term_link($tag)) . '">' . esc_html($tag->name) . '</a> ';
                            }
                            ?>
                        </div>
                        <?php
                    }
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

                // Meta (Time and Price)
                if (($settings['show_time'] === 'yes' && $time) || ($settings['show_price'] === 'yes' && $price)) {
                    ?>
                    <div class="bslm-service-meta">
                        <?php
                        do_action('bslm_service_meta_display', $post_id);

                        if ($settings['show_time'] === 'yes' && $time) {
                            ?>
                            <span class="bslm-service-time">
                                <i class="fa fa-clock"></i> <?php echo esc_html($time); ?>
                            </span>
                            <?php
                        }

                        if ($settings['show_price'] === 'yes' && $price) {
                            $price = apply_filters('bslm_service_price_format', $price, $post_id);
                            ?>
                            <span class="bslm-service-price">
                                <i class="fa fa-tag"></i> <?php echo esc_html($price); ?>
                            </span>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }

                // Button
                if ($settings['show_button'] === 'yes') {
                    $button_text = !empty($settings['button_text']) ? $settings['button_text'] : __('Learn More', 'beauty-salon-services-manager');
                    $button_text = apply_filters('bslm_service_button_text', $button_text, $post_id);
                    ?>
                    <div class="bslm-service-button">
                        <a href="<?php the_permalink(); ?>" class="bslm-btn">
                            <?php echo esc_html($button_text); ?>
                        </a>
                    </div>
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
