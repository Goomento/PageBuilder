/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'domReady!',
], function (
    _,
    $,
    App
) {
    'use strict';

    setTimeout(() => {
        window.goomento = window.goomento || new App;
        $( goomento.start() );
    }, 200);
});
