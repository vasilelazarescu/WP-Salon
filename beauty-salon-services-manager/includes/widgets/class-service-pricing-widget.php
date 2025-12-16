<?php
/**
 * Service Pricing Widget for Elementor
 *
 * Displays service duration, base price, and tier/package pricing options
 *
 * @package Beauty_Salon_Services_Manager
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class BSLM_Service_Pricing_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     */
    public function get_name() {
        return 'service-pricing';
    }

    /**
     * Get widget title.
     */
    public function get_title() {
        return __('Service Pricing & Packages', 'beauty-salon-services-manager');
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
        return ['beauty-salon'];
    }

    /**
     * Get widget keywords.
     */
    public function get_keywords() {
        return ['service', 'pricing', 'price', 'packages', 'tiers', 'duration', 'beauty'];
    }

    /**
     * Register widget controls.
     */
    protected function register_controls() {
        // Content Section
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_duration',
            [
                'label' => __('Show Duration', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'beauty-salon-services-manager'),
                'label_off' => __('Hide', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'duration_label',
            [
                'label' => __('Duration Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Durée:', 'beauty-salon-services-manager'),
                'condition' => [
                    'show_duration' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_base_price',
            [
                'label' => __('Show Base Price', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'beauty-salon-services-manager'),
                'label_off' => __('Hide', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'base_price_label',
            [
                'label' => __('Base Price Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Tarif:', 'beauty-salon-services-manager'),
                'condition' => [
                    'show_base_price' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_packages',
            [
                'label' => __('Show Package Pricing', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'beauty-salon-services-manager'),
                'label_off' => __('Hide', 'beauty-salon-services-manager'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'packages_label',
            [
                'label' => __('Packages Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Tarif Forfaits:', 'beauty-salon-services-manager'),
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'sessions_label',
            [
                'label' => __('Sessions Label', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('séances', 'beauty-salon-services-manager'),
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'savings_text',
            [
                'label' => __('Savings Text', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('save', 'beauty-salon-services-manager'),
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'savings_format',
            [
                'label' => __('Savings Display Format', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'amount',
                'options' => [
                    'amount' => __('Amount (€)', 'beauty-salon-services-manager'),
                    'percentage' => __('Percentage (%)', 'beauty-salon-services-manager'),
                    'both' => __('Both (Amount + %)', 'beauty-salon-services-manager'),
                ],
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'package_columns',
            [
                'label' => __('Package Columns', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => '2',
                'options' => [
                    '1' => __('1 Column', 'beauty-salon-services-manager'),
                    '2' => __('2 Columns', 'beauty-salon-services-manager'),
                    '3' => __('3 Columns', 'beauty-salon-services-manager'),
                    '4' => __('4 Columns', 'beauty-salon-services-manager'),
                ],
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - General
        $this->start_controls_section(
            'style_general',
            [
                'label' => __('General Styling', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'general_alignment',
            [
                'label' => __('Alignment', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'beauty-salon-services-manager'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-widget' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_spacing',
            [
                'label' => __('Section Spacing', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-section' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Duration
        $this->start_controls_section(
            'style_duration',
            [
                'label' => __('Duration', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_duration' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'duration_typography',
                'selector' => '{{WRAPPER}} .bslm-pricing-duration',
            ]
        );

        $this->add_control(
            'duration_color',
            [
                'label' => __('Text Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-duration' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'duration_margin',
            [
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-duration' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Base Price
        $this->start_controls_section(
            'style_base_price',
            [
                'label' => __('Base Price', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_base_price' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'base_price_typography',
                'selector' => '{{WRAPPER}} .bslm-pricing-base',
            ]
        );

        $this->add_control(
            'base_price_color',
            [
                'label' => __('Text Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-base' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'base_price_margin',
            [
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-base' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Packages Header
        $this->start_controls_section(
            'style_packages_header',
            [
                'label' => __('Packages Header', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'packages_header_typography',
                'selector' => '{{WRAPPER}} .bslm-pricing-packages-header',
            ]
        );

        $this->add_control(
            'packages_header_color',
            [
                'label' => __('Text Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-packages-header' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'packages_header_margin',
            [
                'label' => __('Margin', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bslm-pricing-packages-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Package Cards
        $this->start_controls_section(
            'style_package_cards',
            [
                'label' => __('Package Cards', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'package_card_background',
            [
                'label' => __('Background Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-card' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'package_card_border',
                'selector' => '{{WRAPPER}} .bslm-package-card',
            ]
        );

        $this->add_responsive_control(
            'package_card_border_radius',
            [
                'label' => __('Border Radius', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'package_card_shadow',
                'selector' => '{{WRAPPER}} .bslm-package-card',
            ]
        );

        $this->add_responsive_control(
            'package_card_padding',
            [
                'label' => __('Padding', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'top' => 20,
                    'right' => 20,
                    'bottom' => 20,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'package_gap',
            [
                'label' => __('Gap Between Cards', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bslm-packages-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Package Text
        $this->start_controls_section(
            'style_package_text',
            [
                'label' => __('Package Text', 'beauty-salon-services-manager'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_packages' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'package_sessions_typography',
                'label' => __('Sessions Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-package-sessions',
            ]
        );

        $this->add_control(
            'package_sessions_color',
            [
                'label' => __('Sessions Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#333333',
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-sessions' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'package_price_typography',
                'label' => __('Price Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-package-price',
            ]
        );

        $this->add_control(
            'package_price_color',
            [
                'label' => __('Price Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#000000',
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'package_savings_typography',
                'label' => __('Savings Typography', 'beauty-salon-services-manager'),
                'selector' => '{{WRAPPER}} .bslm-package-savings',
            ]
        );

        $this->add_control(
            'package_savings_color',
            [
                'label' => __('Savings Color', 'beauty-salon-services-manager'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'default' => '#ff6b6b',
                'selectors' => [
                    '{{WRAPPER}} .bslm-package-savings' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    /**
     * Render widget output on the frontend.
     */
    protected function render() {
        // Only display on single service pages
        if (!is_singular('bslm_service')) {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
                echo '<p>' . __('This widget only displays on single service pages.', 'beauty-salon-services-manager') . '</p>';
            }
            return;
        }

        $settings = $this->get_settings_for_display();
        $post_id = get_the_ID();

        // Get service data
        $duration = get_post_meta($post_id, '_bslm_service_time', true);
        $price_options = get_post_meta($post_id, '_bslm_service_price_options', true);

        // Ensure price_options is an array
        if (!is_array($price_options) || empty($price_options)) {
            $price_options = array();
        }

        // Sort price options by sessions
        usort($price_options, function($a, $b) {
            return intval($a['sessions']) - intval($b['sessions']);
        });

        // Get base price (1 session)
        $base_price = '';
        foreach ($price_options as $option) {
            if (intval($option['sessions']) === 1) {
                $base_price = $option['price'];
                break;
            }
        }

        // Filter out packages (more than 1 session)
        $packages = array_filter($price_options, function($option) {
            return intval($option['sessions']) > 1;
        });

        ?>
        <div class="bslm-pricing-widget">
            <?php if ($settings['show_duration'] === 'yes' && !empty($duration)) : ?>
                <div class="bslm-pricing-section bslm-pricing-duration">
                    <?php echo esc_html($settings['duration_label']); ?>
                    <strong><?php echo esc_html($duration); ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_base_price'] === 'yes' && !empty($base_price)) : ?>
                <div class="bslm-pricing-section bslm-pricing-base">
                    <?php echo esc_html($settings['base_price_label']); ?>
                    <strong><?php echo esc_html($base_price); ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($settings['show_packages'] === 'yes' && !empty($packages) && !empty($base_price)) : ?>
                <div class="bslm-pricing-section bslm-pricing-packages">
                    <h3 class="bslm-pricing-packages-header">
                        <?php echo esc_html($settings['packages_label']); ?>
                    </h3>

                    <div class="bslm-packages-grid bslm-packages-cols-<?php echo esc_attr($settings['package_columns']); ?>">
                        <?php foreach ($packages as $package) :
                            $sessions = intval($package['sessions']);
                            $package_price = $package['price'];

                            // Calculate savings
                            $base_price_numeric = floatval(preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $base_price)));
                            $package_price_numeric = floatval(preg_replace('/[^0-9.,]/', '', str_replace(',', '.', $package_price)));

                            if ($base_price_numeric > 0) {
                                $expected_price = $base_price_numeric * $sessions;
                                $savings_amount = $expected_price - $package_price_numeric;
                                $savings_percentage = ($savings_amount / $expected_price) * 100;
                                $savings_percentage = round($savings_percentage);

                                // Extract currency symbol from price
                                $currency = preg_replace('/[0-9.,\s]/', '', $package_price);
                                if (empty($currency)) {
                                    $currency = '€'; // Default to Euro
                                }
                            } else {
                                $savings_percentage = 0;
                                $savings_amount = 0;
                                $currency = '€';
                            }

                            // Format savings display based on user preference
                            $savings_display = '';
                            if ($savings_percentage > 0 || $savings_amount > 0) {
                                $format = isset($settings['savings_format']) ? $settings['savings_format'] : 'amount';

                                switch ($format) {
                                    case 'percentage':
                                        $savings_display = $savings_percentage . '%';
                                        break;
                                    case 'both':
                                        $savings_display = number_format($savings_amount, 0, ',', ' ') . $currency . ' (' . $savings_percentage . '%)';
                                        break;
                                    case 'amount':
                                    default:
                                        $savings_display = number_format($savings_amount, 0, ',', ' ') . $currency;
                                        break;
                                }
                            }
                        ?>
                            <div class="bslm-package-card">
                                <div class="bslm-package-sessions">
                                    <?php echo esc_html($sessions); ?>
                                    <?php echo esc_html($settings['sessions_label']); ?>
                                </div>
                                <div class="bslm-package-price">
                                    <?php echo esc_html($package_price); ?>
                                </div>
                                <?php if (!empty($savings_display)) : ?>
                                    <div class="bslm-package-savings">
                                        <?php echo esc_html($settings['savings_text']); ?>
                                        <?php echo esc_html($savings_display); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
}
