/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'Goomento_PageBuilder/js/moduleResolver',
    'swiper',
    'nprogress',
    'perfect-scrollbar',
    'nouislider',
    'domReady!',
], function (
    _,
    $,
    moduleResolver,
    Swiper,
    NProgress,
    PerfectScrollbar,
    noUiSlider
) {

    (function (w, objects) {
        for (let ob in objects) {
            w[ob] = w[ob] ||  objects[ob];
        }
    })(window, {
        'NProgress': NProgress,
        'PerfectScrollbar': PerfectScrollbar,
        'noUiSlider': noUiSlider,
        'Swiper': Swiper,
    });

    moduleResolver(function () {
        moduleResolver.resolveJquery(function () {
            require(['goomento-editor-engine'], function (App) {
                window.goomento = new App;
                goomento.start();
            });
        });
    });
});
