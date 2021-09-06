/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery'
], function ($) {

    let addAction = function (widgetType, callback) {
            goomentoFrontend.hooks.addAction( 'frontend/element_ready/' + widgetType,  callback);
        },
        wrapper = function (callback, options) {
            return function ($element) {
                options.$element = $element;
                goomentoFrontend.elementsHandler.addHandler( callback, options);
            }
        },
        /**
         *
         * @type {*[]}
         */
        handlerRegistered = [],
        goomentoInitialized = false,
        widgetTriggers = [],
        saveWidgetTrigger = function ($element, $context) {
            let type = $element.attr( 'data-widget_type' ),
                elementId = $element.attr( 'data-id' );

            if (type && !widgetTriggers.includes(type)) {
                widgetTriggers[type] = [];
            }

            widgetTriggers[type][elementId] = $element;
        },
        triggerSavedWidget = function (widgetType, callback) {
            if (widgetTriggers[widgetType]) {
                for (let elementId in widgetTriggers[widgetType]) {
                    callback(widgetTriggers[widgetType][elementId]);
                }
            }
        };

    $( window ).on( 'pagebuilder/frontend/init', () => {
        goomentoInitialized = true;

        goomentoFrontend.hooks.addAction( 'frontend/element_ready/widget',  saveWidgetTrigger);

        for (let widgetType in handlerRegistered) {
            for (let widgetCallback of handlerRegistered[widgetType]) {
                addAction(widgetType, widgetCallback);
            }
        }
    });

    /**
     * Register widget js library
     *
     *
     * @param widgetName Alias of widget, that is registered in \Goomento\PageBuilder\Helper\Theme::registerScript() action
     *
     * @see \Goomento\PageBuilder\Helper\Theme::registerScript()
     * @param callback The callback will be triggered when when is present in the HTML output
     * @param options Parameters of callback
     */
    let widgetRegister = function (widgetName, callback, options = {}) {
        let defaultSkin = 'default';

        let widgetType = widgetName;
        if (!widgetName.includes('.')) {
            widgetType += '.' + defaultSkin;
        }

        if (!(callback instanceof wrapper)) {
            callback = wrapper(callback, options);
        }

        if (false === goomentoInitialized) {
            if (!handlerRegistered[widgetType]) {
                handlerRegistered[widgetType] = [];
            }
            handlerRegistered[widgetType].push(callback);
        } else {
            triggerSavedWidget(widgetType, callback);
        }
    };

    return {
        'widgetRegister': widgetRegister
    };
});
