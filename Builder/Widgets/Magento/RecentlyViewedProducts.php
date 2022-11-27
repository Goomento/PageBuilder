<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class RecentlyViewedProducts extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-rvp';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/magento/recently_viewed_products.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Recently Viewed Products');
    }


    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerProductWidgetInterface(AbstractWidget $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'num_posts',
            $args + [
                'label' => __('Number of Products to display'),
                'type' => Controls::NUMBER,
                'default' => 5,
            ]
        );

        $widget->addControl(
            $prefix . 'show_attributes',
            $args + [
                'label' => __('Product attributes to show'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'default' => [
                    'name', 'image',
                ],
                'options' => [
                    'name' => __('Name'),
                    'image' => __('Image'),
                    'price' => __('Price'),
                    'learn_more' => __('Learn More Link'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'show_buttons',
            $args + [
                'label' => __('Buttons to show'),
                'type' => Controls::SELECT2,
                'multiple' => true,
                'default' => [
                    'add_to_cart',
                ],
                'options' => [
                    'add_to_cart' => __('Add to Cart'),
                    'add_to_compare' => __('Add to Compare'),
                    'add_to_wishlist' => __('Add to Wishlist'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'template',
            $args + [
                'label' => __('Template'),
                'type' => Controls::SELECT,
                'default' => 'list',
                'options' => [
                    'grid' => __('Products Grid Template'),
                    'list' => __('Products List Template'),
                    'sidebar' => __('Products Sidebar Template'),
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
            'magento_recently_viewed_products_section',
            [
                'label' => __('Recently Viewed Products'),
            ]
        );

        self::registerProductWidgetInterface($this);

        $this->endControlsSection();
    }

    /**
     * @return bool
     */
    protected function renderPreview(): bool
    {
        return false;
    }
}
