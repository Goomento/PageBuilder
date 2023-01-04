/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-image-carousel',
], function ($) {
    'use strict';

    $.widget('goomento.bannerSlider', $.goomento.imageCarousel, {
        options: {
            selectors: {
                carousel: '.gmt-banner-slider-wrapper',
                slideContent: '.swiper-slide',
            },
            slides_to_show: 3,
            infinite: 'yes',
            autoplay: 'yes',
            effect: 'slide',
            speed: 500,
            slides_to_show_mobile: 1,
            slides_to_scroll_mobile: 1,
            slides_to_show_tablet: 1,
            slides_to_scroll_tablet: 1,
            autoplay_speed: 5000,
            pause_on_hover: true,
            navigation: 'both',
            slides_to_scroll: 1,
            image_spacing_custom: {
                size: 20
            },
        }
    });

    return $.goomento.bannerSlider
});
