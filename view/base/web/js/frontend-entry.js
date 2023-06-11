/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'goomento-frontend-engine',
    'jquery',
], function (
    Frontend,
    $
) {
    'use strict';

    window.goomentoFrontend = window.goomentoFrontend || new Frontend();

    if ( ! goomentoFrontend.isEditMode() ) {
        $( window ).trigger( 'pagebuilder/frontend/init', goomentoFrontend);
        $( () => goomentoFrontend.init() );
    }
});
