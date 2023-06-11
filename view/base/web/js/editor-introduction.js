/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

require([
    'jquery',
    'introjs'
], function ($, introJs) {
    'use strict';

    /**
     * Get steps
     *
     * @returns {*}
     */
    const steps = () => {
        let previewIframe = document.getElementById("gmt-preview-iframe"),
            previewDocument = previewIframe.contentWindow.document;

        return [
            {
                title: 'Goomento Page Builder Tour Guide 👋',
                intro: 'Follow this guide to use create Page, Landing Page or even Magento website'
            },
            {
                title: 'Bottom Bar',
                element: document.getElementById('gmt-controller'),
                intro: 'The bottom bar contains the buttons/actions which control the editor. Let\'s go through some of significant buttons ...',
            },
            {
                title: 'Toggle View Button',
                element: document.getElementById('gmt-panel-control-editing'),
                intro: 'Click to toggle the view, such as view as Published content ...',
            },
            {
                title: 'Responsive Control Buttons',
                element: document.getElementById('gmt-panel-control-responsive'),
                intro: 'Click to view the content in mobile/tablet or desktop device',
                disableInteraction: false
            },
            {
                title: 'Save Button',
                element: document.getElementById('gmt-panel-saver-button-publish'),
                intro: 'Click to save the current view, you might see one of these labels of button' +
                    '</br><strong>SAVED</strong>: Content saved and ready to use' +
                    '</br><strong>SAVE DRAFT</strong>: Save current view as draft and can be restored later' +
                    '</br><strong>PUBLISH</strong>: Publish current view to storefront'
            },
            {
                title: 'Exit Button',
                element: document.getElementById('gmt-panel-control-back-to-dashboard'),
                intro: 'Click this button to back to Magento!'
            },
            {
                title: 'Widget Panel',
                element: document.getElementById('gmt-panel'),
                intro: 'Contains widget that can be dragged to the <strong>Drop Area</strong> for building page<br/>' +
                    '<i>TIP</i> You even can drag <strong>Widget Panel</strong> to somewhere else that work for you, ' +
                    'drag the "&hellip;" sign at top of panel',
                disableInteraction: false
            },
            {
                title: 'Drop Area',
                element: previewDocument.getElementById('gmt-add-new-section'),
                intro: 'Let drag element from <strong>Widget Panel</strong> to here to build your page'
            },
            {
                title: '"Let drag/drop it!☝️"',
                intro: 'We hope you love the new joy of building content on Magento'
            },
        ]
    }

    $(document).on('click', '#gmt-panel-control-help', () => {
        introJs().setOptions({
            steps: steps(),
            disableInteraction: false,
        }).onbeforechange(async function(targetElement) {
            return new Promise((resolve) => {
                if ($(targetElement).parents('body.pagebuilder-content-canvas').length) {
                    $(targetElement).parents('html, body').animate({
                        scrollTop: $(targetElement).offset().top
                    },  200);
                    setTimeout(resolve, 400);
                } else {
                    resolve();
                }
            });
        }).start();
    });
});
