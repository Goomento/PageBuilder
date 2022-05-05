<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets\Magento;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;

abstract class AbstractMagentoWidget extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Magento');
    }

    /**
     * @inheirtDoc
     */
    public function getCategories()
    {
        return ['magento'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fab fa-magento';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'magento' ];
    }
}
