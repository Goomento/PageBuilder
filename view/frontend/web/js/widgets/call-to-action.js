/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'mage/translate',
    'goomento-widget-base',
    'jquery/jquery.cookie',
    'Magento_Ui/js/modal/modal',
], function ($) {
    'use strict';

    /**
     * Call to action widget
     */
    $.widget('goomento.callToAction', $.goomento.base, {
        options: {
            code: "",
            trigger: "",
            action: "",
            target_element_id: "",
            element_id: "",
            trigger_element_id: "",
            timout: 0,
            remember_in_seconds: 0,
            element_data_id: '',
        },
        /**
         * Init
         * @private
         */
        _initWidget: function () {
            if (!this._validateInSeconds()) {
                return;
            }
            switch (this.options.trigger) {
                case "load":
                    this.onPageLoad();
                    break;
                case "timeout":
                    this.onTimeout();
                    break;
                case "click":
                    this.onClick();
                    break;
                default:
            }
        },
        /**
         * Validate remember in seconds
         * @return {boolean}
         * @private
         */
        _validateInSeconds: function() {
            let rememberInSeconds = parseInt(this.options.remember_in_seconds),
                cookieName = `pagebuilder_ris_${this.options.element_id}`,
                cookieValue = rememberInSeconds ? $.cookie(cookieName) : null;

            if (rememberInSeconds) {
                if (cookieValue) {
                    return false;
                } else {
                    let date = new Date();
                    date.setTime(date.getTime() + (rememberInSeconds * 1000));
                    $.cookie(cookieName, '1', {path: '/', expires: date });
                }
            }
            return true;
        },
        /**
         * Do action
         * @return {(function())|*}
         * @private
         */
        doAction: function () {
            this.onStartDoingAction();

            switch (this.options.action) {
                case "code":
                    this.insertCode();
                    break;
                case "show_popup":
                    this.showPopup();
                    break;
                case "hide_popup":
                    this.hidePopup();
                    break;
                case "show_element":
                    this.showElement();
                    break;
                case "hide_element":
                    this.hideElement();
                    break;
                default:
                    break;
            }

            this.onStoppingDoingAction();
        },
        /**
         * Start doing action
         * @return {callToAction}
         */
        onStartDoingAction: function () {
            return this;
        },
        /**
         * On stop doing action
         * @return {callToAction}
         */
        onStoppingDoingAction: function () {
            return this;
        },
        /**
         *
         * @returns {jQuery|HTMLElement|*}
         * @private
         */
        _getTarget: function () {
            if (typeof this.options.$target === "undefined") {
                this.options.$target = $(this.options.target_element_id);
            }

            return this.options.$target;
        },
        /**
         * Get options of popup
         * @return {{}}
         * @private
         */
        _getPopupOptions: function () {
            let $target = this._getTarget(),
                settings = $target.data('settings') || {},
                options = {},
                buttons = [];

            settings = Object.assign({
                popup_title: $.mage.__('Popup Title'),
                popup_close_button_text: '',
                popup_close_button_css_classes: '',
                popup_confirm_button_text: '',
                popup_confirm_button_css_classes: '',
                popup_confirm_button_link: {
                    url: '',
                    is_external: false,
                },
            }, settings);

            options.responsive = true;
            options.type = 'popup';
            if (settings.popup_title) {
                options.title = settings.popup_title;
            }

            if (settings.popup_close_button_text) {
                buttons.push({
                    text: settings.popup_close_button_text,
                    class: settings.popup_close_button_css_classes,
                    click: function () {
                        this.closeModal();
                    }
                });
            }

            if (settings.popup_confirm_button_text) {
                buttons.push({
                    text: settings.popup_confirm_button_text,
                    class: settings.popup_confirm_button_css_classes,
                    click: function () {
                        let link = settings.popup_confirm_button_link || {};
                        if (link && link.url) {
                            if (link.is_external) {
                                window.open(link.url,'_blank');
                            } else {
                                window.location.href = link.url;
                            }
                        } else {
                            this.closeModal();
                        }
                    }
                });
            }
            options.buttons = buttons;

            return options;
        },
        /**
         * Get popup in cached
         * @return {jQuery|HTMLElement|*}
         * @private
         */
        _getPopup: function () {
            if (typeof this.options.$popup === "undefined") {
                let $target = this._getTarget(),
                    settings = $target.data('settings') || {};
                if (!$target.length || !settings || !$target.hasClass('gmt-section-type-popup')) {
                    this.options.$popup = $();
                } else {
                    this.options.$popup = $target.modal(this._getPopupOptions());
                }
            }
            return this.options.$popup;
        },
        /**
         * Show popup
         */
        showPopup: function () {
            this._getPopup().modal('openModal');
        },
        /**
         * Hide popup
         */
        hidePopup: function () {
            this._getPopup().modal('closeModal');
        },
        /**
         * Show element
         */
        showElement: function () {
            this._getTarget().show();
        },
        /**
         * Hide element
         */
        hideElement: function () {
            this._getTarget().hide();
        },
        /**
         * Insert HTML within document
         */
        insertCode: function () {
            let $element = $(this.options.element_data_id);
            if ($element.length && this.options.code) {
                let textArea = document.createElement('textarea');
                textArea.innerHTML = this.options.code;
                $element.html(textArea.value);
            }
        },
        /**
         * On page load
         */
        onPageLoad: function () {
            $(document).ready(this.doAction.bind(this));
        },
        /**
         * On Timeout
         */
        onTimeout: function () {
            let timeout = parseInt(this.options.timout);
            setTimeout(this.doAction.bind(this), timeout*1000);
        },
        /**
         * On Click action
         */
        onClick: function () {
            $(document).on('click', this.options.trigger_element_id, this.doAction.bind(this));
        }
    });

    return $.goomento.callToAction;
})
