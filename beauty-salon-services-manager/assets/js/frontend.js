/**
 * Frontend JavaScript for Beauty Salon Services Manager
 *
 * @package Beauty_Salon_Services_Manager
 */

(function($) {
    'use strict';

    /**
     * Initialize frontend functionality
     */
    $(document).ready(function() {

        /**
         * Lazy load images
         */
        function initLazyLoad() {
            if ('IntersectionObserver' in window) {
                var imageObserver = new IntersectionObserver(function(entries, observer) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                $('.bslm-service-image img.lazy').each(function() {
                    imageObserver.observe(this);
                });
            } else {
                // Fallback for browsers that don't support IntersectionObserver
                $('.bslm-service-image img.lazy').each(function() {
                    $(this).attr('src', $(this).data('src')).removeClass('lazy');
                });
            }
        }

        /**
         * Animate cards on scroll
         */
        function initScrollAnimation() {
            if ('IntersectionObserver' in window) {
                var cardObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('bslm-animated');
                            cardObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1
                });

                $('.bslm-service-card').each(function() {
                    cardObserver.observe(this);
                });
            }
        }

        /**
         * Filter services by group (if filter UI exists)
         */
        function initServiceFilter() {
            $('.bslm-service-filter').on('change', function() {
                var selectedGroup = $(this).val();
                var grid = $('.bslm-services-grid');

                if (selectedGroup === 'all') {
                    $('.bslm-service-card').fadeIn();
                } else {
                    $('.bslm-service-card').hide();
                    $('.bslm-service-card[data-group="' + selectedGroup + '"]').fadeIn();
                }
            });
        }

        /**
         * Read more / Read less functionality for descriptions
         */
        function initReadMore() {
            $('.bslm-service-description').each(function() {
                var description = $(this);
                var fullText = description.data('full-text');
                var shortText = description.data('short-text');

                if (fullText && shortText && fullText.length > shortText.length) {
                    description.html(shortText);

                    var readMoreBtn = $('<a href="#" class="bslm-read-more">Read More</a>');
                    description.append(readMoreBtn);

                    readMoreBtn.on('click', function(e) {
                        e.preventDefault();

                        if (description.hasClass('expanded')) {
                            description.html(shortText);
                            description.append(readMoreBtn);
                            description.removeClass('expanded');
                            $(this).text('Read More');
                        } else {
                            description.html(fullText + ' ');
                            var readLessBtn = $('<a href="#" class="bslm-read-more">Read Less</a>');
                            description.append(readLessBtn);
                            description.addClass('expanded');

                            readLessBtn.on('click', function(e) {
                                e.preventDefault();
                                description.html(shortText);
                                description.append(readMoreBtn);
                                description.removeClass('expanded');
                            });
                        }
                    });
                }
            });
        }

        /**
         * Add smooth scroll to service details
         */
        function initSmoothScroll() {
            $('.bslm-service-button[href^="#"]').on('click', function(e) {
                e.preventDefault();

                var target = $(this).attr('href');
                if ($(target).length) {
                    $('html, body').animate({
                        scrollTop: $(target).offset().top - 100
                    }, 500);
                }
            });
        }

        /**
         * Add tooltip for truncated text
         */
        function initTooltips() {
            $('.bslm-service-title, .bslm-service-description').each(function() {
                var element = $(this);

                if (this.offsetWidth < this.scrollWidth) {
                    element.attr('title', element.text());
                }
            });
        }

        /**
         * Price formatting
         */
        function formatPrices() {
            $('.bslm-service-price').each(function() {
                var price = $(this).text().trim();

                // Add custom formatting if needed
                // This is a placeholder for custom price formatting logic
            });
        }

        /**
         * Add loading state when navigating
         */
        function initLoadingState() {
            $('.bslm-service-button').on('click', function() {
                var button = $(this);

                if (!button.attr('href').startsWith('#')) {
                    button.addClass('loading').text('Loading...');
                }
            });
        }

        /**
         * Equalize card heights in a row
         */
        function equalizeCardHeights() {
            if ($(window).width() > 768) {
                var cards = $('.bslm-service-card');
                var maxHeight = 0;

                cards.css('height', 'auto');

                cards.each(function() {
                    var cardHeight = $(this).outerHeight();
                    if (cardHeight > maxHeight) {
                        maxHeight = cardHeight;
                    }
                });

                cards.css('height', maxHeight + 'px');
            } else {
                $('.bslm-service-card').css('height', 'auto');
            }
        }

        /**
         * Add favorites functionality (localStorage)
         */
        function initFavorites() {
            $('.bslm-favorite-button').on('click', function(e) {
                e.preventDefault();

                var serviceId = $(this).data('service-id');
                var favorites = JSON.parse(localStorage.getItem('bslm_favorites') || '[]');

                if (favorites.includes(serviceId)) {
                    favorites = favorites.filter(function(id) { return id !== serviceId; });
                    $(this).removeClass('active').text('Add to Favorites');
                } else {
                    favorites.push(serviceId);
                    $(this).addClass('active').text('Remove from Favorites');
                }

                localStorage.setItem('bslm_favorites', JSON.stringify(favorites));
            });

            // Initialize favorites state
            var favorites = JSON.parse(localStorage.getItem('bslm_favorites') || '[]');
            $('.bslm-favorite-button').each(function() {
                var serviceId = $(this).data('service-id');
                if (favorites.includes(serviceId)) {
                    $(this).addClass('active').text('Remove from Favorites');
                }
            });
        }

        /**
         * Mobile menu toggle for filters
         */
        function initMobileFilterToggle() {
            $('.bslm-filter-toggle').on('click', function(e) {
                e.preventDefault();
                $('.bslm-service-filters').toggleClass('active');
            });
        }

        /**
         * Track service card impressions (analytics)
         */
        function trackImpressions() {
            if ('IntersectionObserver' in window) {
                var impressionObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            var serviceId = entry.target.dataset.serviceId;

                            // Send analytics event (if analytics is set up)
                            if (typeof gtag !== 'undefined') {
                                gtag('event', 'service_view', {
                                    'service_id': serviceId
                                });
                            }

                            impressionObserver.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.5
                });

                $('.bslm-service-card').each(function() {
                    impressionObserver.observe(this);
                });
            }
        }

        // Initialize all functions
        initLazyLoad();
        initScrollAnimation();
        initServiceFilter();
        initReadMore();
        initSmoothScroll();
        initTooltips();
        formatPrices();
        initLoadingState();
        initFavorites();
        initMobileFilterToggle();
        trackImpressions();

        // Equalize heights on load and resize
        equalizeCardHeights();
        $(window).on('resize', function() {
            clearTimeout(window.resizeTimeout);
            window.resizeTimeout = setTimeout(equalizeCardHeights, 250);
        });

        /**
         * Add accessibility features
         */
        $('.bslm-service-card').on('keypress', function(e) {
            if (e.which === 13 || e.which === 32) {
                $(this).find('.bslm-service-button').click();
            }
        });

        /**
         * Custom event for when services are loaded
         */
        $(document).trigger('bslm_services_loaded');

    });

    /**
     * Make functions available globally if needed
     */
    window.BSLM = window.BSLM || {};
    window.BSLM.reloadServices = function() {
        // Placeholder for AJAX reload functionality
        console.log('Reloading services...');
    };

})(jQuery);
