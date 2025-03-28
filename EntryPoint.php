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
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class EntryPoint extends BuilderRegister
{
    /**
     * Construct the builder
     */
    public function __construct()
    {
        // Run the internal packages
        $this->_construct();
    }

    /**
     * @return void
     */
    private function _construct()
    {
        ObjectManagerHelper::get(Builder\Modules\Preview::class);
        ObjectManagerHelper::get(Builder\Modules\Frontend::class);
        ObjectManagerHelper::get(Builder\Modules\Editor::class);
        ObjectManagerHelper::get(Builder\Modules\Common::class);
        ObjectManagerHelper::get(Builder\Managers\Sources::class);
        ObjectManagerHelper::get(Builder\Managers\Controls::class);
        ObjectManagerHelper::get(Builder\Managers\Schemes::class);
        ObjectManagerHelper::get(Builder\Managers\Elements::class);
        ObjectManagerHelper::get(Builder\Managers\Widgets::class);
        ObjectManagerHelper::get(Builder\Managers\Icons::class);
        ObjectManagerHelper::get(Builder\Managers\Tags::class);
        ObjectManagerHelper::get(Builder\Managers\Revisions::class);
        ObjectManagerHelper::get(Builder\Managers\Documents::class);
        ObjectManagerHelper::get(Builder\Managers\Settings::class);
    }

    /**
     * @inheritDoc
     */
    public function init(array $buildSubject = [])
    {
        // Should add default JS/CSS to all pages
        HooksHelper::addAction('print_resources', function () {
            return DataHelper::addResourceGlobally() || ThemeHelper::hasContentOnPage();
        });

        // Register the widget to be used
        HooksHelper::addAction('pagebuilder/widgets/widgets_registered', [$this, 'registerWidgets']);

        // Register the default resource css, js files
        HooksHelper::addAction('pagebuilder/register_styles', [$this, 'registerStyles'], 8);
        HooksHelper::addAction('pagebuilder/register_scripts', [$this, 'registerScripts'], 8);
    }

    /**
     * @inheritDoc
     */
    public function registerScripts()
    {
        ThemeHelper::registerScript(
            'goomento-widget-base',
            'Goomento_PageBuilder/js/widgets/base'
        );

        ThemeHelper::registerScript(
            'jquery-numerator',
            'Goomento_PageBuilder/lib/jquery-numerator/jquery-numerator.min',
            ['jquery']
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
            'Goomento_PageBuilder/js/view/swiper-wrapper'
        );

        ThemeHelper::registerScript(
            'pen',
            'Goomento_PageBuilder/lib/sofish/pen'
        );

        ThemeHelper::registerScript(
            'jquery-waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'mmenu',
            'Goomento_PageBuilder/js/view/mmenu-wrapper',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'jquery-tipsy',
            'Goomento_PageBuilder/js/view/tipsy-wrapper',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'goomento-backend',
            'Goomento_PageBuilder/js/goomento-backend'
        );

        ThemeHelper::registerScript(
            'goomento-widget-banner-slider',
            'Goomento_PageBuilder/js/widgets/banner-slider'
        );

        ThemeHelper::registerScript(
            'goomento-facebook-sdk',
            'Goomento_PageBuilder/js/widgets/facebook-sdk'
        );

        ThemeHelper::registerScript(
            'goomento-widget-image-carousel',
            'Goomento_PageBuilder/js/widgets/image-carousel'
        );

        ThemeHelper::registerScript(
            'goomento-widget-product-slider',
            'Goomento_PageBuilder/js/widgets/product-slider'
        );

        ThemeHelper::registerScript(
            'goomento-widget-alert',
            'Goomento_PageBuilder/js/widgets/alert'
        );

        ThemeHelper::registerScript(
            'goomento-widget-progress',
            'Goomento_PageBuilder/js/widgets/progress'
        );

        ThemeHelper::registerScript(
            'goomento-widget-counter',
            'Goomento_PageBuilder/js/widgets/counter'
        );

        ThemeHelper::registerScript(
            'goomento-widget-text-editor',
            'Goomento_PageBuilder/js/widgets/text-editor'
        );

        ThemeHelper::registerScript(
            'goomento-widget-tabs',
            'Goomento_PageBuilder/js/widgets/tabs'
        );

        ThemeHelper::registerScript(
            'goomento-widget-toggle',
            'Goomento_PageBuilder/js/widgets/toggle'
        );

        ThemeHelper::registerScript(
            'goomento-widget-accordion',
            'Goomento_PageBuilder/js/widgets/accordion'
        );

        ThemeHelper::registerScript(
            'goomento-widget-video',
            'Goomento_PageBuilder/js/widgets/video'
        );

        ThemeHelper::registerScript(
            'goomento-calltoaction',
            'Goomento_PageBuilder/js/widgets/call-to-action'
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

        ThemeHelper::registerScript(
            'mmenu',
            'Goomento_PageBuilder/lib/mmenu/mmenu.min.css'
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
            'pen',
            'Goomento_PageBuilder/lib/sofish/pen.css'
        );

        ThemeHelper::registerStyle(
            'introjs',
            'Goomento_PageBuilder/lib/intro/intro.min.css'
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
            Builder\Widgets\PageBuilder::class,
            Builder\Widgets\CallToAction::class,
        ];

        if (!DataHelper::isModuleOutputEnabled('Goomento_PageBuilderForm')) {
            $widgets[] = Builder\Widgets\Form\FormBuilder::class;
            $widgets[] = Builder\Widgets\Form\MultistepForm::class;
        }

        foreach ($widgets as $widget) {
            $widgetsManager->registerWidgetType($widget);
        }
    }
}
