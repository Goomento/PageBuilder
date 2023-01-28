/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'Goomento_PageBuilder/lib/perfect-scrollbar/js/perfect-scrollbar.min'
], function (PerfectScrollbar) {
    'use strict';

    window.PerfectScrollbar = window.PerfectScrollbar || PerfectScrollbar;
    return PerfectScrollbar;
});
