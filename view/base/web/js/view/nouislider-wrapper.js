/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
define([
    'Goomento_PageBuilder/lib/nouislider/nouislider.min'
], function (noUiSlider) {
    'use strict';

    window.noUiSlider = window.noUiSlider || noUiSlider;
    return noUiSlider;
});
