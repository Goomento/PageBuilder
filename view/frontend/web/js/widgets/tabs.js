/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'Goomento_PageBuilder/js/widgets/base-tab'
], function ($) {
    'use strict';

    /**
     * Tabs widget
     */
    $.widget('goomento.tabs', $.goomento.baseTab, {
        options: {
            toggleSelf: false,
        },
    });

    return $.goomento.tabs;
});
