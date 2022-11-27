<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

class RecentlyComparedProducts extends AbstractMagentoWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'magento-rcp';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/magento/recently_compared_products.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Recently Compared Products');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'magento_recently_compared_products_section',
            [
                'label' => __('Recently Compared Products'),
            ]
        );

        RecentlyViewedProducts::registerProductWidgetInterface($this, self::NAME . '_');

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
