<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class EntryPoint extends BuilderRegister
{
    /**
     * @inheritDoc
     */
    public function init(array $buildSubject = [])
    {
        HooksHelper::addAction('pagebuilder/register_scripts', [$this, 'beforeRegisterScripts'], 9);
        parent::init($buildSubject);
    }

    /**
     * Pre-define core script and library for use
     *
     * @return void
     */
    public function beforeRegisterScripts()
    {
        /**
         * Use `pagebuilderWidgetRegister` to register js handling which responsible for specify widget
         */
        ThemeHelper::registerScript(
            'pagebuilderRegister',
            'Goomento_PageBuilder/js/action/pagebuilderRegister',
            ['jquery', 'mage/translate']
        );

        // Use default Magento underscore
        ThemeHelper::registerScript(
            'underscore',
            'underscore'
        );

        ThemeHelper::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone.min'
        );

        ThemeHelper::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio.min',
            ['backbone']
        );

        ThemeHelper::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette.min',
            [
                'backbone',
                'backbone.radio'
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function registerScripts()
    {
        $minSuffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerScript(
            'jquery-numerator',
            'Goomento_PageBuilder/lib/jquery-numerator/jquery-numerator.min',
            [
                'jquery',
                'jquery/ui'
            ]
        );

        ThemeHelper::registerScript(
            'imagesloaded',
            'Goomento_PageBuilder/lib/imagesloaded/imagesloaded' . $minSuffix
        );

        ThemeHelper::registerScript(
            'goomento-dialog',
            'Goomento_PageBuilder/lib/dialog/dialog' . $minSuffix,
            ['jquery/ui',]
        );

        ThemeHelper::registerScript(
            'swiper',
            'Goomento_PageBuilder/lib/swiper/swiper.min'
        );

        ThemeHelper::registerScript(
            'goomento-waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints' . $minSuffix
        );

        ThemeHelper::registerScript(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr' . $minSuffix
        );

        ThemeHelper::registerScript(
            'goomento-dialog',
            'Goomento_PageBuilder/lib/dialog/dialog' . $minSuffix
        );

        ThemeHelper::registerScript(
            'nouislider',
            'Goomento_PageBuilder/lib/nouislider/nouislider.min'
        );

        ThemeHelper::registerScript(
            'goomento-waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints-for-editor' . $minSuffix
        );

        ThemeHelper::registerScript(
            'goomento-gallery',
            'Goomento_PageBuilder/lib/e-gallery/js/e-gallery' . $minSuffix
        );

        ThemeHelper::registerStyle(
            'hover-animation',
            'Goomento_PageBuilder/lib/hover/hover.min.css'
        );

        ThemeHelper::registerScript(
            'perfect-scrollbar',
            'Goomento_PageBuilder/lib/perfect-scrollbar/js/perfect-scrollbar' . $minSuffix
        );

        ThemeHelper::registerScript(
            'jquery-easing',
            'Goomento_PageBuilder/lib/jquery-easing/jquery-easing' . $minSuffix,
            ['jquery']
        );

        ThemeHelper::registerScript(
            'nprogress',
            'Goomento_PageBuilder/lib/nprogress/nprogress' . $minSuffix
        );

        ThemeHelper::registerScript(
            'tipsy',
            'Goomento_PageBuilder/lib/tipsy/tipsy' . $minSuffix
        );

        ThemeHelper::registerScript(
            'jquery-goomento-select2',
            'Goomento_PageBuilder/lib/e-select2/js/e-select2.full' . $minSuffix,
            ['jquery']
        );

        ThemeHelper::registerScript(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr' . $minSuffix
        );

        ThemeHelper::registerScript(
            'ace',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace'
        );

        ThemeHelper::registerScript(
            'ace-language-tools',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ext-language_tools',
            ['ace']
        );

        ThemeHelper::registerScript(
            'jquery-hover-intent',
            'Goomento_PageBuilder/lib/jquery-hover-intent/jquery-hover-intent' . $minSuffix,
            ['jquery']
        );

        ThemeHelper::registerScript(
            'iris',
            'Goomento_PageBuilder/lib/color-picker/iris.min',
            [
                'jquery',
                'jquery/ui',
            ]
        );

        ThemeHelper::registerScript(
            'image-carousel',
            'Goomento_PageBuilder/js/widgets/image-carousel'
        );

        ThemeHelper::registerScript(
            'product-slider',
            'Goomento_PageBuilder/js/widgets/product-slider'
        );

        ThemeHelper::registerScript(
            'banner-slider',
            'Goomento_PageBuilder/js/widgets/banner-slider',
            ['goomento-frontend']
        );

        ThemeHelper::registerScript(
            'call-to-action',
            'Goomento_PageBuilder/js/widgets/call-to-action'
        );
    }

    /**
     * @inheritDoc
     */
    public function registerStyles()
    {
        $minSuffix = Configuration::debug() ? '.css' : '.min.css';
        $directionSuffix = DataHelper::isRtl() ? '-rtl' : '';

        ThemeHelper::registerStyle(
            'fontawesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all.min.css'
        );

        ThemeHelper::registerStyle(
            'goomento-animations',
            'Goomento_PageBuilder/lib/animations/animations.min.css'
        );

        ThemeHelper::registerStyle(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr'  . $minSuffix
        );

        ThemeHelper::registerStyle(
            'goomento-gallery',
            'Goomento_PageBuilder/lib/e-gallery/css/e-gallery'  . $minSuffix
        );

        ThemeHelper::registerStyle(
            'google-font-roboto',
            'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap'
        );

        ThemeHelper::registerStyle(
            'google-font-inter',
            'https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap'
        );

        ThemeHelper::registerStyle(
            'goomento-select2',
            'Goomento_PageBuilder/lib/e-select2/css/e-select2' . $minSuffix
        );

        ThemeHelper::registerStyle(
            'goomento-widgets',
            'Goomento_PageBuilder/build/widgets' . $directionSuffix . $minSuffix,
            ['goomento-frontend'],
            Configuration::version()
        );
    }

    /**
     * @inheritDoc
     */
    public function registerWidgets(Widgets $widgetsManager)
    {
        $widgets = [
            Builder\Widgets\Accordion::class,
            Builder\Widgets\Alert::class,
            Builder\Widgets\Audio::class,
            Builder\Widgets\Banner::class,
            Builder\Widgets\Block::class,
            Builder\Widgets\Button::class,
            Builder\Widgets\Counter::class,
            Builder\Widgets\Divider::class,
            Builder\Widgets\Text::class,
            Builder\Widgets\Html::class,
            Builder\Widgets\Icon::class,
            Builder\Widgets\IconBox::class,
            Builder\Widgets\IconList::class,
            Builder\Widgets\Image::class,
            Builder\Widgets\ImageBox::class,
            Builder\Widgets\Progress::class,
            Builder\Widgets\SocialIcons::class,
            Builder\Widgets\Spacer::class,
            Builder\Widgets\StarRating::class,
            Builder\Widgets\Video::class,
            Builder\Widgets\Tabs::class,
            Builder\Widgets\TextEditor::class,
            Builder\Widgets\GoogleMaps::class,
            Builder\Widgets\BannerSlider::class,
            Builder\Widgets\ImageCarousel::class,
            Builder\Widgets\ProductList::class,
            Builder\Widgets\ProductSlider::class,
            Builder\Widgets\Testimonial::class,
            Builder\Widgets\Toggle::class,
            Builder\Widgets\AddToCartButton::class,
            Builder\Widgets\CallToAction::class,
        ];

        foreach ($widgets as $widget) {
            $widgetsManager->registerWidgetType($widget);
        }
    }
}
