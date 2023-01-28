/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-base',
], function ($) {
    'use strict';

    /**
     * Text editor widget
     */
    $.widget('goomento.textEditor', $.goomento.base, {
        options: {
            selectors: {
                paragraph: '.gmt-text-editor p:first',
            },
            classes: {
                dropCap: 'gmt-drop-cap',
                dropCapLetter: 'gmt-drop-cap-letter',
            },
            "text-editor_drop_cap": ''
        },
        $paragraph: $(),
        /**
         * @inheritDoc
         * @private
         */
        _initWidget: function () {
            if (this.options["text-editor_drop_cap"] !== 'yes') {
                return;
            }

            const $dropCap = $( '<span>', { class: this.options.classes.dropCap } ),
                $dropCapLetter = $( '<span>', { class: this.options.classes.dropCapLetter } );

            $dropCap.append( $dropCapLetter );

            this.$dropCap = $dropCap;
            this.$dropCapLetter = $dropCapLetter;

            if (!this.$paragraph.length) {
                this.$paragraph = this.$element.find('.gmt-text-editor');
            }

            this.wrapDropCap();
        },
        wrapDropCap() {

            const $paragraph = this.$paragraph;

            if ( ! $paragraph.length ) {
                return;
            }

            const paragraphContent = $paragraph.html().replace( /&nbsp;/g, ' ' ),
                firstLetterMatch = paragraphContent.match( /^ *([^ ] ?)/ );

            if ( ! firstLetterMatch ) {
                return;
            }

            const firstLetter = firstLetterMatch[ 1 ],
                trimmedFirstLetter = firstLetter.trim();

            // Don't apply drop cap when the content starting with an HTML tag
            if ( '<' === trimmedFirstLetter ) {
                return;
            }

            this.dropCapLetter = firstLetter;

            this.$dropCapLetter.text( trimmedFirstLetter );

            const restoredParagraphContent = paragraphContent.slice( firstLetter.length ).replace( /^ */, ( match ) => {
                return new Array( match.length + 1 ).join( '&nbsp;' );
            } );

            $paragraph.html( restoredParagraphContent ).prepend( this.$dropCap );
        }
    });

    return $.goomento.textEditor;
});
