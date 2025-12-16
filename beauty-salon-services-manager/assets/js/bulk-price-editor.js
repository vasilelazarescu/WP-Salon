/**
 * Bulk Price Editor JavaScript
 *
 * @package Beauty_Salon_Services_Manager
 */

(function($) {
    'use strict';

    /**
     * Bulk Price Editor Object
     */
    const BulkPriceEditor = {

        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
        },

        /**
         * Bind event listeners
         */
        bindEvents: function() {
            // Select all checkbox
            $('#select-all-services').on('change', this.handleSelectAll);

            // Toggle edit mode
            $(document).on('click', '.bslm-toggle-edit', this.toggleEditMode);

            // Cancel edit
            $(document).on('click', '.bslm-cancel-edit', this.cancelEdit);

            // Save prices
            $(document).on('click', '.bslm-save-prices', this.savePrices);

            // Add price tier
            $(document).on('click', '.bslm-add-price-tier', this.addPriceTier);

            // Remove price tier
            $(document).on('click', '.bslm-remove-price-tier', this.removePriceTier);

            // Bulk operations
            $('#apply-percentage').on('click', this.applyPercentage);
            $('#apply-fixed').on('click', this.applyFixed);

            // Column visibility controls
            $('.bslm-toggle-column-controls').on('click', BulkPriceEditor.toggleColumnControls);
            $('.bslm-column-toggle').on('change', BulkPriceEditor.handleColumnToggle);
        },

        /**
         * Handle select all checkbox
         */
        handleSelectAll: function() {
            const isChecked = $(this).prop('checked');
            $('.bslm-service-checkbox').prop('checked', isChecked);
        },

        /**
         * Toggle edit mode for a service
         */
        toggleEditMode: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $row = $button.closest('.bslm-service-row');
            const $detailsRow = $row.next('.bslm-price-details');

            // Toggle visibility
            $detailsRow.toggle();

            // Update button text
            if ($detailsRow.is(':visible')) {
                $button.text($button.data('close-text') || 'Close');
            } else {
                $button.text($button.data('open-text') || 'Edit Prices');
            }
        },

        /**
         * Cancel edit mode
         */
        cancelEdit: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $detailsRow = $button.closest('.bslm-price-details');
            const $serviceRow = $detailsRow.prev('.bslm-service-row');

            // Hide details row
            $detailsRow.hide();

            // Reset toggle button text
            $serviceRow.find('.bslm-toggle-edit').text('Edit Prices');
        },

        /**
         * Save prices for a service
         */
        savePrices: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $detailsRow = $button.closest('.bslm-price-details');
            const $serviceRow = $detailsRow.prev('.bslm-service-row');
            const serviceId = $serviceRow.data('service-id');

            // Collect price options
            const priceOptions = [];
            $detailsRow.find('.bslm-price-option-row').each(function() {
                const sessions = $(this).find('.bslm-sessions-input').val();
                const price = $(this).find('.bslm-price-input').val();

                if (sessions && price) {
                    priceOptions.push({
                        sessions: sessions,
                        price: price
                    });
                }
            });

            if (priceOptions.length === 0) {
                alert(bslmBulkEditor.strings.error);
                return;
            }

            // Show loading state
            $button.prop('disabled', true).text(bslmBulkEditor.strings.saving);

            // AJAX save
            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_update_service_prices',
                    nonce: bslmBulkEditor.nonce,
                    service_id: serviceId,
                    price_options: priceOptions
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        $button.text(bslmBulkEditor.strings.saved);

                        // Update base price in table
                        if (priceOptions.length > 0) {
                            $serviceRow.find('.column-base-price').text(priceOptions[0].price);
                        }

                        // Reset button after delay
                        setTimeout(function() {
                            $button.prop('disabled', false).text('Save Prices');
                            $detailsRow.hide();
                            $serviceRow.find('.bslm-toggle-edit').text('Edit Prices');
                        }, 1500);
                    } else {
                        alert(response.data.message || bslmBulkEditor.strings.error);
                        $button.prop('disabled', false).text('Save Prices');
                    }
                },
                error: function() {
                    alert(bslmBulkEditor.strings.error);
                    $button.prop('disabled', false).text('Save Prices');
                }
            });
        },

        /**
         * Add a new price tier
         */
        addPriceTier: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $container = $button.closest('.bslm-price-editor').find('.bslm-price-options');
            const $lastRow = $container.find('.bslm-price-option-row:last');

            // Get the last session number
            const lastSessions = parseInt($lastRow.find('.bslm-sessions-input').val()) || 1;
            const newSessions = lastSessions + 1;

            // Create new row
            const newIndex = $container.find('.bslm-price-option-row').length;
            const $newRow = $('<div class="bslm-price-option-row"></div>');

            $newRow.html(`
                <label>Sessions:</label>
                <input type="number" class="bslm-sessions-input" value="${newSessions}" min="1" data-index="${newIndex}">
                <label>Price:</label>
                <input type="text" class="bslm-price-input" value="" placeholder="50€" data-index="${newIndex}">
                <button type="button" class="button button-secondary button-small bslm-remove-price-tier">Remove</button>
            `);

            $container.append($newRow);
        },

        /**
         * Remove a price tier
         */
        removePriceTier: function(e) {
            e.preventDefault();
            const $button = $(this);
            const $row = $button.closest('.bslm-price-option-row');
            const $container = $row.closest('.bslm-price-options');

            // Don't allow removing the last tier
            if ($container.find('.bslm-price-option-row').length <= 1) {
                alert('You must have at least one price tier.');
                return;
            }

            $row.remove();
        },

        /**
         * Get selected service IDs
         */
        getSelectedServiceIds: function() {
            const ids = [];
            $('.bslm-service-checkbox:checked').each(function() {
                ids.push($(this).val());
            });
            return ids;
        },

        /**
         * Apply percentage to selected services
         */
        applyPercentage: function(e) {
            e.preventDefault();

            const percentage = parseFloat($('#bulk-percentage').val());
            const selectedIds = BulkPriceEditor.getSelectedServiceIds();

            // Validation
            if (!percentage || isNaN(percentage)) {
                alert(bslmBulkEditor.strings.invalidValue);
                return;
            }

            if (selectedIds.length === 0) {
                alert(bslmBulkEditor.strings.noSelection);
                return;
            }

            // Confirmation
            const confirmMessage = bslmBulkEditor.strings.confirmBulk
                .replace('%d', selectedIds.length)
                .replace('%s', percentage + '%');

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading
            const $button = $(this);
            $button.prop('disabled', true).text(bslmBulkEditor.strings.saving);

            // AJAX request
            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_bulk_apply_percentage',
                    nonce: bslmBulkEditor.nonce,
                    service_ids: selectedIds,
                    percentage: percentage
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        BulkPriceEditor.showMessage('success', response.data.message);

                        // Reload page to show updated prices
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert(response.data.message || bslmBulkEditor.strings.error);
                        $button.prop('disabled', false).text('Apply to Selected');
                    }
                },
                error: function() {
                    alert(bslmBulkEditor.strings.error);
                    $button.prop('disabled', false).text('Apply to Selected');
                }
            });
        },

        /**
         * Apply fixed amount to selected services
         */
        applyFixed: function(e) {
            e.preventDefault();

            const fixedAmount = parseFloat($('#bulk-fixed').val());
            const selectedIds = BulkPriceEditor.getSelectedServiceIds();

            // Validation
            if (!fixedAmount || isNaN(fixedAmount)) {
                alert(bslmBulkEditor.strings.invalidValue);
                return;
            }

            if (selectedIds.length === 0) {
                alert(bslmBulkEditor.strings.noSelection);
                return;
            }

            // Confirmation
            const sign = fixedAmount > 0 ? '+' : '';
            const confirmMessage = bslmBulkEditor.strings.confirmBulk
                .replace('%d', selectedIds.length)
                .replace('%s', sign + fixedAmount + '€');

            if (!confirm(confirmMessage)) {
                return;
            }

            // Show loading
            const $button = $(this);
            $button.prop('disabled', true).text(bslmBulkEditor.strings.saving);

            // AJAX request
            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_bulk_apply_fixed',
                    nonce: bslmBulkEditor.nonce,
                    service_ids: selectedIds,
                    fixed_amount: fixedAmount
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        BulkPriceEditor.showMessage('success', response.data.message);

                        // Reload page to show updated prices
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        alert(response.data.message || bslmBulkEditor.strings.error);
                        $button.prop('disabled', false).text('Apply to Selected');
                    }
                },
                error: function() {
                    alert(bslmBulkEditor.strings.error);
                    $button.prop('disabled', false).text('Apply to Selected');
                }
            });
        },

        /**
         * Show success/error message
         */
        showMessage: function(type, message) {
            const iconClass = type === 'success' ? 'dashicons-yes-alt' : 'dashicons-warning';
            const $message = $('<div class="bslm-message ' + type + '"></div>');

            $message.html(`
                <span class="dashicons ${iconClass}"></span>
                <span>${message}</span>
            `);

            $('.bslm-bulk-operations').after($message);

            // Auto-remove after 5 seconds
            setTimeout(function() {
                $message.fadeOut(function() {
                    $message.remove();
                });
            }, 5000);
        },

        /**
         * Toggle column controls visibility
         */
        toggleColumnControls: function(e) {
            e.preventDefault();
            const $checkboxes = $('.bslm-column-checkboxes');
            const $icon = $(this).find('.dashicons');

            $checkboxes.slideToggle(300);

            // Toggle icon
            if ($checkboxes.is(':visible')) {
                $icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
            } else {
                $icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
            }
        },

        /**
         * Handle column visibility toggle
         */
        handleColumnToggle: function() {
            const $checkbox = $(this);
            const column = $checkbox.data('column');
            const isVisible = $checkbox.prop('checked');

            // Toggle column visibility immediately
            if (isVisible) {
                $('.column-' + column).show();
            } else {
                $('.column-' + column).hide();
            }

            // Collect all visible columns
            const visibleColumns = [];
            $('.bslm-column-toggle:checked').each(function() {
                const col = $(this).data('column');
                // Convert data attribute format to user meta format
                if (col.startsWith('tier-')) {
                    visibleColumns.push(col.replace('tier-', 'tier_'));
                } else if (col === 'base-price') {
                    visibleColumns.push('base_price');
                } else {
                    visibleColumns.push(col);
                }
            });

            // Save to user meta via AJAX
            $.ajax({
                url: bslmBulkEditor.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bslm_save_column_visibility',
                    nonce: bslmBulkEditor.nonce,
                    columns: visibleColumns
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Column visibility saved');
                    }
                },
                error: function() {
                    console.error('Failed to save column visibility');
                }
            });
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        BulkPriceEditor.init();
    });

})(jQuery);
