/**
 * Handle popular badge showing/hiding
 * Check if the current location is category page
 * Then for every popular badge check in which categories this product is popular(data-visible-in-categories)
 * Show popular badge only if there is a match between current category and category in which given product is popular
 */

define([
    'jquery',
], function ($) {
    'use strict';

    return function (config) {
        if (config.isCurrentPageCategory) {
            document.querySelectorAll('.cs-product-tile__badge--popular').forEach((badge) => {
                const categories = JSON.parse(badge.getAttribute('data-visible-in-categories'));
                if (categories.includes(config.currentCategoryId)) {
                    badge.style.display = '';
                }
            });
        }
    }
});
