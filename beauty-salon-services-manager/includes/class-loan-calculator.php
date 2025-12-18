<?php
/**
 * Loan Calculator Shortcode.
 *
 * @package Beauty_Salon_Services_Manager
 */

class BSLM_Loan_Calculator {

    /**
     * Constructor.
     */
    public function __construct() {
        add_shortcode('bslm_loan_calculator', array($this, 'loan_calculator_shortcode'));
    }

    /**
     * Loan calculator shortcode.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function loan_calculator_shortcode($atts) {
        $atts = shortcode_atts(array(
            'style' => 'modern', // modern, simple
            'default_amount' => '100000',
            'default_rate' => '1.26',
            'default_duration' => '24',
            'currency_symbol' => '£',
            'currency_position' => 'before', // before, after
            'show_daily_payment' => 'yes',
            'show_eligibility_check' => 'yes',
        ), $atts, 'bslm_loan_calculator');

        // Enqueue calculator-specific assets
        wp_enqueue_style('bslm-loan-calculator');
        wp_enqueue_script('bslm-loan-calculator');

        ob_start();
        $this->render_calculator($atts);
        return ob_get_clean();
    }

    /**
     * Render the loan calculator HTML.
     *
     * @param array $atts Shortcode attributes.
     */
    private function render_calculator($atts) {
        ?>
        <div class="bslm-loan-calculator" data-style="<?php echo esc_attr($atts['style']); ?>">
            <div class="bslm-calc-container">
                <div class="bslm-calc-header">
                    <h2 class="bslm-calc-title">
                        <?php _e('Business Loan Calculator', 'beauty-salon-services-manager'); ?>
                    </h2>
                    <p class="bslm-calc-subtitle">
                        <?php _e('Get an estimate of how much you might be able to borrow in under a minute.', 'beauty-salon-services-manager'); ?>
                    </p>
                </div>

                <div class="bslm-calc-content">
                    <div class="bslm-calc-inputs">
                        <div class="bslm-calc-input-group">
                            <label for="bslm-loan-amount" class="bslm-calc-label">
                                <?php _e('Loan Amount', 'beauty-salon-services-manager'); ?>
                            </label>
                            <div class="bslm-calc-input-wrapper">
                                <?php if ($atts['currency_position'] === 'before') : ?>
                                    <span class="bslm-currency-symbol"><?php echo esc_html($atts['currency_symbol']); ?></span>
                                <?php endif; ?>
                                <input
                                    type="number"
                                    id="bslm-loan-amount"
                                    class="bslm-calc-input"
                                    value="<?php echo esc_attr($atts['default_amount']); ?>"
                                    min="1000"
                                    max="1000000"
                                    step="1000"
                                />
                                <?php if ($atts['currency_position'] === 'after') : ?>
                                    <span class="bslm-currency-symbol"><?php echo esc_html($atts['currency_symbol']); ?></span>
                                <?php endif; ?>
                            </div>
                            <input
                                type="range"
                                id="bslm-loan-amount-slider"
                                class="bslm-calc-slider"
                                min="1000"
                                max="1000000"
                                step="1000"
                                value="<?php echo esc_attr($atts['default_amount']); ?>"
                            />
                        </div>

                        <div class="bslm-calc-input-group">
                            <label for="bslm-interest-rate" class="bslm-calc-label">
                                <?php _e('Annual Interest Rate (%)', 'beauty-salon-services-manager'); ?>
                            </label>
                            <div class="bslm-calc-input-wrapper">
                                <span class="bslm-currency-symbol">%</span>
                                <input
                                    type="number"
                                    id="bslm-interest-rate"
                                    class="bslm-calc-input"
                                    value="<?php echo esc_attr($atts['default_rate']); ?>"
                                    min="0.1"
                                    max="50"
                                    step="0.01"
                                />
                            </div>
                            <input
                                type="range"
                                id="bslm-interest-rate-slider"
                                class="bslm-calc-slider"
                                min="0.1"
                                max="50"
                                step="0.1"
                                value="<?php echo esc_attr($atts['default_rate']); ?>"
                            />
                            <p class="bslm-calc-hint">
                                <?php _e('Interest rates vary depending on the lender. Use 10% if you\'re unsure.', 'beauty-salon-services-manager'); ?>
                            </p>
                        </div>

                        <div class="bslm-calc-input-group">
                            <label class="bslm-calc-label">
                                <?php _e('Loan Duration', 'beauty-salon-services-manager'); ?>
                            </label>
                            <div class="bslm-calc-duration-buttons">
                                <button type="button" class="bslm-duration-btn" data-months="6">6 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                                <button type="button" class="bslm-duration-btn" data-months="12">12 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                                <button type="button" class="bslm-duration-btn active" data-months="24">24 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                                <button type="button" class="bslm-duration-btn" data-months="36">36 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                                <button type="button" class="bslm-duration-btn" data-months="48">48 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                                <button type="button" class="bslm-duration-btn" data-months="60">60 <?php _e('months', 'beauty-salon-services-manager'); ?></button>
                            </div>
                            <input type="hidden" id="bslm-loan-duration" value="<?php echo esc_attr($atts['default_duration']); ?>" />
                        </div>

                        <button type="button" id="bslm-calc-calculate" class="bslm-calc-button bslm-calc-button-primary">
                            <?php _e('Calculate', 'beauty-salon-services-manager'); ?>
                        </button>
                    </div>

                    <div class="bslm-calc-results">
                        <div class="bslm-calc-results-header">
                            <h3 class="bslm-calc-results-title">
                                <?php _e('Your Estimate', 'beauty-salon-services-manager'); ?>
                            </h3>
                        </div>

                        <div class="bslm-calc-results-content">
                            <div class="bslm-calc-result-item bslm-calc-result-highlight">
                                <span class="bslm-result-label">
                                    <?php _e('Monthly Payment', 'beauty-salon-services-manager'); ?>
                                </span>
                                <span class="bslm-result-value" id="bslm-monthly-payment">
                                    <?php echo esc_html($atts['currency_symbol']); ?>0
                                </span>
                            </div>

                            <?php if ($atts['show_daily_payment'] === 'yes') : ?>
                            <div class="bslm-calc-result-item">
                                <span class="bslm-result-label">
                                    <?php _e('Daily Payment', 'beauty-salon-services-manager'); ?>
                                </span>
                                <span class="bslm-result-value" id="bslm-daily-payment">
                                    <?php echo esc_html($atts['currency_symbol']); ?>0
                                </span>
                            </div>
                            <?php endif; ?>

                            <div class="bslm-calc-result-item">
                                <span class="bslm-result-label">
                                    <?php _e('Total Interest', 'beauty-salon-services-manager'); ?>
                                </span>
                                <span class="bslm-result-value" id="bslm-total-interest">
                                    <?php echo esc_html($atts['currency_symbol']); ?>0
                                </span>
                            </div>

                            <div class="bslm-calc-result-item">
                                <span class="bslm-result-label">
                                    <?php _e('Loan Duration', 'beauty-salon-services-manager'); ?>
                                </span>
                                <span class="bslm-result-value" id="bslm-loan-duration-display">
                                    24 <?php _e('months', 'beauty-salon-services-manager'); ?>
                                </span>
                            </div>

                            <div class="bslm-calc-result-item bslm-calc-result-total">
                                <span class="bslm-result-label">
                                    <?php _e('Total Repayment', 'beauty-salon-services-manager'); ?>
                                </span>
                                <span class="bslm-result-value" id="bslm-total-repayment">
                                    <?php echo esc_html($atts['currency_symbol']); ?>0
                                </span>
                            </div>

                            <?php if ($atts['show_eligibility_check'] === 'yes') : ?>
                            <div class="bslm-calc-actions">
                                <button type="button" class="bslm-calc-button bslm-calc-button-secondary">
                                    <?php _e('Check Eligibility', 'beauty-salon-services-manager'); ?>
                                </button>
                            </div>
                            <?php endif; ?>

                            <div class="bslm-calc-disclaimer">
                                <p>
                                    <?php _e('This calculator is for illustrative purposes only. The figures calculated are not a statement of the actual repayments that will be charged on any actual loan and do not constitute a loan offer.', 'beauty-salon-services-manager'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
