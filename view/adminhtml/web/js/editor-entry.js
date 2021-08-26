/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'goomento-editor-engine',
    'swiper',
    'nprogress',
    'perfect-scrollbar',
    'nouislider',
    'domReady!',
], function (
    _,
    $,
    App,
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

    window.goomento = new App;

    setTimeout(() => {
        window.goomento.start();
    }, 500);

    return window.goomento;
});
