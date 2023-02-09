/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'introjs'
], function ($) {
    'use strict';

    const steps = () => {
        let previewIframe = document.getElementById("gmt-preview-iframe"),
            previewDocument = previewIframe.contentWindow.document;

        return [
            {
                title: $.mage.__('Goomento Page Builder Tour Guide 👋'),
                intro: $.mage.__('Follow this guide to use create Page, Landing Page or even Magento website')
            },
            {
                title: $.mage.__('Bottom Bar'),
                element: document.getElementById('gmt-controller'),
                intro: $.mage.__('The bottom bar contains the buttons/actions which control the editor. Let\'s go through some of significant buttons ...'),
            },
            {
                title: $.mage.__('Toggle View Button'),
                element: document.getElementById('gmt-panel-control-editing'),
                intro: $.mage.__('Click to toggle the view, such as view as Published content ...'),
            },
            {
                title: $.mage.__('Responsive Control Buttons'),
                element: document.getElementById('gmt-panel-control-responsive'),
                intro: $.mage.__('Click to view the content in mobile/tablet or desktop device'),
                disableInteraction: false
            },
            {
                title: $.mage.__('Save Button'),
                element: document.getElementById('gmt-panel-saver-button-publish'),
                intro: $.mage.__('Click to save the current view, you might see one of these labels of button' +
                    '</br><strong>SAVED</strong>: Content saved and ready to use' +
                    '</br><strong>SAVE DRAFT</strong>: Save current view as draft and can be restored later' +
                    '</br><strong>PUBLISH</strong>: Publish current view to storefront'
                )
            },
            {
                title: $.mage.__('Exit Button'),
                element: document.getElementById('gmt-panel-control-back-to-dashboard'),
                intro: $.mage.__('Click this button to back to Magento!')
            },
            {
                title: $.mage.__('Widget Panel'),
                element: document.getElementById('gmt-panel'),
                intro: $.mage.__('Contains widget that can be dragged to the <strong>Drop Area</strong> for building page<br/>' +
                    '<i>TIP</i> You even can drag <strong>Widget Panel</strong> to somewhere else that work for you, ' +
                    'drag the "&hellip;" sign at top of panel'),
                disableInteraction: false
            },
            {
                title: $.mage.__('Drop Area'),
                element: previewDocument.getElementById('gmt-add-new-section'),
                intro: $.mage.__('Let drag element from <strong>Widget Panel</strong> to here to build your page')
            },
            {
                title: $.mage.__('"Let drag/drop it!☝️"'),
                intro: $.mage.__('We hope you love the new joy of building content on Magento')
            },
        ]
    }

    /**
     * Return introjs configs
     */
    return {
        steps
    }
});
