/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'goomento-editor-introjs-config',
    'Goomento_PageBuilder/js/view/moment-wrapper',
    'domReady!',
], function (
    _,
    $,
    App,
    intro
) {
    'use strict';

    if (typeof goomento === 'undefined') {

        window.goomento = new App;

        /**
         * Custom extended helpers
         * @type {*}
         */
        const helpers = {
            /**
             * Format datetime
             * @param datetime
             * @returns {string}
             */
            formatDatetime: datetime => {
                if (moment(datetime).startOf('day').isSame(moment(new Date()).startOf('day'))) {
                    return moment(datetime).fromNow();
                } else {
                    return moment(datetime).calendar();
                }
            },
            intro
        }

        _.extend(goomento.helpers, helpers);

        $( () => goomento.start() );
    }

    return window.goomento;
});
