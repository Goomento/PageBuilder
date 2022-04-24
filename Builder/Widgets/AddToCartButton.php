<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;

class AddToCartButton extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'addcart-button';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/addcart_button.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Add To Cart');
    }

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
    public function getCategories()
    {
        return ['products'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-cart-plus';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'cart', 'add', 'add to cart'];
    }

    /**
     * @inheirtDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'addcart-button_product_section',
            [
                'label' => __('Product'),
            ]
        );

        $this->addControl(
            'addcart-button_product',
            [
                'label' => __('Products'),
                'type' => Controls::TEXT,
                'description' => __('Enter your product SKU.')
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'addcart-button_title_section',
            [
                'label' => __('Title'),
            ]
        );

        Button::registerButtonInterface($this, self::NAME . '_');

        $this->removeControl(self::NAME . '_link');

        $this->endControlsSection();

        $this->startControlsSection(
            'addcart-button_section_style',
            [
                'label' => __('Button'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Button::registerButtonStyle($this, self::NAME . '_');

        $this->endControlsSection();

    }
}
