define([
    'jquery',
], function ($) {
    'use strict';

    return function (config) {
        if (config.isCurrentPageCategory) {
            $('.cs-product-tile__badge--popular').show();
        }
    };
});
