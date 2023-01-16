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
     * Progress widget
     */
    $.widget('goomento.progress', $.goomento.base, {
        options: {
            selectors: {
                progressNumber: '.gmt-progress-bar',
            },
        },
        $progressNumber: $,
        /**
         * On focus
         * @private
         */
        _onFocus() {
            this.$progressNumber.css( 'width', this.$progressNumber.data( 'max' ) + '%' );
        }
    });

    return $.goomento.progress;
});
