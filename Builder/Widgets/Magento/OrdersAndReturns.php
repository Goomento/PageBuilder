<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Builder\Managers\Controls;

class OrdersAndReturns extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-oar';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Orders And Returns');
    }

    /**
     * @inheritDoc
     */
    protected function render()
    {
        return '{{widget type="Magento\Sales\Block\Widget\Guest\Form" template="widget/guest/form.phtml"}}';
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'magento_orders_and_returns_section',
            [
                'label' => __('Orders And Returns'),
            ]
        );

        $this->addControl(
            'magento_orders_and_returns_note',
            [
                'raw' => __('This widget has no parameters.'),
                'type' => Controls::RAW_HTML,
                'content_classes' => 'gmt-descriptor',
            ]
        );

        $this->endControlsSection();
    }
}
