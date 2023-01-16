<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

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
                'label' => __('Product SKU(s)'),
                'type' => Controls::SELECT2,
                'description' => __('Enter your product SKU.'),
                'options' => [],
                'select2options' => [
                    'ajax' => [
                        'url' => UrlBuilderHelper::getUrl('pagebuilder/catalog/search')
                    ]
                ]
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'addcart-button_title_section',
            [
                'label' => __('Title'),
            ]
        );

        $prefixKey = self::buildPrefixKey(Button::NAME);

        Button::registerButtonInterface($this, $prefixKey);

        $this->removeControl($prefixKey . 'link');

        $this->addControl(
            $prefixKey . 'html_tag',
            [
                'type' => Controls::HIDDEN,
                'default' => 'button',
            ]
        );

        $this->addControl(
            $prefixKey . 'html_type',
            [
                'type' => Controls::HIDDEN,
                'default' => 'submit',
            ]
        );

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
