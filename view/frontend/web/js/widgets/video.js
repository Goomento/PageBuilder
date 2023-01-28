/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-base'
], function ($) {
    'use strict';

    /**
     * Video widget
     */
    $.widget('goomento.video', $.goomento.base, {
        options: {
            selectors: {
                imageOverlay: '.gmt-custom-embed-image-overlay',
                video: '.gmt-video',
                videoIframe: '.gmt-video-iframe'
            },
            lightbox: '',
            video_aspect_ratio: '',
        },
        $imageOverlay: $(),
        $video: $(),
        $videoIframe: $(),

        /**
         * Get light box
         * @returns {*}
         */
        getLightBox() {
            return goomentoFrontend.utils.lightbox;
        },
        /**
         * Handle video
         */
        handleVideo() {
            if ( ! this.options.lightbox) {
                this.$imageOverlay.remove();

                this.playVideo();
            }
        },
        /**
         * Play video
         */
        playVideo() {
            if ( this.$video.length ) {
                this.$video[ 0 ].play();

                return;
            }

            const $videoIframe = this.$videoIframe,
                lazyLoad = $videoIframe.data( 'lazy-load' );

            if ( lazyLoad ) {
                $videoIframe.attr( 'src', lazyLoad );
            }

            const newSourceUrl = $videoIframe[ 0 ].src.replace( '&autoplay=0', '' );

            $videoIframe[ 0 ].src = newSourceUrl + '&autoplay=1';

            if ( $videoIframe[ 0 ].src.includes( 'vimeo.com' ) ) {
                const videoSrc = $videoIframe[ 0 ].src,
                    timeMatch = /#t=[^&]*/.exec( videoSrc );

                // Param '#t=' must be last in the URL
                $videoIframe[ 0 ].src = videoSrc.slice( 0, timeMatch.index ) + videoSrc.slice( timeMatch.index + timeMatch[ 0 ].length ) + timeMatch[ 0 ];
            }
        },
        /**
         * Animate video
         */
        animateVideo() {
            this.getLightBox().setEntranceAnimation( goomentoFrontend.getCurrentDeviceSetting( this.options, 'lightbox_content_animation' ) );
        },

        /**
         * Handle Aspect Ratio
         */
        handleAspectRatio() {
            this.getLightBox().setVideoAspectRatio( this.options.video_aspect_ratio );
        },

        /**
         * Bind events
         */
        bindEvents() {
            this.$imageOverlay.on( 'click', this.handleVideo.bind( this ) );
        },
        /**
         * Init widget
         * @private
         */
        _initWidget() {
            const isLightBoxEnabled = this.options.lightbox;

            if ( !isLightBoxEnabled ) {
                this.getLightBox().getModal().hide();
            } else {
                this.handleAspectRatio();
                this.bindEvents();
            }
        }
    });

    return $.goomento.video;
});
