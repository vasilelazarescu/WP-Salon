<?php
/**
 * Bulk Price Editor View Template - Modern UI/UX
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
                <label for="bslm-tag-filter">
                    <span class="dashicons dashicons-tag"></span>
                    <?php esc_html_e('Tag:', 'beauty-salon-services-manager'); ?>
                </label>
                <select id="bslm-tag-filter" name="tag" class="bslm-tag-select">
                    <option value="0"><?php esc_html_e('All Tags', 'beauty-salon-services-manager'); ?></option>
                    <?php foreach ($tags as $tag) : ?>
                        <option value="<?php echo esc_attr($tag->term_id); ?>" <?php selected($tag_filter, $tag->term_id); ?>>
                            <?php echo esc_html($tag->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="bslm-filter-group">
                <label for="bslm-parent-filter">
                    <span class="dashicons dashicons-networking"></span>
                    <?php esc_html_e('Hierarchy:', 'beauty-salon-services-manager'); ?>
                </label>
                <select id="bslm-parent-filter" name="parent" class="bslm-parent-select">
                    <option value=""><?php esc_html_e('All Services', 'beauty-salon-services-manager'); ?></option>
                    <option value="parent_only" <?php selected($parent_filter, 'parent_only'); ?>><?php esc_html_e('Parent Services Only', 'beauty-salon-services-manager'); ?></option>
                    <option value="children_only" <?php selected($parent_filter, 'children_only'); ?>><?php esc_html_e('Child Services Only', 'beauty-salon-services-manager'); ?></option>
                    <?php if (!empty($parent_services)) : ?>
                        <optgroup label="<?php esc_attr_e('Children of:', 'beauty-salon-services-manager'); ?>">
                            <?php foreach ($parent_services as $parent_service) : ?>
                                <option value="<?php echo esc_attr($parent_service->ID); ?>" <?php selected($parent_filter, $parent_service->ID); ?>>
                                    <?php echo esc_html($parent_service->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
            </div>

            <div class="bslm-filter-group">
                <label for="bslm-min-price">
                    <span class="dashicons dashicons-money-alt"></span>
                    <?php esc_html_e('Price Range:', 'beauty-salon-services-manager'); ?>
                </label>
                <div class="bslm-price-range">
                    <input
                        type="number"
                        id="bslm-min-price"
                        name="min_price"
                        value="<?php echo esc_attr($min_price); ?>"
                        placeholder="<?php esc_attr_e('Min', 'beauty-salon-services-manager'); ?>"
                        step="0.01"
                        min="0"
                        class="small-text"
                    >
                    <span class="bslm-range-separator">—</span>
                    <input
                        type="number"
                        id="bslm-max-price"
                        name="max_price"
                        value="<?php echo esc_attr($max_price); ?>"
                        placeholder="<?php esc_attr_e('Max', 'beauty-salon-services-manager'); ?>"
                        step="0.01"
                        min="0"
                        class="small-text"
                    >
                </div>
            </div>

            <button type="submit" class="button button-secondary">
                <span class="dashicons dashicons-filter" style="margin-top: 3px;"></span>
                <?php esc_html_e('Apply Filters', 'beauty-salon-services-manager'); ?>
            </button>

            <?php if (!empty($search) || $tag_filter > 0 || !empty($parent_filter) || $min_price > 0 || $max_price > 0) : ?>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=bslm_service&page=bslm-bulk-prices')); ?>" class="button button-secondary">
                    <span class="dashicons dashicons-dismiss" style="margin-top: 3px;"></span>
                    <?php esc_html_e('Clear Filters', 'beauty-salon-services-manager'); ?>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bulk Operations Section -->
    <div class="bslm-bulk-operations">
        <div class="bslm-bulk-header">
            <div>
                <h2>
                    <span class="dashicons dashicons-admin-tools"></span>
                    <?php esc_html_e('Bulk Operations', 'beauty-salon-services-manager'); ?>
                </h2>
                <p class="description">
                    <?php esc_html_e('Select services below and apply changes to multiple items at once.', 'beauty-salon-services-manager'); ?>
                </p>
            </div>
            <button type="button" class="button button-small bslm-toggle-bulk-operations">
                <span class="dashicons dashicons-arrow-down-alt2"></span>
                <?php esc_html_e('Toggle', 'beauty-salon-services-manager'); ?>
            </button>
        </div>

        <div class="bslm-bulk-actions" style="display: none;">
            <!-- Percentage Operation -->
            <div class="bslm-bulk-action-group">
                <div class="bslm-bulk-action-header">
                    <span class="dashicons dashicons-chart-line" style="color: var(--bslm-primary);"></span>
                    <label for="bulk-percentage">
                        <?php esc_html_e('Apply Percentage Change', 'beauty-salon-services-manager'); ?>
                    </label>
                </div>
                <div class="bslm-bulk-input-group">
                    <input
                        type="number"
                        id="bulk-percentage"
                        step="0.1"
                        placeholder="10"
                        class="small-text"
                    >
                    <span class="bslm-unit">%</span>
                    <button type="button" id="apply-percentage" class="button button-primary">
                        <span class="dashicons dashicons-yes-alt" style="margin-top: 4px;"></span>
                        <?php esc_html_e('Apply to Selected', 'beauty-salon-services-manager'); ?>
                    </button>
                </div>
                <p class="description">
                    <?php esc_html_e('Enter positive number to increase (e.g., 10 for +10%) or negative to decrease (e.g., -5 for -5%)', 'beauty-salon-services-manager'); ?>
                </p>
            </div>

            <!-- Fixed Amount Operation -->
            <div class="bslm-bulk-action-group">
                <div class="bslm-bulk-action-header">
                    <span class="dashicons dashicons-money-alt" style="color: var(--bslm-primary);"></span>
                    <label for="bulk-fixed">
                        <?php esc_html_e('Apply Fixed Amount', 'beauty-salon-services-manager'); ?>
                    </label>
                </div>
                <div class="bslm-bulk-input-group">
                    <input
                        type="number"
                        id="bulk-fixed"
                        step="0.1"
                        placeholder="5"
                        class="small-text"
                    >
                    <span class="bslm-unit">€</span>
                    <button type="button" id="apply-fixed" class="button button-primary">
                        <span class="dashicons dashicons-yes-alt" style="margin-top: 4px;"></span>
                        <?php esc_html_e('Apply to Selected', 'beauty-salon-services-manager'); ?>
                    </button>
                </div>
                <p class="description">
                    <?php esc_html_e('Enter positive number to add (e.g., 5 for +5€) or negative to subtract (e.g., -2 for -2€)', 'beauty-salon-services-manager'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Column Visibility Controls -->
    <?php if (!empty($services)) : ?>
        <div class="bslm-column-controls">
            <div class="bslm-column-controls-header">
                <h3>
                    <span class="dashicons dashicons-visibility"></span>
                    <?php esc_html_e('Column Visibility', 'beauty-salon-services-manager'); ?>
                </h3>
                <button type="button" class="button button-small bslm-toggle-column-controls">
                    <span class="dashicons dashicons-arrow-down-alt2"></span>
                    <?php esc_html_e('Toggle', 'beauty-salon-services-manager'); ?>
                </button>
            </div>
            <div class="bslm-column-checkboxes" style="display: none;">
                <label>
                    <input type="checkbox" class="bslm-column-toggle" data-column="service-name" checked disabled>
                    <?php esc_html_e('Service Name', 'beauty-salon-services-manager'); ?>
                    <small>(<?php esc_html_e('required', 'beauty-salon-services-manager'); ?>)</small>
                </label>
                <label>
                    <input type="checkbox" class="bslm-column-toggle" data-column="tags" <?php checked(in_array('tags', $visible_columns)); ?>>
                    <?php esc_html_e('Tags', 'beauty-salon-services-manager'); ?>
                </label>
                <label>
                    <input type="checkbox" class="bslm-column-toggle" data-column="parent" <?php checked(in_array('parent', $visible_columns)); ?>>
                    <?php esc_html_e('Parent Service', 'beauty-salon-services-manager'); ?>
                </label>
                <label>
                    <input type="checkbox" class="bslm-column-toggle" data-column="base-price" <?php checked(in_array('base_price', $visible_columns)); ?>>
                    <?php esc_html_e('Base Price', 'beauty-salon-services-manager'); ?>
                </label>
                <?php foreach ($tier_columns as $tier) : ?>
                    <label>
                        <input type="checkbox" class="bslm-column-toggle" data-column="tier-<?php echo esc_attr($tier); ?>" <?php checked(in_array('tier_' . $tier, $visible_columns)); ?>>
                        <?php
                        /* translators: %d: number of sessions */
                        printf(esc_html__('%d Session(s)', 'beauty-salon-services-manager'), $tier);
                        ?>
                    </label>
                <?php endforeach; ?>
                <label>
                    <input type="checkbox" class="bslm-column-toggle" data-column="actions" checked disabled>
                    <?php esc_html_e('Actions', 'beauty-salon-services-manager'); ?>
                    <small>(<?php esc_html_e('required', 'beauty-salon-services-manager'); ?>)</small>
                </label>
            </div>
        </div>
    <?php endif; ?>

    <!-- Services Table -->
    <div class="bslm-services-table-wrapper">
        <?php if (empty($services)) : ?>
            <div class="bslm-no-services">
                <span class="dashicons dashicons-info" style="font-size: 48px; color: var(--bslm-gray-400); margin-bottom: 16px;"></span>
                <p><?php esc_html_e('No services found matching your criteria.', 'beauty-salon-services-manager'); ?></p>
                <?php if (!empty($search) || $category_filter > 0) : ?>
                    <p>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=bslm_service&page=bslm-bulk-prices')); ?>" class="button button-primary" style="margin-top: 12px;">
                            <?php esc_html_e('Show All Services', 'beauty-salon-services-manager'); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped bslm-services-table">
                <thead>
                    <tr>
                        <td class="check-column">
                            <input type="checkbox" id="select-all-services" title="<?php esc_attr_e('Select all services', 'beauty-salon-services-manager'); ?>">
                        </td>
                        <th class="column-service-name">
                            <?php esc_html_e('Service Name', 'beauty-salon-services-manager'); ?>
                        </th>
                        <?php if (in_array('tags', $visible_columns)) : ?>
                            <th class="column-tags">
                                <?php esc_html_e('Tags', 'beauty-salon-services-manager'); ?>
                            </th>
                        <?php endif; ?>
                        <?php if (in_array('parent', $visible_columns)) : ?>
                            <th class="column-parent">
                                <?php esc_html_e('Parent', 'beauty-salon-services-manager'); ?>
                            </th>
                        <?php endif; ?>
                        <?php if (in_array('base_price', $visible_columns)) : ?>
                            <th class="column-base-price">
                                <?php esc_html_e('Base Price', 'beauty-salon-services-manager'); ?>
                            </th>
                        <?php endif; ?>
                        <?php foreach ($tier_columns as $tier) : ?>
                            <?php if (in_array('tier_' . $tier, $visible_columns)) : ?>
                                <th class="column-tier-<?php echo esc_attr($tier); ?>">
                                    <?php
                                    /* translators: %d: number of sessions */
                                    printf(esc_html__('%d Session(s)', 'beauty-salon-services-manager'), $tier);
                                    ?>
                                </th>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <th class="column-actions">
                            <?php esc_html_e('Actions', 'beauty-salon-services-manager'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service) : ?>
                        <?php
                        // Calculate colspan for price details row
                        $colspan = 2; // checkbox + service name (always visible)
                        if (in_array('tags', $visible_columns)) $colspan++;
                        if (in_array('parent', $visible_columns)) $colspan++;
                        if (in_array('base_price', $visible_columns)) $colspan++;
                        foreach ($tier_columns as $tier) {
                            if (in_array('tier_' . $tier, $visible_columns)) $colspan++;
                        }
                        $colspan++; // actions column (always visible)
                        ?>
                        <tr class="bslm-service-row" data-service-id="<?php echo esc_attr($service['id']); ?>">
                            <th class="check-column">
                                <input
                                    type="checkbox"
                                    class="bslm-service-checkbox"
                                    value="<?php echo esc_attr($service['id']); ?>"
                                    title="<?php echo esc_attr(sprintf(__('Select %s', 'beauty-salon-services-manager'), $service['title'])); ?>"
                                >
                            </th>
                            <td class="column-service-name">
                                <strong>
                                    <a href="<?php echo esc_url(get_edit_post_link($service['id'])); ?>">
                                        <?php echo esc_html($service['title']); ?>
                                    </a>
                                </strong>
                            </td>
                            <?php if (in_array('tags', $visible_columns)) : ?>
                                <td class="column-tags">
                                    <?php
                                    if (!empty($service['tags'])) {
                                        $tag_names = array_map(function($tag) {
                                            return $tag->name;
                                        }, $service['tags']);
                                        echo esc_html(implode(', ', $tag_names));
                                    } else {
                                        echo '<span style="color: var(--bslm-gray-400);">—</span>';
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if (in_array('parent', $visible_columns)) : ?>
                                <td class="column-parent">
                                    <?php
                                    if (!empty($service['parent_title'])) {
                                        echo esc_html($service['parent_title']);
                                    } else {
                                        echo '<span style="color: var(--bslm-gray-400);">—</span>';
                                    }
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if (in_array('base_price', $visible_columns)) : ?>
                                <td class="column-base-price">
                                    <?php echo esc_html(BSLM_Bulk_Price_Editor::get_base_price($service['price_options'])); ?>
                                </td>
                            <?php endif; ?>
                            <?php foreach ($tier_columns as $tier) : ?>
                                <?php if (in_array('tier_' . $tier, $visible_columns)) : ?>
                                    <td class="column-tier-<?php echo esc_attr($tier); ?>">
                                        <?php
                                        $tier_price = BSLM_Bulk_Price_Editor::get_price_for_tier($service['price_options'], $tier);
                                        if (!empty($tier_price)) {
                                            echo esc_html($tier_price);
                                        } else {
                                            echo '<span style="color: var(--bslm-gray-400);">—</span>';
                                        }
                                        ?>
                                    </td>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <td class="column-actions">
                                <button type="button" class="button button-small bslm-toggle-edit">
                                    <span class="dashicons dashicons-edit" style="margin-top: 3px;"></span>
                                    <?php esc_html_e('Edit Prices', 'beauty-salon-services-manager'); ?>
                                </button>
                            </td>
                        </tr>
                        <tr class="bslm-price-details" style="display: none;">
                            <td colspan="<?php echo esc_attr($colspan); ?>">
                                <div class="bslm-price-editor">
                                    <h3>
                                        <span class="dashicons dashicons-tag" style="color: var(--bslm-primary); margin-left: -4px;"></span>
                                        <?php esc_html_e('Price Tiers', 'beauty-salon-services-manager'); ?>
                                    </h3>

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
                                                    placeholder="1"
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

                                                <button type="button" class="button button-small bslm-remove-price-tier" title="<?php esc_attr_e('Remove this price tier', 'beauty-salon-services-manager'); ?>">
                                                    <span class="dashicons dashicons-trash" style="margin-top: 3px;"></span>
                                                    <?php esc_html_e('Remove', 'beauty-salon-services-manager'); ?>
                                                </button>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="bslm-price-actions">
                                        <button type="button" class="button bslm-add-price-tier">
                                            <span class="dashicons dashicons-plus-alt"></span>
                                            <?php esc_html_e('Add Price Tier', 'beauty-salon-services-manager'); ?>
                                        </button>

                                        <div class="bslm-save-actions">
                                            <button type="button" class="button bslm-save-prices">
                                                <span class="dashicons dashicons-saved" style="margin-top: 4px;"></span>
                                                <?php esc_html_e('Save Prices', 'beauty-salon-services-manager'); ?>
                                            </button>
                                            <button type="button" class="button bslm-cancel-edit">
                                                <span class="dashicons dashicons-no-alt" style="margin-top: 4px;"></span>
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
                    <span class="dashicons dashicons-admin-post" style="margin-top: -2px;"></span>
                    <?php
                    /* translators: %d: number of services */
                    printf(esc_html__('Total services: %d', 'beauty-salon-services-manager'), count($services));
                    ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
