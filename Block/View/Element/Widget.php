<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\View\Element;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Magento\Framework\View\Element\Template;

class Widget extends Template
{
    /**
     * @return AbstractWidget
     */
    public function getWidget() : AbstractWidget
    {
        return $this->getData('widget');
    }

    /**
     * @return array|string
     */
    public function getSettingsForDisplay(?string $key = null)
    {
        if (empty($settings = $this->getData('settings'))) {
            $settings = $this->getWidget()->getSettingsForDisplay();
            $this->setData('settings', $settings);
        }

        return $key ? ($settings[$key] ?? null) : $settings;
    }
}
