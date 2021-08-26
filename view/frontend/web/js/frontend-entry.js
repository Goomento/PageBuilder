/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'swiper',
    'goomento-frontend-engine',
    'goomento-dialog',
], function (
    _,
    $,
    Swiper,
    Frontend
) {
    window.goomentoFrontend = new Frontend();

    (function (w, objects) {
        for (let ob in objects) {
            w[ob] = w[ob] ||  objects[ob];
        }
    })(typeof module !== 'undefined' ? module.exports : window, {
        'Swiper': Swiper,
    });

    if ( ! goomentoFrontend.isEditMode() ) {
        $( () => goomentoFrontend.init() );
    }

    return window.goomentoFrontend;
});
