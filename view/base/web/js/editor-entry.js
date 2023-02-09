/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'moment',
    'goomento-editor-introjs-config',
    'domReady!',
], function (
    _,
    $,
    App,
    moment,
    introJsConfig
) {
    'use strict';

    setTimeout(() => {
        window.goomento = window.goomento || new App;
        window.moment = window.moment || moment;
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
        goomento.introJsConfig = introJsConfig;
        $( () => goomento.start() );
    }, 200);
});
