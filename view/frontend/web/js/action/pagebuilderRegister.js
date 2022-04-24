/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery'
], function ($) {

    /**
     * Regis widget
     * @param widgetType
     * @param callback
     */
    let addAction = function (widgetType, callback) {
        goomentoFrontend.hooks.addAction( 'frontend/element_ready/' + widgetType,  callback);
    },
    /**
     * This method will pass the parameters into callback, the parameters contains
     * {
     *     $element: {jQuery}, // Elements that bond with widget
     *     settings: {JSON}, // AbstractSettings that set
     *     ... // The 'options' param in `widgetRegister`
     * }
     * @param callback
     * @param options
     * @returns {(function(*=): void)|*}
     */
    wrapper = function (callback, options) {
        return function ($element) {
            options.$element = $element;
            options.settings = widgetRegister.getElementSettings($element);
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
     * @param widgetName Alias of widget, that is registered in \Goomento\PageBuilder\Helper\ThemeHelper::registerScript() action
     *
     * @see \Goomento\PageBuilder\Helper\ThemeHelper::registerScript()
     * @param callback The callback will be triggered when when is present in the HTML output
     * @param options Parameters of callback
     */
    let widgetRegister = function (widgetName, callback, options = {}) {

        let widgetType = widgetName;

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
    }

    /**
     * Fix for incompatible with old JS
     *
     * @type {widgetRegister}
     */
    widgetRegister.widgetRegister = widgetRegister;

    /**
     * Get settings of element
     * @param $object
     * @return {*}
     */
    widgetRegister.getElementSettings = function($object) {
        let cid = null,
            elementSettings = {};

        if (!$object instanceof jQuery) {
            $object = $($object)
        }

        cid = $object.data( 'model-cid' );

        if (cid) {
            const settings = goomentoFrontend.config.elements.data[ cid ],
                attributes = settings.attributes;

            let type = attributes.widgetType || attributes.elType;

            if ( attributes.isInner ) {
                type = 'inner-' + type;
            }

            let settingsKeys = goomentoFrontend.config.elements.keys[ type ];

            if ( ! settingsKeys ) {
                settingsKeys = goomentoFrontend.config.elements.keys[ type ] = [];

                $.each( settings.controls, (name, {frontend_available} ) => {
                    if ( frontend_available ) {
                        settingsKeys.push( name );
                    }
                } );
            }

            $.each( settings.getActiveControls(), function( controlKey ) {
                if ( -1 !== settingsKeys.indexOf( controlKey ) ) {
                    let value = attributes[ controlKey ];

                    if ( value.toJSON ) {
                        value = value.toJSON();
                    }

                    elementSettings[ controlKey ] = value;
                }
            } );
        } else {
            elementSettings = $object.data( 'settings' ) || {};
        }

        return elementSettings;
    }

    /**
     * Manual run trigger on element - which is call JS of widget
     *
     * @param $scope
     */
    widgetRegister.runReadyTrigger = function ($scope) {
        let callback = function () {
            if (!($scope instanceof jQuery)) {
                $scope = $($scope)
            }

            let $elements = $scope.find('.gmt-element');

            $elements.each(function (i, element) {
                goomentoFrontend.elementsHandler.runReadyTrigger(element);
            });
        }.bind(this)

        if (typeof goomentoFrontend === "undefined") {
            $( window ).on( 'pagebuilder/frontend/init', function () {
                goomentoFrontend.on('components:init', callback);
            });
        } else {
            callback();
        }
    }

    return widgetRegister;
});
