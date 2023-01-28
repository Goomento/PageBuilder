/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-base',
    'jquery-numerator'
], function ($) {
    'use strict';

    /**
     * Counter widget
     */
    $.widget('goomento.counter', $.goomento.base, {
        options: {
            selectors: {
                counterNumber: '.gmt-counter-number',
            },
        },

        $counterNumber: $(),
        /**
         * @inheritDoc
         * @private
         */
        _onFocus: function () {
            const data = this.$counterNumber.data(),
                decimalDigits = data.toValue.toString().match( /\.(.*)/ );

            if ( decimalDigits ) {
                data.rounding = decimalDigits[ 1 ].length;
            }

            this.$counterNumber.numerator( data );
        }
    });

    return $.goomento.counter;
});
