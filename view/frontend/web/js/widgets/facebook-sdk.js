/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Init facebook like
     */
    return function(config = {}, element) {
        config = Object.assign({}, {
            app_id: '',
            app_version: 'v13.0',
            language: 'en_US',
        },config);

        if ($('#fb-root').length) {
            if (goomentoFrontend.isEditMode() && typeof FB !== 'undefined') {
                FB.XFBML.parse()
            }
            return;
        }

        $('body')
            .prepend('<div id="fb-root"></div>');

        setTimeout(function () {
            let href = 'https://connect.facebook.net/{language}/sdk.js#xfbml=1&version={graph-api-version}&appId={your-facebook-app-id}';
            href = href
                .replace('{language}', config.language)
                .replace('{graph-api-version}', config.app_version)
                .replace('{your-facebook-app-id}', config.app_id);

            require([href]);
        }, 50);
    };
});
