/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'moment',
    'domReady!',
], function (
    _,
    $,
    App,
    moment
) {
    'use strict';

    window.$ = window.$ || $;

    setTimeout(() => {
        window.goomento = window.goomento || new App;
        goomento.helpers = goomento.helpers || {};
        _.extend(goomento.helpers, {
            formatDatetime: datetime => {
                if (moment(datetime).startOf('day').isSame(moment(new Date()).startOf('day'))) {
                    return moment(datetime).fromNow();
                } else {
                    return moment(datetime).calendar();
                }
            }
        });
        $( () => goomento.start() );
    }, 200);
});
