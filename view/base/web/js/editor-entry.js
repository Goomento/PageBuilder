/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'Goomento_PageBuilder/js/view/moment-wrapper',
], function (
    _,
    $,
    App
) {
    'use strict';

    if (typeof goomento === 'undefined') {

        // Setup ajax request
        $.ajaxSetup({
            beforeSend: function(jqXHR, settings) {
                let url = new URL(settings.url);
                if (!url.searchParams.get('form_key')) {
                    url.searchParams.set('form_key', window.FORM_KEY || document.getElementsByName('form_key')[0].value);
                    settings.url = url.toString();
                }
            },
        });

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
            }
        }

        _.extend(goomento.helpers, helpers);

        $( () => goomento.start() );
    }

    return window.goomento;
});
