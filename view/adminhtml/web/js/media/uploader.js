/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    $.widget('goomento.mediaUploader', {
        options: {
            htmlId: '',
            openDialogUrl: '',
        },
        imageUrl: '',
        /**
         * @inheritDoc
         * @private
         */
        _create: function() {
            $(`#${this.options.htmlId}`).change(this.onChange.bind(this));

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

            window.mediaBucket = this;
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
            let urlPattern = new RegExp('^(https?:\\/\\/)?'+ // validate protocol
                '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|'+ // validate domain name
                '((\\d{1,3}\\.){3}\\d{1,3}))'+ // validate OR ip (v4) address
                '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*'+ // validate port and path
                '(\\?[;&a-z\\d%_.~+=-]*)?'+ // validate query string
                '(\\#[-a-z\\d_]*)?$','i'); // validate fragment locator
            return !!urlPattern.test(urlString);
        },
        onChange: function () {
            let filePathname = ($(`#${this.options.htmlId}`).val() || '').trim();
            if (filePathname !== '') {
                this.imageUrl = this._getFileUrl(filePathname);
                $(this).trigger('selected').off('selected');
                // Reset the image in bucket
                $(`#${this.options.htmlId}`).val('');
            }
        },
        openFrame: function() {
            this.imageUrl = '';
            MediabrowserUtility.openDialog(this.options.openDialogUrl, null, null, null, { 'targetElementId': this.options.htmlId });
            return this;
        },
        get: function () {
            return {
                url: this.imageUrl
            };
        },
        /**
         * Wait for media stop return the file
         * @param callback
         * @returns {Window.mediaBucket}
         */
        onSelected: function (callback) {
            if (this.callback) {
                $(this).off('selected', this.callback);
            }
            $(this).on('selected', callback);
            this.callback = callback;
            return this;
        },
    });

    return $.goomento.mediaUploader;
});
