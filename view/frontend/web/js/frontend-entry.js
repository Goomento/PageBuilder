/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'goomento-frontend-engine',
], function (
    Frontend
) {
    'use strict';

    window.goomentoFrontend = window.goomentoFrontend || new Frontend();
    if ( ! goomentoFrontend.isEditMode() ) {
        goomentoFrontend.init();
    }
});
