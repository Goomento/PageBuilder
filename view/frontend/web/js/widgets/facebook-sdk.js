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
     * Facebook SDK widget
     */
    $.widget('goomento.facebookSdk', $.goomento.base, {
        options: {
            app_id: '',
            app_version: 'v13.0',
            language: 'en_US',
        },
        _initWidget: function () {
            if ($('#fb-root').length) {
                if (this.isEditor && typeof window.FB !== 'undefined') {
                    window.FB.XFBML.parse()
                }
                return;
            }

            $('body').prepend('<div id="fb-root"></div>');

            let href = 'https://connect.facebook.net/{language}/sdk.js#xfbml=1&version={graph-api-version}&appId={your-facebook-app-id}';
            href = href
                .replace('{language}', this.options.language)
                .replace('{graph-api-version}', this.options.app_version)
                .replace('{your-facebook-app-id}', this.options.app_id);

            setTimeout(() => {
                require([href], () => {});
            }, 50);
        }
    });

    return $.goomento.facebookSdk;
});
