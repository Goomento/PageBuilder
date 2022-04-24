/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

require([
    'jquery',
    'Goomento_PageBuilder/js/model/slider',
    'pagebuilderRegister'
], function ($, model, pagebuilderRegister) {
    pagebuilderRegister.widgetRegister(
        'banner-slider',
        model,
        {
            selectors: {
                carousel: '.gmt-banner-slider-wrapper',
                slideContent: '.swiper-slide',
            }
        }
    );
});
