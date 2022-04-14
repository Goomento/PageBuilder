/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'ko',
    'mage/translate'
], function ($, ko) {
    'use strict';

    const AssistanceActions = function () {};

    AssistanceActions.prototype = {
        config: {
            endpoint: '',
            defaultContentId: {
                value: '',
                label: $.mage.__('Click To Change Page Builder'),
            }
        },
        isLoading: ko.observable(true),
        availableContentIds: ko.observable([]),
        setEndpoint: function (url = '') {
            this.config.endpoint = url;
            return this;
        },
        refreshContentList: function () {
            $.get(this.config.endpoint, {
                action: 'list'
            }).done(function (items) {
                items.unshift(this.config.defaultContentId);
                this.availableContentIds(items);
            }.bind(this));
            return this;
        },
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
        getEditUrl: function (identifier = '', cb) {
            $.get(this.config.endpoint, {
                action: 'edit',
                identifier: identifier
            }).done(function (data) {
                if (data.href && cb) {
                    cb(data.href);
                }
            }.bind(this));
            return this;
        }
    };

    return (new AssistanceActions);
});
