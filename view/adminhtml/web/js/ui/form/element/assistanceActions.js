/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'ko',
    'mage/translate',
    'Goomento_PageBuilder/lib/e-select2/js/e-select2.full.min'
], function ($, ko) {
    'use strict';

    /**
     * Add stylesheet to <head
     * @param href
     */
    const addCss = function (href) {
        href = require.toUrl(href);
        var link = document.createElement('link');
        link.type = 'text/css';
        link.rel = 'stylesheet';
        document.head.appendChild(link);

        link.href = href;
    }

    addCss('Goomento_PageBuilder/lib/e-select2/css/e-select2.min.css');

    /**
     * @constructor
     */
    const AssistanceActions = function () {
        this.refreshContentList = _.throttle(this.refreshContentList.bind(this), 1000);
    };

    AssistanceActions.prototype = {
        config: {
            endpoint: '',
            defaultContentId: {
                value: '',
                label: $.mage.__('Click To Choose'),
            }
        },
        isLoading: ko.observable(true),
        availableContentIds: ko.observable([]),
        setEndpoint: function (url = '') {
            this.config.endpoint = url;
            return this;
        },
        /**
         * Refresh the list
         * @return {AssistanceActions}
         */
        refreshContentList: function () {
            $.get(this.config.endpoint, {
                action: 'list'
            }).done(function (items) {
                items.unshift(this.config.defaultContentId);
                this.availableContentIds(items);
            }.bind(this));
            return this;
        },
        /**
         * Create new content
         * @param data
         * @param cb
         */
        createNewContent: function (data, cb) {
            let post = {action: 'create'};
            if (typeof data === 'string') {
                post.html = data;
            } else {
                Object.assign(post, data);
            }
            $.post(this.config.endpoint, post).done(function (item) {
                let items = this.availableContentIds();
                items.push(item);
                this.availableContentIds(items);
                if (cb) {
                    cb(item);
                }
            }.bind(this));
        },
        /**
         * @param elementId
         * @param storeId
         * @param cb
         */
        initWysiwyg: function (elementId = '', storeId = 0, cb) {
            $.post(this.config.endpoint, {
                element_id: elementId,
                store_id: storeId,
                action: 'wysiwyg'
            }).done(function (data) {
                if (cb && data) {
                    cb(data);
                }
            }.bind(this));
        },
        /**
         * @param identifier
         * @param cb
         * @return {AssistanceActions}
         */
        getEditUrl: function (identifier = '', cb) {
            $.get(this.config.endpoint, {
                action: 'edit',
                identifier: identifier
            }).done(function (data) {
                if (data.href && cb) {
                    cb(data);
                }
            }.bind(this));
            return this;
        }
    };

    return (new AssistanceActions);
});
