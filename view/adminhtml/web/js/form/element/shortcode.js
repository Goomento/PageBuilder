/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/modal'
], function ($, AbstractElement, modal) {
    'use strict';

    let shortcodeHtml = '<div class="modal-wrapper">' +
        '   <div class="modal-inner-content">' +
        '       <div class="modal-widget" style="background: #333;color: #fff;padding: 5px;">' +
        '           <code class="modal-content-widget">{{widget type=&quot;PageBuilderRenderer&quot; identifier=&quot;%identifier&quot;}}</code>' +
        '       </div>' +
        '       <small><i>Copy then paste code above to CMS/Content editor.</i></small>' +
        '       <hr />' +
        '       <div class="modal-xml" style="background: #333;color: #fff;padding: 5px;">' +
        '           <code class="modal-content-widget">' +
        '               &lt;block class=&quot;PageBuilderRenderer&quot; name=&quot;unique-block-name&quot;&gt;\n<br />' +
        '               &nbsp;&nbsp;&lt;arguments&gt;\n<br />' +
        '               &nbsp;&nbsp;&nbsp;&nbsp;&lt;argument name=&quot;identifier&quot; xsi:type=&quot;string&quot;&gt;%identifier&lt;/argument&gt;\n<br />' +
        '               &nbsp;&nbsp;&lt;/arguments&gt;\n<br />' +
        '               &lt;/block&gt;<br />' +
        '           </code>' +
        '       </div>' +
        '       <small><i>Copy then paste code above to <strong>xml</strong> configuration, then replace the <strong>unique-block-name</strong></i></small>' +
        '       <hr />' +
        '       <div class="modal-phtml" style="background: #333;color: #fff;padding: 5px;">' +
        '           <code class="modal-content-widget"> &lt;?= $block-&gt;getLayout()-&gt;getBlock(\'PageBuilderRenderer\')-&gt;setIdentifier(\'%identifier\')-&gt;toHtml(); ?&gt;</code>' +
        '       </div>' +
        '       <small><i>Copy then paste code above to <strong>.phtml</strong> or <strong>.php</strong> file.</i></small>' +
        '   </div>' +
        '</div>';

    return AbstractElement.extend({
        defaults: {
            bodyTmpl: 'Goomento_PageBuilder/grid/columns/shortcode'
        },
        $modal: null,
        identifier: null,
        modalOptions: {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: $.mage.__('Shortcode'),
            buttons: [{
                text: $.mage.__('Close'),
                class: 'modal-close',
                click: function (){
                    this.closeModal();
                }
            }]
        },
        /**
         * Get Modal
         * @return {null}
         * @private
         */
        _getModal: function () {
            let html = shortcodeHtml.replaceAll('%identifier', this.identifier);
            if (this.$modal === null) {
                this.$modal = $('<div />');
                modal(this.modalOptions, this.$modal);
            }

            this.$modal.html(html);

            return this.$modal;
        },
        /**
         * Open modal
         * @param row
         */
        openModal: function (row) {
            this.identifier = row.identifier;
            this._getModal()
                .modal("openModal");
        }
    });
});
