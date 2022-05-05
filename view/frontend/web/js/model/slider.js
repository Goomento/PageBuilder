/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'underscore',
    'jquery',
    'swiper',
], function (_, $, Swiper) {
    'use strict';
    /**
     * Model for Slider
     * The `args` is the JSON and contains
     * {
     *     $element: {jQuery},
     *     settings: {JSON},
     *     ...
     * }
     * @param {object}
     */
    return function (args) {
        let options = _.extend({
                selectors: {
                    carousel: '.swiper-wrapper',
                    slideContent: '.swiper-slide',
                },
                settings: {}
            }, args),
            $element = options.$element,
            $carousel = $element.find( options.selectors.carousel ),
            $swiperSlides = $carousel.find( options.selectors.slideContent ),
            getSwiperSettings = function () {
                const elementSettings = options.settings,
                    slidesToShow = +elementSettings.slides_to_show || 3,
                    isSingleSlide = 1 === slidesToShow,
                    defaultLGDevicesSlidesCount = isSingleSlide ? 1 : 2,
                    goomentoBreakpoints = goomentoFrontend.config.breakpoints;

                const swiperOptions = {
                    slidesPerView: slidesToShow,
                    loop: 'yes' === elementSettings.infinite,
                    speed: elementSettings.speed,
                };

                swiperOptions.breakpoints = {};

                swiperOptions.breakpoints[ goomentoBreakpoints.md ] = {
                    slidesPerView: +elementSettings.slides_to_show_mobile || 1,
                    slidesPerGroup: +elementSettings.slides_to_scroll_mobile || 1,
                };

                swiperOptions.breakpoints[ goomentoBreakpoints.lg ] = {
                    slidesPerView: +elementSettings.slides_to_show_tablet || defaultLGDevicesSlidesCount,
                    slidesPerGroup: +elementSettings.slides_to_scroll_tablet || 1,
                };

                if ( 'yes' === elementSettings.autoplay ) {
                    swiperOptions.autoplay = {
                        delay: elementSettings.autoplay_speed,
                        disableOnInteraction: !! elementSettings.pause_on_hover,
                    };
                }

                if ( true === swiperOptions.loop ) {
                    swiperOptions.loopedSlides = $swiperSlides.length;
                }

                if ( isSingleSlide ) {
                    swiperOptions.effect = elementSettings.effect;

                    if ( 'fade' === elementSettings.effect ) {
                        swiperOptions.fadeEffect = { crossFade: true };
                    }
                } else {
                    swiperOptions.slidesPerGroup = +elementSettings.slides_to_scroll || 1;
                }

                if ( elementSettings.image_spacing_custom ) {
                    swiperOptions.spaceBetween = elementSettings.image_spacing_custom.size;
                }

                const showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation,
                    showDots = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation;

                if ( showArrows ) {
                    swiperOptions.navigation = {
                        prevEl: '.gmt-swiper-button-prev',
                        nextEl: '.gmt-swiper-button-next',
                    };
                }

                if ( showDots ) {
                    swiperOptions.pagination = {
                        el: '.swiper-pagination',
                        type: 'bullets',
                        clickable: true,
                    };
                }

                swiperOptions.on = {
                    init: function () {
                        $element.removeClass('gmt-invisible');
                    }
                }

                return swiperOptions;
            }

        const swiper = new Swiper( $carousel, getSwiperSettings() );

        $element.on('DOMSubtreeModified DOMNodeInserted DOMNodeRemoved', function () {
            swiper.update();
        });
    }
});
