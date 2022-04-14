<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Goomento\PageBuilder\Helper\ThemeHelper;

class EntryPoint extends BuilderRegister
{
    /**
     * @inheritDoc
     */
    public function registerScripts()
    {
        /**
         * Use `pagebuilderWidgetRegister` to register js handling which responsible for specify widget
         */
        ThemeHelper::registerScript(
            'pagebuilderRegister',
            'Goomento_PageBuilder/js/action/pagebuilderRegister',
            []
        );

        ThemeHelper::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone.min'
        );

        ThemeHelper::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio.min',
            [
                'backbone'
            ]
        );

        ThemeHelper::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette.min',
            [
                'backbone',
                'backbone.radio'
            ]
        );

        ThemeHelper::registerScript(
            'jquery-numerator',
            'Goomento_PageBuilder/lib/jquery-numerator/jquery-numerator.min',
            [
                'jquery',
                'jquery/ui'
            ]
        );

        ThemeHelper::registerScript(
            'swiper',
            'Goomento_PageBuilder/lib/swiper/swiper.min',
            [],
            '4.4.6'
        );
    }

    /**
     * @inheritDoc
     */
    public function registerStyles()
    {
        ThemeHelper::registerStyle(
            'fontawesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all.min.css',
            [],
            '5.9.0'
        );
    }
}
