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
        return ['goomento-widget-product-slider'];
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

        ImageCarousel::registerCarouselImagesControl($this, '');

        $this->removeControl('link_to');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        ImageCarousel::registerCarouselControl($this, '');

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

        ImageCarousel::registerNavigationStyle($this, '', '.gmt-product-carousel-wrapper');

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
