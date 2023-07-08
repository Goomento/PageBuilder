<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\View\Element;

use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Traits\TraitWidgetBlock;
use Magento\Framework\View\Element\Template;

class Widget extends Template
{
    use TraitWidgetBlock;

    /**
     * @inheritDoc
     */
    public function getCacheKeyInfo()
    {
        $keys = parent::getCacheKeyInfo();
        if ($this->getWidget()) {
            $settings = $this->getWidget()->getSettings();
            // Reference the key via widget settings
            $keys['builder_cache_key'] = EncryptorHelper::uniqueContextId($settings);
            $keys['builder_widget_id'] = $this->getWidget()->getId();
        }

        return $keys;
    }
}
