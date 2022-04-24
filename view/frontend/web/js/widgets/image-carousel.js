/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

require([
    'jquery',
    'Goomento_PageBuilder/js/model/slider',
    'pagebuilderRegister'
], function ($, model, pagebuilderRegister) {
    return pagebuilderRegister.widgetRegister(
        'image-carousel',
        model,
        {
            selectors: {
                carousel: '.gmt-image-carousel-wrapper',
                slideContent: '.swiper-slide',
            }
        }
    );
});
