/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/image-uploader'
], function ($, _, Element) {
    window.mediaBucket = {
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
                this._$bucket.on('change', this.addFileFromMediaGallery.bind(this));
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
        addFileFromMediaGallery: function () {
            var fileSize = this.bucket().data('size'),
                fileMimeType = this.bucket().data('mime-type'),
                filePathname = this.bucket().val(),
                fileBasename = filePathname.split('/').pop();

            this.add({
                url: this._getFileUrl(filePathname)
            });

            $(this).trigger('selected').off('selected');
        },
        _getFileUrl: function (rawUrl) {
            let matched = rawUrl.match(/___directive\/([a-zA-Z0-9,]*)/i),
                url = '';
            if (matched[1]) {
                let widget = Base64.mageDecode(matched[1]);
                widget = widget.match(/url=\"([^"]+)\"/i);
                if (widget[1]) {
                    url = widget[1];
                }
            }

            return mediaUrl + url;
        },
        initialize: function (model) {
            this.setModel(model);
            $(document.body).on('click.add-media-button', '.insert-media', function (event) {
                let elem = $( event.currentTarget ),
                    editor = elem.data('editor');

                mediaBucket.onSelected(function () {
                    let image = mediaBucket.get();
                    if (!_.isEmpty(image) && tinymce) {
                        let html = '<img src=\'' + image.url + '\' alt=\'\' title=\'\' />';
                        tinymce.activeEditor.execCommand('mceInsertContent', false, html);
                    }
                }).openFrame();
            });
        },
        setModel: function (model) {
            this._model = model;
        },
        get: function () {
            return this._data;
        },
        reset: function () {
            this._model.value([]);
            this._data = [];
            return this;
        },
        openFrame: function () {
            let e = {};
            e.target = '#' + this.bucket().attr('id');
            this.reset()
                .model().openMediaBrowserDialog(null, e);
        },
        model: function () {
            return this._model;
        },
        onSelected: function (callback) {
            $(this).one('selected', callback);
            return this;
        },
    };

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
        initialize: function () {
            this._super();
            mediaBucket.initialize(this);
            return this;
        },
    });
});
