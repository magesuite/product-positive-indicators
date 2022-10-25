/**
 * Handle popular badge showing/hiding
 * Check if the current location is category page
 * For all pages that are not category page, always how popular badge (f.e. wishlist, carousels, autocomplete)
 * If the current page is category page first check if a badge is part of a product that is on products list (one of
 * its parents has product-items class)
 * Then for every popular badge check in which categories this product is popular(data-visible-in-categories)
 * Show popular badge only if there is a match between current category and category in which given product is popular
 * For all other components with tile on category page, show popular badge without any check.
 */

define([
    'jquery',
], function ($) {
    'use strict';

    return function (config) {
        if (config.isCurrentPageCategory) {
            document.querySelectorAll('.cs-product-tile__badge--popular').forEach((badge) => {

                function hasSomeParentTheClass(element, classname) {
                    if (element.className.split(' ').indexOf(classname) >= 0) return true;
                    return element.parentNode && hasSomeParentTheClass(element.parentNode, classname);
                }

                const isOnCategoryList = hasSomeParentTheClass(badge, 'product-items');

                if (isOnCategoryList) {
                    const categories = JSON.parse(badge.getAttribute('data-visible-in-categories'));
                    if (categories.includes(config.currentCategoryId)) {
                        badge.style.display = '';
                    }
                } else {
                    badge.style.display = '';
                }
            });
        } else {
            document.querySelectorAll('.cs-product-tile__badge--popular').forEach((badge) => {
                badge.style.display = '';
            });
        }
    }
});
