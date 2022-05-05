<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class ProductSlider extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'product-slider';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/product_list.phtml';

    /**
     * @inheriDoc
     */
    protected $renderer = \Goomento\PageBuilder\Block\Widgets\Product\ProductList::class;

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Product Slider');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-store-alt';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'list', 'product', 'product-slider' , 'slider'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'products' ];
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return ['product-slider'];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_items',
            [
                'label' => __('Product & Category'),
            ]
        );

        ProductList::registerProductFilter($this, self::NAME . '_');

        $this->removeControl([self::NAME . '_mode', self::NAME . '_show_pager']);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_slider',
            [
                'label' => __('Slider'),
            ]
        );

        $slides_to_show = range(1, 10);
        $slides_to_show = array_combine($slides_to_show, $slides_to_show);

        $this->addResponsiveControl(
            'slides_to_show',
            [
                'label' => __('Slides to Show'),
                'type' => Controls::SELECT,
                'options' => [
                        '' => __('Default'),
                    ] + $slides_to_show,
                'frontend_available' => true,
            ]
        );

        $this->addResponsiveControl(
            'slides_to_scroll',
            [
                'label' => __('Slides to Scroll'),
                'type' => Controls::SELECT,
                'description' => __('Set how many slides are scrolled per swipe.'),
                'options' => [
                        '' => __('Default'),
                    ] + $slides_to_show,
                'condition' => [
                    'slides_to_show!' => '1',
                ],
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'navigation',
            [
                'label' => __('Navigation'),
                'type' => Controls::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => __('Arrows and Dots'),
                    'arrows' => __('Arrows'),
                    'dots' => __('Dots'),
                    'none' => __('None'),
                ],
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        ImageCarousel::registerCarouselControl($this, self::NAME . '_');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_navigation',
            [
                'label' => __('Navigation'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'navigation' => [ 'arrows', 'dots', 'both' ],
                ],
            ]
        );

        ImageCarousel::registerNavigationStyle($this, self::NAME . '_', '.gmt-product-carousel-wrapper');

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        // Hide the widget at first, then wait for Swiper
        $this->addRenderAttribute('_wrapper', 'class', 'gmt-invisible');
        $this->addRenderAttribute('items-wrapper', 'class', 'gmt-product-carousel-wrapper swiper-container');
        $this->addRenderAttribute('items', 'class', 'gmt-product-carousel swiper-wrapper');
        $this->addRenderAttribute('item', 'class', 'swiper-slide');

        return parent::render();
    }
}
