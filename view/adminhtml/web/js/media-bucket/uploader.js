/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/image-uploader'
], function ($, _, Element) {
    'use strict';

    const mediaBucket = {
        _data: [],
        _model: null,
        _bucket: '#image-bucket',
        _$bucket: null,
        _event: null,
        add: function (values) {
            this._data = values;
        },
        bucket: function () {
            if (this._$bucket === null) {
                this._$bucket = $(this._bucket);
                this._$bucket.on('change', this._addFileFromMediaGallery.bind(this));
            }

            return this._$bucket;
        },
        /**
         *
         * @returns {Event}
         */
        event: function () {
            if (this._event == null) {
                this._event = new Event('build');
            }
            return this._event;
        },
        /**
         *
         */
        _addFileFromMediaGallery: function () {
            let filePathname = this.bucket().val();

            this.add({
                url: this._getFileUrl(filePathname)
            });

            $(this).trigger('selected').off('selected');
        },
        /**
         * Get full media file
         * @param rawUrl
         * @returns {string|*}
         * @private
         */
        _getFileUrl: function (rawUrl) {
            let matched = rawUrl.match(/___directive\/([a-zA-Z0-9,]*)/i),
                url = rawUrl;
            if (matched && matched[1]) {
                let widget = Base64.mageDecode(matched[1]);
                widget = widget.match(/url=(\"|\')([^\"\']+)(\"|\')/i);
                if (widget && widget[2]) {
                    url = widget[2];
                }
            }

            return this._isUrl(url) ?
                url : goomentoMediaUrl.replace(/\/$/, '') + '/' + url;
        },
        /**
         * Check whether is url or not
         * @param urlString
         * @returns {boolean}
         * @private
         */
        _isUrl: function (urlString) {
            var urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
                '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator
            return !!urlPattern.test(urlString);
        },
        /**
         * Construct the bucket
         * @param model
         */
        initialize: function (model) {
            this._setModel(model);
            $(document.body).on('click.add-media-button', '.insert-media', function () {
                mediaBucket.onSelected(function () {
                    let image = mediaBucket.get();
                    if (!_.isEmpty(image) && tinymce) {
                        let html = '<img src=\'' + image.url + '\' alt=\'\' title=\'\' />';
                        tinymce.activeEditor.execCommand('mceInsertContent', false, html);
                    }
                });
                mediaBucket.openFrame();
            });
        },
        _setModel: function (model) {
            this._model = model;
        },
        /**
         * Get last media, which are stored in bucket
         * @returns {[]}
         */
        get: function () {
            return this._data;
        },
        /**
         * Reset bucket
         *
         * @returns {Window.mediaBucket}
         */
        reset: function () {
            this._model.value([]);
            this._data = [];
            return this;
        },
        /**
         * Open Frame
         */
        openFrame: function () {
            let e = {};
            e.target = '#' + this.bucket().attr('id');
            this.reset()
                ._getModel()
                .openMediaBrowserDialog(null, e);
        },
        /**
         * Get model
         * @returns Element
         */
        _getModel: function () {
            return this._model;
        },
        /**
         * Wait for media stop return the file
         * @param callback
         * @returns {Window.mediaBucket}
         */
        onSelected: function (callback) {
            $(this).one('selected', callback);
            return this;
        },
    };

    window.mediaBucket = mediaBucket;

    return Element.extend({
        defaults: {
            isMultipleFiles: false,
            template: '',
            maxFileSize:"2097152",
            allowedExtensions:"jpg jpeg gif png ico apng",
            label:"Favicon Icon",
            notice:"Not all browsers support all these formats!",
            componentType:"imageUploader",
        },
        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();
            mediaBucket.initialize(this);
            return this;
        },
    });
});
