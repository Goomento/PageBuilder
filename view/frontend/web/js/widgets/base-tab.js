/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

define([
    'jquery',
    'goomento-widget-base',
    'jquery-numerator'
], function ($) {
    'use strict';

    /**
     * Base tab widget
     */
    $.widget('goomento.baseTab', $.goomento.base, {
        options: {
            selectors: {
                tabTitles: '.gmt-tab-title',
                tabContents: '.gmt-tab-content',
            },
            classes: {
                active: 'gmt-active',
            },
            showTabFn: 'show',
            hideTabFn: 'hide',
            toggleSelf: true,
            hidePrevious: true,
            autoExpand: true,
        },
        $tabTitles: $(),
        $tabContent: $(),
        /**
         * Init widget
         * @private
         */
        _initWidget() {
            this.bindEvents();
            this.activateDefaultTab();
        },
        /**
         * Activate default tab
         */
        activateDefaultTab() {

            if ( ! this.options.autoExpand || ( 'editor' === this.options.autoExpand && ! this.isEditor ) ) {
                return;
            }

            const defaultActiveTab = this.options.activeItemIndex  || 1;

            this.changeActiveTab( defaultActiveTab );
        },
        /**
         *
         * @param tabIndex
         * @returns Boolean
         */
        isActiveTab( tabIndex ) {
            return this.$tabTitles.filter( '[data-tab="' + tabIndex + '"]' ).hasClass( this.options.classes.active );
        },
        /**
         * Bind events
         */
        bindEvents() {
            this.$tabTitles.on( {
                keydown: ( event ) => {
                    if ( 'Enter' === event.key ) {
                        event.preventDefault();

                        this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
                    }
                },
                click: ( event ) => {
                    event.preventDefault();

                    this.changeActiveTab( event.currentTarget.getAttribute( 'data-tab' ) );
                },
            } );
        },
        /**
         * Toggle tab
         * @param tabIndex
         */
        changeActiveTab( tabIndex ) {
            const isActiveTab = this.isActiveTab( tabIndex );

            if ( ( this.options.toggleSelf || ! isActiveTab ) && this.options.hidePrevious ) {
                this.deactivateActiveTab();
            }

            if ( ! this.options.hidePrevious && isActiveTab ) {
                this.deactivateActiveTab( tabIndex );
            }

            if ( ! isActiveTab ) {
                this.activateTab( tabIndex );
            }
        },
        /**
         * Deactivate tab
         * @param tabIndex
         */
        deactivateActiveTab( tabIndex ) {
            const activeClass = this.options.classes.active,
                activeFilter = tabIndex ? '[data-tab="' + tabIndex + '"]' : '.' + activeClass,
                $activeTitle = this.$tabTitles.filter( activeFilter ),
                $activeContent = this.$tabContents.filter( activeFilter );

            $activeTitle.add( $activeContent ).removeClass( activeClass );

            $activeContent[ this.options.hideTabFn ]();
        },
        /**
         * Active tab
         * @param tabIndex
         */
        activateTab( tabIndex ) {
            const activeClass = this.options.classes.active,
                $requestedTitle = this.$tabTitles.filter( '[data-tab="' + tabIndex + '"]' ),
                $requestedContent = this.$tabContents.filter( '[data-tab="' + tabIndex + '"]' );

            $requestedTitle.add( $requestedContent ).addClass( activeClass );

            $requestedContent[ this.options.showTabFn ]();
        }
    });

    return $.goomento.baseTab;
});
