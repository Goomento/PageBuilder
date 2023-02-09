/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

require.config({
    paths: {
        ace: ['//cdnjs.cloudflare.com/ajax/libs/ace/1.2.5']
    }
});

define([], function () {
    'use strict';

    require([
        'ace/ace',
        'ace/ext-language_tools',
    ], (ace) => {
        window.ace = window.ace || ace;
    });

    return window.ace;
});
