/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'Goomento_PageBuilder/lib/mmenu/mmenu.min'
], function (MMenu) {
    'use strict';

    window.MMenu = window.MMenu || MMenu;
    return MMenu;
});
