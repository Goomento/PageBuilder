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
        // Should add default JS/CSS to all pages
        HooksHelper::addAction('print_resources', function () {
            return DataHelper::addResourceGlobally() || ThemeHelper::hasContentOnPage();
        });

        HooksHelper::addAction('pagebuilder/register_scripts', [$this, 'beforeRegisterScripts'], 9);

        // Register the widget to be used
        HooksHelper::addAction('pagebuilder/widgets/widgets_registered', [$this, 'registerWidgets']);

        // Register the default resource css, js files
        HooksHelper::addAction('pagebuilder/register_styles', [$this, 'registerStyles']);
        HooksHelper::addAction('pagebuilder/register_scripts', [$this, 'registerScripts']);
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
            ['jquery']
        );

        ThemeHelper::enqueueScript('pagebuilderRegister');
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
            'Goomento_PageBuilder/lib/imagesloaded/imagesloaded.min'
        );

        ThemeHelper::registerScript(
            'dialogs-manager',
            'Goomento_PageBuilder/lib/dialog/dialog.min',
            ['jquery/ui',]
        );

        ThemeHelper::registerScript(
            'swiper',
            'Goomento_PageBuilder/lib/swiper/swiper.min'
        );

        ThemeHelper::inlineScript(
            'swiper',
            "require(['swiper'], Swiper => {window.Swiper = window.Swiper || Swiper});",
            'before'
        );

        ThemeHelper::registerScript(
            'sofish-pen',
            'Goomento_PageBuilder/lib/sofish/pen',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints.min'
        );

        ThemeHelper::registerScript(
            'jquery-tipsy',
            'Goomento_PageBuilder/lib/tipsy/tipsy.min'
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

        ThemeHelper::registerScript(
            'facebook-sdk',
            'Goomento_PageBuilder/js/widgets/facebook-sdk'
        );
    }

    /**
     * @inheritDoc
     */
    public function registerStyles()
    {
        ThemeHelper::registerStyle(
            'fontawesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all.min.css'
        );

        ThemeHelper::registerStyle(
            'hover-animation',
            'Goomento_PageBuilder/lib/hover/hover.min.css'
        );

        ThemeHelper::registerStyle(
            'goomento-animations',
            'Goomento_PageBuilder/lib/animations/animations.min.css'
        );

        ThemeHelper::registerStyle(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr.min.css'
        );

        ThemeHelper::registerStyle(
            'jquery-select2',
            'Goomento_PageBuilder/lib/e-select2/css/e-select2.min.css'
        );

        ThemeHelper::registerStyle(
            'goomento-widgets',
            'Goomento_PageBuilder/css/widgets.css',
            ['goomento-frontend']
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
            Builder\Widgets\FacebookLike::class,
            Builder\Widgets\FacebookContent::class,
            Builder\Widgets\Magento\RecentlyViewedProducts::class,
            Builder\Widgets\Magento\RecentlyComparedProducts::class,
            Builder\Widgets\Magento\NewProducts::class,
            Builder\Widgets\Magento\CmsBlock::class,
            Builder\Widgets\Magento\CmsPage::class,
            Builder\Widgets\Magento\OrdersAndReturns::class,
            Builder\Widgets\PricingTable::class,
            Builder\Widgets\Navigation::class,
        ];

        foreach ($widgets as $widget) {
            $widgetsManager->registerWidgetType($widget);
        }
    }
}
