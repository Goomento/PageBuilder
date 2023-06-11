/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'jquery',
    'Goomento_PageBuilder/lib/tipsy/tipsy.min'
], function ($) {
    'use strict';

    /**
     * Attach checking event to window.
     */
    let checkTooltip = function () {
        let $items = $('.tooltip-target');
        $items.each((i, ele) => {
            let $target = $( ele );

            /**
             * Check data already bind
             */
            if ( !$target.data('tipsy') ) {
                let gravity = $target.attr( 'data-tooltip-gravity' ) || 's',
                    html = $target.attr( 'data-tooltip-html' ) || false,
                    checkHide = null,
                    title = () => {
                        if (html) {
                            return $target.find(html).length ? $target.find(html).get(0).outerHTML : '';
                        } else {
                            return $target.attr( 'data-tooltip' ) || $target.attr( 'title' );
                        }
                    };

                $target.tipsy({
                    gravity,
                    title,
                    trigger: 'manual',
                    html: !!html,
                }).bind('mouseenter', function () {
                    let tipsy = $(this).data("tipsy"),
                        $tip = tipsy ? tipsy.tip() : null;
                    tipsy && tipsy.show();
                    $tip && $target.attr('data-tooltip-class') && $tip.addClass($target.attr('data-tooltip-class'));
                    checkHide && clearInterval(checkHide);
                }).bind('mouseleave dragstart click blur remove', function () {
                    // Keep the tooltip when hover on itself
                    setTimeout(() => {
                        let tipsy = $(this).data("tipsy"),
                            $tip = tipsy ? tipsy.tip() : null;
                        tipsy && !$tip.is(':hover') && tipsy.hide();
                        checkHide && clearInterval(checkHide);
                        checkHide = setInterval(() => {
                            // Is hovering on tooltip
                            if (!$tip || ($tip && !$tip.is(':hover'))) {
                                tipsy && tipsy.hide();
                                checkHide && clearInterval(checkHide);
                            }

                            if (!$target.length || !$target.is(':visible')) {
                                // Zombie, let remove them all
                                $.fn.tipsy.revalidate();
                                checkHide && clearInterval(checkHide);
                            }
                        }, 50);
                    }, 50);
                });

                $target.off('mouseenter', checkTooltip);
            }
        });
    }

    $(checkTooltip);
    $(document).on('mouseenter click', '.tooltip-target', checkTooltip);
    $(document).on('click', '.tipsy', $.fn.tipsy.revalidate);

    return $.fn.tipsy;
});
