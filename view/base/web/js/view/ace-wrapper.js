/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define(['!domReady'], function () {
    require.config({
        paths: {
            ace: ['//cdnjs.cloudflare.com/ajax/libs/ace/1.2.5']
        }
    })

    setTimeout(() => {
        require([
            'ace/ace',
            'ace/ext-language_tools',
        ], () => {});
    }, 100);
});
