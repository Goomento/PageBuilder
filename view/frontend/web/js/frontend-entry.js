/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'swiper',
    'Goomento_PageBuilder/js/moduleResolver',
    'domReady!'
], function (
    Swiper,
    moduleResolver
) {
    moduleResolver(function () {
        (function (w, objects) {
            for (let ob in objects) {
                w[ob] = w[ob] ||  objects[ob];
            }
        })(window, {
            'Swiper': Swiper,
        });
        require(['goomento-frontend-engine'], function (Frontend) {
            window.goomentoFrontend = new Frontend();
            if ( ! goomentoFrontend.isEditMode() ) {
                goomentoFrontend.init();
            }
        });
    });
});
