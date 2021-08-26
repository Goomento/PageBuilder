define([
    'jquery'
], function ($) {

    const windowEventName = 'pagebuilder/frontend/init';

    let registry = {},
        frontendInit = false,
        addAction = function (widgetName, callback) {
            goomentoFrontend.hooks.addAction( 'frontend/element_ready/' + widgetName,  callback);
        },
        wrapper = function (callback, options) {
            return function ($element) {
                options.$element = $element;
                goomentoFrontend.elementsHandler.addHandler( callback, options);
            }
        };

    $( window ).on( windowEventName, () => {
        frontendInit = true;
        let widgets = registry;
        registry = {};
        for (let widgetName in widgets) {
            for (let widgetCallback of widgets[widgetName]) {
                addAction(widgetName, widgetCallback);
            }
        }
    });

    function register (widgetName, callback, options = {}) {
        let defaultSkin = 'default';

        if (!widgetName.includes('.')) {
            widgetName += '.' + defaultSkin;
        }

        if (!(callback instanceof wrapper)) {
            callback = wrapper(callback, options);
        }

        if (false === frontendInit) {
            if (!registry[widgetName]) {
                registry[widgetName] = [];
            }

            registry[widgetName].push(callback);
        } else {
            addAction(widgetName,  callback);
        }
    }

    return register;
});
