<?php
/**
 * @package Goomento_DocBuilder
 * @link https://github.com/Goomento/DocBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\Exception\LocalizedException;

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
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException|LocalizedException
     */
    public static function registerProductFilter(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        /** @var \Goomento\PageBuilder\Model\Config\Source\CatalogCategory $categorySource */
        $categorySource = ObjectManagerHelper::get(\Goomento\PageBuilder\Model\Config\Source\CatalogCategory::class);
        $categoryIds = array_column($categorySource->toOptionArray(), 'value');
        $categoryLabels = array_column($categorySource->toOptionArray(), 'label');

        $widget->addControl(
            $prefix . 'category',
            [
                'label' => __('Categories'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'options' => array_combine($categoryIds, $categoryLabels)
            ]
        );

        $widget->addControl(
            $prefix . 'product',
            [
                'label' => __('Product SKU(s)'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'placeholder' => __('Type SKU ...'),
                'select2options' => [
                    'ajax' => [
                        'url' => UrlBuilderHelper::getUrl('pagebuilder/catalog/search')
                    ]
                ]
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'products_per_row',
            [
                'label' => __('Products Per Row'),
                'type' => Controls::SELECT,
                'default' => '25',
                'options' => [
                    '100' => 1,
                    '50' => 2,
                    '33' => 3,
                    '25' => 4,
                    '20' => 5,
                ],
                'condition' => [
                    $prefix . 'mode' => 'grid'
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'show_pager',
            [
                'label' => __('Display Page Control'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'products_per_page',
            [
                'label' => __('Products Per Page'),
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
