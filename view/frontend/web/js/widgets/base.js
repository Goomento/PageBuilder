/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'jquery/ui',
    'goomento-frontend'
], function (_, $) {
    'use strict';

    $.widget('goomento.base',{
        options: {
            selectors: {},
        },
        /**
         * On live edit mode
         */
        isEdit: false,
        /**
         * @private
         */
        _create: function() {
            this.$element = $(this.element);
            this.isEdit = this.$element.hasClass( 'gmt-element-edit-mode' );
            if (!_.isEmpty(this.options.selectors)) {
                _.each(this.options.selectors, (selector, key) => {
                    this[`$${key}`] = this.$element.find(selector);
                });
            }
            this._init();
        },
        /**
         * On initializing widget
         * @private
         */
        _init: function () {
            // Add widget code here
        },
    });

    return $.goomento.base;
});
