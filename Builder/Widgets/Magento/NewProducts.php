<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

class NewProducts extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-np';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/magento/new_products.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('New Products');
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerProductWidgetInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'display_type',
            [
                'label' => __('Display Type'),
                'type' => Controls::SELECT,
                'default' => 'new_products',
                'options' => [
                    'new_products' => __('New products'),
                    'all_products' => __('All products'),
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
                'label' => __('Number of Products per Page'),
                'type' => Controls::NUMBER,
                'default' => 5,
            ]
        );

        $widget->addControl(
            $prefix . 'num_posts',
            [
                'label' => __('Number of Products to display'),
                'type' => Controls::NUMBER,
                'default' => 10,
            ]
        );

        $widget->addControl(
            $prefix . 'template',
            [
                'label' => __('Template'),
                'type' => Controls::SELECT,
                'default' => 'grid',
                'options' => [
                    'grid' => __('Products Grid Template'),
                    'list' => __('Products List Template'),
                    'list_default' => __('Products Images and Names Template'),
                    'list_names' => __('Products Names Only Template'),
                    'list_images' => __('Products Images Only Template'),
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
            'magento_new_products_section',
            [
                'label' => __('New Products'),
            ]
        );

        self::registerProductWidgetInterface($this);

        $this->endControlsSection();
    }
}
