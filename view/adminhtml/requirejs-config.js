/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
var config = {
    paths: {
        'goomento-builder-actions': 'Goomento_PageBuilder/js/ui/form/element/assistance-actions',
        'goomento-builder-assistance': 'Goomento_PageBuilder/js/ui/form/element/builder-assistance',
        'goomento-backend': 'Goomento_PageBuilder/js/goomento-backend',
    },
    shim: {
        'goomento-builder-assistance': {
            deps: ['jquery']
        }
    },
    deps: ['goomento-backend']
};
