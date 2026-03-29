define([
    'jquery',
    'slick'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).slick(config);
    };
});
