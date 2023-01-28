/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'jquery-waypoints',
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
        isEditor: false,
        /**
         * @private
         */
        _create: function() {
            this.$element = $(this.element);
            this.isEditor = this.$element.hasClass( 'gmt-element-edit-mode' ) || this.$element.parent().hasClass( 'gmt-element-edit-mode' );
            if (!_.isEmpty(this.options.selectors)) {
                _.each(this.options.selectors, (selector, key) => {
                    this[`$${key}`] = this.$element.find(selector);
                });
            }

            goomentoFrontend.waypoint(this.$element, this._onFocus.bind(this));

            this._initWidget();
        },
        /**
         * On initializing widget
         * @private
         */
        _initWidget: $.noop,
        /**
         * On widget focusing
         * @private
         */
        _onFocus: $.noop
    });

    return $.goomento.base;
});
