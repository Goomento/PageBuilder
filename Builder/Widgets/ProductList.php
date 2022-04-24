<?php
/**
 * @package Goomento_DocBuilder
 * @link https://github.com/Goomento/DocBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class ProductList extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/product_list.phtml';

    /**
     * @inheriDoc
     */
    const NAME = 'product-list';

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
        return __('Product List');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-store';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'list', 'product', 'product-list'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'products' ];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerProductFilter(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'category',
            $args + [
                'label' => __('Categories'),
                'type' => Controls::TEXT,
                'description' => __('Category IDs, separate by comma.')
            ]
        );

        $widget->addControl(
            $prefix . 'product',
            $args + [
                'label' => __('Products'),
                'type' => Controls::TEXT,
                'placeholder' => __('SKU-1, SKU-2'),
                'description' => __('Product SKUs, separate by comma.')
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'products_per_row',
            $args + [
                'label' => __('Products Per Row'),
                'type' => Controls::SELECT,
                'default' => '25',
                'options' => [
                    '100' => 1,
                    '50' => 2,
                    '33' => 3,
                    '25' => 4,
                    '20' => 5,
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'products_per_page',
            $args + [
                'label' => __('Product Per Page'),
                'type' => Controls::NUMBER,
                'default' => 12,
            ]
        );

        $widget->addControl(
            $prefix . 'mode',
            [
                'label' => __('Mode'),
                'type' => Controls::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('Grid'),
                    'list' => __('List'),
                ],
            ]
        );
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

        self::registerProductFilter($this);

        $this->endControlsSection();
    }
}
