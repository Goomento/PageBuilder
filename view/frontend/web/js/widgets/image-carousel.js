/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'swiper',
    'goomento-widget-base'
], function ($, Swiper) {
    'use strict';

    /**
     * Carousel widget
     */
    $.widget('goomento.imageCarousel', $.goomento.base, {
        options: {
            selectors: {
                carousel: '.gmt-image-carousel-wrapper',
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
        },
        $carouse: $,
        $slideContent: $,
        /**
         * @returns {{loop: boolean, slidesPerView: (number|number), speed: (number|number|string|string|*)}}
         */
        getSwiperSettings: function() {
            const elementSettings = this.options,
                slidesToShow = +elementSettings.slides_to_show || 3,
                isSingleSlide = 1 === slidesToShow,
                defaultLGDevicesSlidesCount = isSingleSlide ? 1 : 2,
                goomentoBreakpoints = goomentoFrontend.config.breakpoints,
                swiperOptions = {
                    slidesPerView: slidesToShow,
                    loop: 'yes' === elementSettings.infinite,
                    speed: elementSettings.speed,
                };

            swiperOptions.breakpoints = {};

            swiperOptions.breakpoints[goomentoBreakpoints.md] = {
                slidesPerView: +elementSettings.slides_to_show_mobile || 1,
                slidesPerGroup: +elementSettings.slides_to_scroll_mobile || 1,
            };

            swiperOptions.breakpoints[goomentoBreakpoints.lg] = {
                slidesPerView: +elementSettings.slides_to_show_tablet || defaultLGDevicesSlidesCount,
                slidesPerGroup: +elementSettings.slides_to_scroll_tablet || 1,
            };

            if ('yes' === elementSettings.autoplay) {
                swiperOptions.autoplay = {
                    delay: elementSettings.autoplay_speed,
                    disableOnInteraction: !!elementSettings.pause_on_hover,
                };
            }

            if (true === swiperOptions.loop) {
                swiperOptions.loopedSlides = this.$slideContent.length;
            }

            if (isSingleSlide) {
                swiperOptions.effect = elementSettings.effect;

                if ('fade' === elementSettings.effect) {
                    swiperOptions.fadeEffect = {crossFade: true};
                }
            } else {
                swiperOptions.slidesPerGroup = +elementSettings.slides_to_scroll || 1;
            }

            if (elementSettings.image_spacing_custom) {
                swiperOptions.spaceBetween = elementSettings.image_spacing_custom.size;
            }

            const showArrows = 'arrows' === elementSettings.navigation || 'both' === elementSettings.navigation,
                showDots = 'dots' === elementSettings.navigation || 'both' === elementSettings.navigation;

            if (showArrows) {
                swiperOptions.navigation = {
                    prevEl: '.gmt-swiper-button-prev',
                    nextEl: '.gmt-swiper-button-next',
                };
            }

            if (showDots) {
                swiperOptions.pagination = {
                    el: '.swiper-pagination',
                    type: 'bullets',
                    clickable: true,
                };
            }

            swiperOptions.on = {
                init: () => {
                    this.$element.removeClass('gmt-invisible').parent().removeClass('gmt-invisible');
                }
            }

            return swiperOptions;
        },
        _initWidget: function () {
            new Swiper(this.$carousel, this.getSwiperSettings());
        }
    });

    return $.goomento.imageCarousel;
});
