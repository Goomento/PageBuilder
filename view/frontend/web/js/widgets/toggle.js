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
     * Toggle widget
     */
    $.widget('goomento.toggle', $.goomento.baseTab, {
        options: {
            showTabFn: 'slideDown',
            hideTabFn: 'slideUp',
            hidePrevious: false,
            autoExpand: 'editor',
        },
    });

    return $.goomento.toggle;
});
