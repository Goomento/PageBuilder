/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'ko',
    'mage/translate',
    'goomento-backend',
    'Goomento_PageBuilder/lib/e-select2/js/e-select2.full.min'
], function ($, ko) {
    'use strict';

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
                label: $.mage.__('Select page builder'),
            }
        },
        isLoading: ko.observable(true),
        availableContentIds: ko.observable([]),
        setEndpoint: function (url = '') {
            this.config.endpoint = url;
            this.refreshContentList();
            return this;
        },
        /**
         * Go ajax
         * @returns {*}
         */
        doAjax: function (data, method = 'get', loader = true) {
            let result = $.Deferred();
            loader && $('body').trigger('processStart');
            $.ajax({
                url: this.config.endpoint,
                type: (method || 'get').toUpperCase(),
                data: data,
                success: data => { result.resolve(data) },
                error: () => { result.reject() },
                complete: () => {
                    loader && $('body').trigger('processStop');
                },
            });
            return result;
        },
        /**
         * Refresh the list
         * @return {AssistanceActions}
         */
        refreshContentList: function () {
            this.doAjax({
                action: 'list'
            }, 'get', false).done(function (items) {
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
            this.doAjax(post, 'post').done(function (item) {
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
            this.doAjax({
                element_id: elementId,
                store_id: storeId,
                action: 'wysiwyg'
            }, 'post').done(function (data) {
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
            this.doAjax({
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
