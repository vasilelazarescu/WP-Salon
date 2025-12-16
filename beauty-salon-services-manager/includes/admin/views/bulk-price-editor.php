<?php
/**
 * Bulk Price Editor View Template.
 *
 * @package Beauty_Salon_Services_Manager
 * @var array $services Array of service data
 * @var array $categories Array of category terms
 * @var string $search Current search term
 * @var int $category_filter Current category filter
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>

<div class="wrap bslm-bulk-price-editor">
    <h1 class="wp-heading-inline">
        <?php esc_html_e('Bulk Price Editor', 'beauty-salon-services-manager'); ?>
    </h1>

    <hr class="wp-header-end">

    <!-- Filters Section -->
    <div class="bslm-filters-section">
        <form method="get" action="" class="bslm-filters-form">
            <input type="hidden" name="post_type" value="bslm_service">
            <input type="hidden" name="page" value="bslm-bulk-prices">

            <div class="bslm-filter-group">
                <label for="bslm-search">
                    <span class="dashicons dashicons-search"></span>
                    <?php esc_html_e('Search:', 'beauty-salon-services-manager'); ?>
                </label>
                <input
                    type="text"
                    id="bslm-search"
                    name="search"
                    value="<?php echo esc_attr($search); ?>"
                    placeholder="<?php esc_attr_e('Search services...', 'beauty-salon-services-manager'); ?>"
                    class="bslm-search-input"
                >
            </div>

            <div class="bslm-filter-group">
                <label for="bslm-category-filter">
                    <span class="dashicons dashicons-category"></span>
                    <?php esc_html_e('Category:', 'beauty-salon-services-manager'); ?>
                </label>
                <select id="bslm-category-filter" name="category" class="bslm-category-select">
                    <option value="0"><?php esc_html_e('All Categories', 'beauty-salon-services-manager'); ?></option>
                    <?php foreach ($categories as $category) : ?>
                        <option value="<?php echo esc_attr($category->term_id); ?>" <?php selected($category_filter, $category->term_id); ?>>
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="button button-secondary">
                <?php esc_html_e('Apply Filters', 'beauty-salon-services-manager'); ?>
            </button>

            <?php if (!empty($search) || $category_filter > 0) : ?>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=bslm_service&page=bslm-bulk-prices')); ?>" class="button button-secondary">
                    <?php esc_html_e('Clear Filters', 'beauty-salon-services-manager'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bulk Operations Section -->
    <div class="bslm-bulk-operations">
        <div class="bslm-bulk-header">
            <h2><?php esc_html_e('Bulk Operations', 'beauty-salon-services-manager'); ?></h2>
            <p class="description">
                <?php esc_html_e('Select services below and apply changes to multiple items at once.', 'beauty-salon-services-manager'); ?>
            </p>
        </div>

        <div class="bslm-bulk-actions">
            <div class="bslm-bulk-action-group">
                <label for="bulk-percentage">
                    <?php esc_html_e('Apply Percentage:', 'beauty-salon-services-manager'); ?>
                </label>
                <input
                    type="number"
                    id="bulk-percentage"
                    step="0.1"
                    placeholder="10"
                    class="small-text"
                >
                <span class="bslm-unit">%</span>
                <button type="button" id="apply-percentage" class="button button-primary">
                    <?php esc_html_e('Apply to Selected', 'beauty-salon-services-manager'); ?>
                </button>
                <p class="description">
                    <?php esc_html_e('Enter positive number to increase (e.g., 10 for +10%) or negative to decrease (e.g., -5 for -5%)', 'beauty-salon-services-manager'); ?>
                </p>
            </div>

            <div class="bslm-bulk-action-group">
                <label for="bulk-fixed">
                    <?php esc_html_e('Apply Fixed Amount:', 'beauty-salon-services-manager'); ?>
                </label>
                <input
                    type="number"
                    id="bulk-fixed"
                    step="0.1"
                    placeholder="5"
                    class="small-text"
                >
                <span class="bslm-unit">€</span>
                <button type="button" id="apply-fixed" class="button button-primary">
                    <?php esc_html_e('Apply to Selected', 'beauty-salon-services-manager'); ?>
                </button>
                <p class="description">
                    <?php esc_html_e('Enter positive number to add (e.g., 5 for +5€) or negative to subtract (e.g., -2 for -2€)', 'beauty-salon-services-manager'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Services Table -->
    <div class="bslm-services-table-wrapper">
        <?php if (empty($services)) : ?>
            <div class="bslm-no-services">
                <p><?php esc_html_e('No services found.', 'beauty-salon-services-manager'); ?></p>
            </div>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped bslm-services-table">
                <thead>
                    <tr>
                        <td class="check-column">
                            <input type="checkbox" id="select-all-services">
                        </td>
                        <th class="column-service-name">
                            <?php esc_html_e('Service Name', 'beauty-salon-services-manager'); ?>
                        </th>
                        <th class="column-category">
                            <?php esc_html_e('Category', 'beauty-salon-services-manager'); ?>
                        </th>
                        <th class="column-base-price">
                            <?php esc_html_e('Base Price', 'beauty-salon-services-manager'); ?>
                        </th>
                        <th class="column-actions">
                            <?php esc_html_e('Actions', 'beauty-salon-services-manager'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service) : ?>
                        <tr class="bslm-service-row" data-service-id="<?php echo esc_attr($service['id']); ?>">
                            <th class="check-column">
                                <input
                                    type="checkbox"
                                    class="bslm-service-checkbox"
                                    value="<?php echo esc_attr($service['id']); ?>"
                                >
                            </th>
                            <td class="column-service-name">
                                <strong>
                                    <a href="<?php echo esc_url(get_edit_post_link($service['id'])); ?>">
                                        <?php echo esc_html($service['title']); ?>
                                    </a>
                                </strong>
                            </td>
                            <td class="column-category">
                                <?php
                                if (!empty($service['categories'])) {
                                    $cat_names = array_map(function($cat) {
                                        return $cat->name;
                                    }, $service['categories']);
                                    echo esc_html(implode(', ', $cat_names));
                                } else {
                                    echo '—';
                                }
                                ?>
                            </td>
                            <td class="column-base-price">
                                <?php echo esc_html(BSLM_Bulk_Price_Editor::get_base_price($service['price_options'])); ?>
                            </td>
                            <td class="column-actions">
                                <button type="button" class="button button-small bslm-toggle-edit">
                                    <?php esc_html_e('Edit Prices', 'beauty-salon-services-manager'); ?>
                                </button>
                            </td>
                        </tr>
                        <tr class="bslm-price-details" style="display: none;">
                            <td colspan="5">
                                <div class="bslm-price-editor">
                                    <h3><?php esc_html_e('Price Tiers', 'beauty-salon-services-manager'); ?></h3>

                                    <div class="bslm-price-options">
                                        <?php foreach ($service['price_options'] as $index => $option) : ?>
                                            <div class="bslm-price-option-row">
                                                <label>
                                                    <?php esc_html_e('Sessions:', 'beauty-salon-services-manager'); ?>
                                                </label>
                                                <input
                                                    type="number"
                                                    class="bslm-sessions-input"
                                                    value="<?php echo esc_attr($option['sessions']); ?>"
                                                    min="1"
                                                    data-index="<?php echo esc_attr($index); ?>"
                                                >

                                                <label>
                                                    <?php esc_html_e('Price:', 'beauty-salon-services-manager'); ?>
                                                </label>
                                                <input
                                                    type="text"
                                                    class="bslm-price-input"
                                                    value="<?php echo esc_attr($option['price']); ?>"
                                                    placeholder="50€"
                                                    data-index="<?php echo esc_attr($index); ?>"
                                                >

                                                <button type="button" class="button button-secondary button-small bslm-remove-price-tier">
                                                    <?php esc_html_e('Remove', 'beauty-salon-services-manager'); ?>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="bslm-price-actions">
                                        <button type="button" class="button button-secondary bslm-add-price-tier">
                                            <span class="dashicons dashicons-plus-alt"></span>
                                            <?php esc_html_e('Add Price Tier', 'beauty-salon-services-manager'); ?>
                                        </button>

                                        <div class="bslm-save-actions">
                                            <button type="button" class="button button-primary bslm-save-prices">
                                                <?php esc_html_e('Save Prices', 'beauty-salon-services-manager'); ?>
                                            </button>
                                            <button type="button" class="button button-secondary bslm-cancel-edit">
                                                <?php esc_html_e('Cancel', 'beauty-salon-services-manager'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="bslm-table-footer">
                <p class="bslm-services-count">
                    <?php
                    /* translators: %d: number of services */
                    printf(esc_html__('Total services: %d', 'beauty-salon-services-manager'), count($services));
                    ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
