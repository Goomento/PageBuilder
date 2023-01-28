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
     * Accordion widget
     */
    $.widget('goomento.accordion', $.goomento.baseTab, {
        options: {
            showTabFn: 'slideDown',
            hideTabFn: 'slideUp',
        },
    });

    return $.goomento.accordion;
});
