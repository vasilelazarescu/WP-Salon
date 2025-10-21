/**
 * Taxonomy Icon Uploader
 *
 * @package Beauty_Salon_Services_Manager
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        var mediaUploader;

        /**
         * Upload icon button click
         */
        $(document).on('click', '.bslm-upload-icon-button', function(e) {
            e.preventDefault();

            var button = $(this);
            var container = button.closest('.bslm-category-icon-upload');
            var preview = container.find('.bslm-icon-preview img');
            var input = container.find('#bslm_category_icon');
            var removeButton = container.find('.bslm-remove-icon-button');

            // If the uploader object has already been created, reopen the dialog
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            // Extend the wp.media object
            mediaUploader = wp.media({
                title: 'Choose Category Icon',
                button: {
                    text: 'Use this icon'
                },
                library: {
                    type: ['image']
                },
                multiple: false
            });

            // When a file is selected, run a callback
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                // Set the icon
                input.val(attachment.id);
                preview.attr('src', attachment.url).show();
                removeButton.show();
            });

            // Open the uploader dialog
            mediaUploader.open();
        });

        /**
         * Remove icon button click
         */
        $(document).on('click', '.bslm-remove-icon-button', function(e) {
            e.preventDefault();

            var button = $(this);
            var container = button.closest('.bslm-category-icon-upload');
            var preview = container.find('.bslm-icon-preview img');
            var input = container.find('#bslm_category_icon');

            // Clear the icon
            input.val('');
            preview.attr('src', '').hide();
            button.hide();
        });

    });

})(jQuery);
