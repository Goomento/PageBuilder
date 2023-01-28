/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'Goomento_PageBuilder/lib/nprogress/nprogress.min'
], function (NProgress) {
    'use strict';

    window.NProgress = window.NProgress || NProgress;
    return NProgress;
});
