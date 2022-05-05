/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'Goomento_PageBuilder/js/model/slider',
    'pagebuilderRegister'
], function ($, model, pagebuilderRegister) {
    'use strict';

    pagebuilderRegister.widgetRegister(
        'product-slider',
        model,
        {
            selectors: {
                carousel: '.gmt-product-carousel-wrapper',
                slideContent: '.swiper-slide',
            }
        }
    );
})
