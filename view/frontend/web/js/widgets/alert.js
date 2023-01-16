/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-base',
], function ($) {
    'use strict';

    /**
     * Alert widget
     */
    $.widget('goomento.alert', $.goomento.base, {
        options: {
            selectors: {
                dismissButton: '.gmt-alert-dismiss',
            },
        },
        $dismissButton: $,
        _initWidget: function () {
            this.$dismissButton.on( 'click', () => {
                this.$element.fadeOut();
            } );
        }
    });

    return $.goomento.alert;
});
