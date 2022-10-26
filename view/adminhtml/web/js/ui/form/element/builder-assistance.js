/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/element/abstract',
    'goomento-builder-actions',
    'mage/translate',
    'goomento-backend'
], function ($, _, Abstract, assistanceActions) {
    'use strict';

    goomentoBackend.helpers.addCss('Goomento_PageBuilder/css/ui/form/element/builder-assistance.min.css');
    goomentoBackend.helpers.addCss('Goomento_PageBuilder/lib/e-select2/css/e-select2.min.css');

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Goomento_PageBuilder/ui/form/element/builder_assistance',
            endpoint: '',
            html_name: '',
            template: 'ui/form/field',
            toggleBtnLabel: '',
            wysiwygBtnLabel: $.mage.__('WYSIWYG Editor'),
            message: '',
            selectedContentId: '',
            wrapperId: '',
            contentId: '',
            isMigrating: false,
            originContent: '',
            showSelectBox: false,
            showSelectionList: false,
            showEditBtn: false,
            showCreateBtn: false,
            showMigrateBtn: false,
            createdContentId: '',
            migratedContentId: '',
            isActiveBuilderAssistance: true,
            showWysiwyg: false,
            editorWindow: null,
            wysiwygEditor: '',
            editorUrl: {href: '', open_in: ''},
            $iframe: null,
            availableContentIds: [],
            wrapperClasses: [],
            isLoading: assistanceActions.isLoading,
            cols: 15,
            rows: 3,
        },
        /**
         * @inheritDoc
         * @return {*}
         */
        initialize: function () {
            this._super()
                .observe('toggleBtnLabel message wrapperClasses selectedContentId isActiveBuilderAssistance showWysiwyg wrapperId wysiwygEditor ' +
                    'showSelectionList availableContentIds showMigrateBtn showCreateBtn showEditBtn showSelectBox wysiwygBtnLabel');

            this.originContent = this.value();
            this.wysiwygEditorId = this.uid + '_wysiwyg_editor';
            this.wrapperId(this.uid + '_wrapper');

            assistanceActions
                .setEndpoint(this.endpoint)
                .availableContentIds.subscribe(function (items) {
                    let selectedContentId = this.selectedContentId();
                    this.availableContentIds(items);
                    this.selectedContentId(selectedContentId);
                    this.activatingAssistance();
                }.bind(this));
            return this;
        },
        /**
         * After render the html component callback
         */
        afterRender: function () {
            let content = this.value().trim();
            if (!content || (content && !this._shouldMigrateStr(content))) {
                this._onEndWysiwyg()
                    ._onActiveAssistance();
            } else {
                this._onEndWysiwyg()
                    ._onDeactivateAssistance()
                    .onClickWysiwygBtn();
            }
        },
        /**
         *
         * @private
         */
        _cleanTheBuilderContent: function () {
            let content = this.value();
            content = content.trim();
            if (content !== '') {
                this.value(this._removeWrapping(content));
            } else {
                this.value('');
            }
            this.originContent = this.value();
        },
        /**
         *
         * @return {*}
         * @private
         */
        _migrateScreen: function () {
            this.value(this.originContent);
            this.selectedContentId('');
            this.showCreateBtn(true);
            this.showSelectBox(true);
            this.showMigrateBtn(true);
            this.showEditBtn(false);
            this.message($.mage.__('We found the content did not compatible with Goomento! Please select actions below.'));
            return this;
        },
        /**
         *
         * @return {*}
         * @private
         */
        _selectingScreen: function () {
            this.selectedContentId('');
            this.showCreateBtn(true);
            this.showSelectBox(true);
            this.showMigrateBtn(false);
            this.showEditBtn(false);
            this.message($.mage.__('Please select actions below.'));
            return this;
        },
        /**
         * Open the editing action
         *
         * @return {this}
         * @private
         */
        _editingScreen: function () {
            this.selectedContentId(this.contentId);
            this.showCreateBtn(false);
            this.showSelectBox(true);
            this.showMigrateBtn(false);
            this.showEditBtn(true);
            this.showSelectionList(false);
            this.showSuccessMessage('');
            return this;
        },
        /**
         * On Change Builder
         */
        onClickChangeBuilderBtn: function () {
            this.showSelectionList(true);
        },
        /**
         * Show error message to admin
         */
        showErrorMessage: function (message) {
            this.wrapperClasses(['builder-assistance-message-error']);
            this.message(message);
        },
        /**
         * Show error message to admin
         */
        showSuccessMessage: function (message) {
            this.wrapperClasses(['builder-assistance-message-ok']);
            this.message(message);
        },
        /**
         *
         */
        onSelected: function () {
            let selectedContentId = this.selectedContentId();
            if (selectedContentId) {
                if (selectedContentId !== this.contentId) {
                    let content = '{{widget type="PageBuilderRenderer" identifier="' + selectedContentId + '"}}';
                    this.value(content);
                    this._shouldSaveScreen();
                } else {
                    this._editingScreen();
                }
            } else if (this.isMigrating) {
                this._migrateScreen();
            } else {
                this._selectingScreen();
            }
        },
        /**
         * Activating builder assistance
         */
        activatingAssistance: function () {
            let content = this.value();
            if (content !== this.originContent) {
                this.contentId = this._getMainContentId(content);
                this.selectedContentId('');
                this._shouldSaveScreen();
            } else {
                if (this._shouldMigrateStr(content)) {
                    this.isMigrating = true;
                    this._migrateScreen();
                } else {
                    this._cleanTheBuilderContent();
                    content = this.value();
                    this._initContentId();
                    if (content === '' || !this.contentId) {
                        this.contentId = '';
                        this._selectingScreen();
                    } else {
                        this._editingScreen();
                    }
                }
            }
        },
        /**
         *
         * @private
         */
        _shouldSaveScreen: function () {
            this.showCreateBtn(false);
            this.showSelectBox(true);
            this.showMigrateBtn(false);
            this.showEditBtn(false);
            this.showSelectionList(true);
            this.message($.mage.__('Content changed, click save before continue!'));
        },
        /**
         * Create New Empty Page Builder
         * @private
         */
        onClickCreateBtn: function () {
            let cb = function () {
                this.selectedContentId(this.createdContentId);
                this.onSelected();
            }.bind(this);
            if (this.createdContentId) {
                cb();
            } else {
                assistanceActions.createNewContent({
                    title: this.getContentTitle()
                }, function (item) {
                    this.createdContentId = item.value;
                    cb();
                }.bind(this));
            }
        },
        /**
         * Open the Page Builder Live Editor;
         * @private
         */
        onClickEditBtn: function () {
            let cb = function () {
                let openIn = this.editorUrl.open_in;
                if (openIn === 'same_tab') {
                    window.location.href = this.editorUrl.href;
                } else if (openIn === 'new_tab') {
                    let editorUrl = null;
                    if (!this.editorWindow) {
                        this.editorWindow = window.open(this.editorUrl.href, '_blank');
                    }
                    try {
                        editorUrl = this.editorWindow.location.href;
                    } catch (e) {}
                    if (!editorUrl) {
                        this.editorWindow = window.open(this.editorUrl.href, '_blank');
                    }
                    if (editorUrl !== this.editorUrl.href) {
                        this.editorWindow.location.href = this.editorUrl.href;
                    }
                    this.editorWindow.focus();
                } else if (openIn === 'iframe') {
                    if (!this.$iframe) {
                        this.$iframe = $('#builder-assistance-iframe');
                        if ( !this.$iframe.length ) {
                            this.$iframe = $('<iframe>', {
                                src: '',
                                width: window.innerWidth - $('.admin__menu').width(),
                                id:  'builder-assistance-iframe',
                                frameborder: 0,
                                scrolling: 'no'
                            });
                            this.$iframe.appendTo('body');
                        }

                        window.closeBuilderAssistanceIFrame = () => {
                            this.$iframe.hide();
                            this.$iframe.remove();
                            this.$iframe = null;
                        };
                    }

                    let editorUrl = new URL(this.editorUrl.href);
                    editorUrl.searchParams.set('exitCallback', 'closeBuilderAssistanceIFrame');
                    this.$iframe.attr('src', editorUrl.toString());
                    this.$iframe.css('left', $('.admin__menu').width());
                }
            }.bind(this);

            if (!this.editorUrl.href) {
                assistanceActions.getEditUrl(this.contentId, function (editorUrl) {
                    this.editorUrl = editorUrl;
                    cb();
                }.bind(this));
            } else {
                cb();
            }
        },
        /**
         * Retrieve sample title
         * @return {string}
         */
        getContentTitle: function () {
            let result = '',
                $title = $('input[name="title"]');

            if ($title.length && $title.val()) {
                result = $title.val();
            } else {
                let title = document.title.split('/');
                if (title.length) {
                    result = title[0];
                }
            }

            return result;
        },
        /**
         * On Click The Migrate Button
         */
        onClickMigrateBtn: function () {
            let cb = function () {
                this.selectedContentId(this.migratedContentId);
                this.onSelected();
            }.bind(this);
            if (this.migratedContentId) {
                cb();
            } else {
                let content = this.value();
                assistanceActions.createNewContent({
                    html: content,
                    title: this.getContentTitle()
                }, function (item) {
                    this.migratedContentId = item.value;
                    cb();
                }.bind(this));
            }

        },
        /**
         * Toggle Assistance button
         */
        onClickAssistanceBtn: function () {
            if (this.isActiveBuilderAssistance()) {
                this._onDeactivateAssistance();
            } else {
                this._onActiveAssistance();
            }
        },
        /**
         *
         * @return {*}
         */
        onClickWysiwygBtn: function () {
            let cb = function () {
                if (!this.showWysiwyg()) {
                    this._onStartWysiwyg();
                } else {
                    this._onEndWysiwyg();
                }
            }.bind(this)
            if (!this.wysiwygEditor()) {
                assistanceActions.initWysiwyg(this.wysiwygEditorId, 0, function (data) {
                    this.wysiwygEditor(data.html);
                    let $wysiwygEditor = $(`#${this.wysiwygEditorId}`);
                    $wysiwygEditor.on('change', function () {
                        this.value($wysiwygEditor.val());
                    }.bind(this));
                    cb();
                }.bind(this));
            } else {
                cb();
            }

            return this;
        },
        /**
         *
         * @return {*}
         * @private
         */
        _onActiveAssistance:function () {
            this.toggleBtnLabel($.mage.__('Deactivate Pagebuilder'));
            this.isActiveBuilderAssistance(true);
            this.activatingAssistance();
            return this;
        },
        /**
         * Deactive the Builder Assistance
         * @return {*}
         * @private
         */
        _onDeactivateAssistance:function () {
            this.toggleBtnLabel('Activate Pagebuilder');
            this.isActiveBuilderAssistance(false);
            return this;
        },
        /**
         *
         * @return {*}
         * @private
         */
        _onStartWysiwyg:function () {
            this.showWysiwyg(true);
            this.wysiwygBtnLabel($.mage.__('Deactivate WYSIWYG'));

            if (this.wysiwygEditor()) {
                let $wysiwygEditor = $(`#${this.wysiwygEditorId}`);
                $wysiwygEditor.val(this.value());
            }

            return this;
        },
        /**
         *
         * @return {*}
         * @private
         */
        _onEndWysiwyg:function () {
            this.showWysiwyg(false);
            this.wysiwygBtnLabel($.mage.__('WYSIWYG Editor'));

            return this;
        },
        /**
         * Common remove <p> from WYSIWYG Editor
         * @param str
         * @return {string|*|jQuery}
         * @private
         */
        _removeWrapping: function (str = '') {
            str = str.trim();
            try {
                let $html = $(str);
                return $html.length !== 1 ? str : $html.unwrap().html();
            } catch (e) {
                return str;
            }
        },
        /**
         * Get Content Id in editors
         * @return {string}
         * @private
         */
        _initContentId: function () {
            let content = this.originContent;
            this.contentId = this._getMainContentId(content);
            this._validateContentId();
            return this;
        },
        _validateContentId: function () {
            if (this.contentId) {
                let items = this.availableContentIds(),
                    contentId = this.contentId;
                this.contentId = '';
                for (let item of items) {
                    if (item.value === contentId) {
                        this.contentId = contentId;
                        break;
                    }
                }
            }
            return this;
        },
        /**
         * Get Main Content Id
         * @param str
         * @return {string|null|string}
         * @private
         */
        _getMainContentId: function (str = '') {
            if (!this._shouldMigrateStr(str)) {
                let widgets = this._parseWidgets(str);
                return widgets.length ? this._getPageBuilderIdentifier(widgets[0]) : '';
            }
            return '';
        },
        /**
         *
         * @param widget
         * @return {string|null}
         * @private
         */
        _getPageBuilderIdentifier: function (widget = '') {
            widget = widget.trim();
            let matches = widget.match(/\{\{widget\s+type="PageBuilderRenderer"\s+identifier="([^"]+)"\}\}/i);
            if (matches && matches.length === 2) {
                return matches[1];
            }

            return null;
        },
        /**
         *
         * @param str
         * @return {RegExpMatchArray|*[]}
         * @private
         */
        _parseWidgets: function (str = '') {
            let widgets = str.match(/\{\{widget(.*?)\}\}/ig);
            return widgets ? widgets : [];
        },
        /**
         *
         * @param str
         * @return {boolean}
         * @private
         */
        _shouldMigrateStr: function (str = '') {
            str = str.trim();
            if (str !== '') {
                let widgets = this._parseWidgets(str);
                if (widgets.length !== 1) {
                    return true;
                }
                if (this._getPageBuilderIdentifier(widgets[0])) {
                    return widgets[0] !== this._removeWrapping(str);
                }
            }
            return false;
        }
    })
});
