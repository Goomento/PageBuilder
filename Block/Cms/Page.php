<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Cms;

class Page extends \Magento\Cms\Block\Page
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        return $this;
    }
}
