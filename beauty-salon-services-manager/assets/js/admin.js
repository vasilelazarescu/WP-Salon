/**
 * Admin JavaScript for Beauty Salon Services Manager
 *
 * @package Beauty_Salon_Services_Manager
 */

(function($) {
    'use strict';

    /**
     * Initialize admin functionality
     */
    $(document).ready(function() {

        /**
         * Meta box validation
         */
        function validateMetaBox() {
            var priceField = $('#bslm_service_price');
            var timeField = $('#bslm_service_time');

            // Price validation (optional - can be text to allow currency symbols)
            priceField.on('blur', function() {
                var value = $(this).val();
                if (value && !value.match(/^[\d\s\.,\$\€\£\¥]+$/)) {
                    $(this).css('border-color', '#dc3232');
                    if (!$(this).next('.bslm-error').length) {
                        $(this).after('<span class="bslm-error" style="color: #dc3232; font-size: 12px; display: block; margin-top: 5px;">Please enter a valid price format</span>');
                    }
                } else {
                    $(this).css('border-color', '');
                    $(this).next('.bslm-error').remove();
                }
            });
        }

        /**
         * Quick edit functionality
         */
        function initQuickEdit() {
            var wp_inline_edit = inlineEditPost.edit;

            inlineEditPost.edit = function(id) {
                wp_inline_edit.apply(this, arguments);

                var postId = 0;
                if (typeof(id) == 'object') {
                    postId = parseInt(this.getId(id));
                }

                if (postId > 0) {
                    var editRow = $('#edit-' + postId);
                    var postRow = $('#post-' + postId);

                    // Get meta values
                    var time = $('.column-time', postRow).text();
                    var price = $('.column-price', postRow).text();

                    // Set meta values in quick edit
                    $(':input[name="bslm_service_time"]', editRow).val(time === '—' ? '' : time);
                    $(':input[name="bslm_service_price"]', editRow).val(price === '—' ? '' : price);
                }
            };
        }

        /**
         * Add help tooltips
         */
        function addTooltips() {
            $('.bslm-help-icon').hover(
                function() {
                    var tooltip = $(this).data('tooltip');
                    $(this).append('<div class="bslm-tooltip">' + tooltip + '</div>');
                },
                function() {
                    $('.bslm-tooltip').remove();
                }
            );
        }

        /**
         * Confirm delete for services
         */
        function confirmDelete() {
            $('.submitdelete').on('click', function(e) {
                if (!confirm('Are you sure you want to delete this service?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        /**
         * Add custom column sorting
         */
        function initColumnSorting() {
            // Make custom columns sortable if needed
            $('.column-time, .column-price').addClass('sortable');
        }

        /**
         * Character counter for textarea
         */
        function initCharacterCounter() {
            $('#bslm_service_notes').on('input', function() {
                var length = $(this).val().length;
                var counter = $(this).siblings('.character-counter');

                if (!counter.length) {
                    $(this).after('<span class="character-counter" style="font-size: 12px; color: #666;"></span>');
                    counter = $(this).siblings('.character-counter');
                }

                counter.text(length + ' characters');
            });
        }

        /**
         * Auto-save meta box data
         */
        function initAutoSave() {
            var metaBoxFields = $('#bslm_service_time, #bslm_service_price, #bslm_service_notes');
            var saveTimeout;

            metaBoxFields.on('input', function() {
                clearTimeout(saveTimeout);

                // Show saving indicator
                if (!$('.bslm-saving-indicator').length) {
                    $('.bslm-meta-box').prepend('<div class="bslm-saving-indicator" style="padding: 10px; background: #d5e5f2; border-left: 3px solid #0073aa; margin-bottom: 15px;">Changes will be saved when you update the post</div>');
                }

                saveTimeout = setTimeout(function() {
                    $('.bslm-saving-indicator').fadeOut(function() {
                        $(this).remove();
                    });
                }, 2000);
            });
        }

        /**
         * Media uploader for custom fields (if needed in future)
         */
        function initMediaUploader() {
            $('.bslm-upload-image-button').on('click', function(e) {
                e.preventDefault();

                var button = $(this);
                var imageId = button.prev();
                var imagePreview = button.siblings('.bslm-image-preview');

                var customUploader = wp.media({
                    title: 'Select Image',
                    button: {
                        text: 'Use this image'
                    },
                    multiple: false
                }).on('select', function() {
                    var attachment = customUploader.state().get('selection').first().toJSON();
                    imageId.val(attachment.id);
                    imagePreview.html('<img src="' + attachment.url + '" style="max-width: 200px; height: auto;" />');
                }).open();
            });
        }

        // Initialize all functions
        validateMetaBox();
        if (typeof inlineEditPost !== 'undefined') {
            initQuickEdit();
        }
        addTooltips();
        confirmDelete();
        initColumnSorting();
        initCharacterCounter();
        initAutoSave();
        initMediaUploader();

        /**
         * Show/hide fields based on conditions
         */
        $('.bslm-conditional-field').each(function() {
            var field = $(this);
            var condition = field.data('condition');
            var conditionValue = field.data('condition-value');
            var conditionField = $('#' + condition);

            function checkCondition() {
                if (conditionField.val() === conditionValue) {
                    field.show();
                } else {
                    field.hide();
                }
            }

            conditionField.on('change', checkCondition);
            checkCondition();
        });

        /**
         * Bulk actions confirmation
         */
        $('#doaction, #doaction2').on('click', function(e) {
            var action = $(this).siblings('select').val();

            if (action === 'trash' || action === 'delete') {
                var checkedBoxes = $('input[name="post[]"]:checked').length;
                if (checkedBoxes > 0) {
                    if (!confirm('Are you sure you want to ' + action + ' ' + checkedBoxes + ' service(s)?')) {
                        e.preventDefault();
                        return false;
                    }
                }
            }
        });

        /**
         * Add star rating preview (future feature)
         */
        $('.bslm-star-rating').on('click', '.star', function() {
            var rating = $(this).data('rating');
            var container = $(this).parent();

            container.find('.star').each(function(index) {
                if (index < rating) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active');
                }
            });

            container.siblings('input').val(rating);
        });

    });

})(jQuery);
