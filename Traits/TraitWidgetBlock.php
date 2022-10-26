<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Magento\Framework\Escaper;

/**
 * Use this under Block template
 */
trait TraitWidgetBlock
{
    /**
     * @return AbstractWidget|null
     */
    public function getWidget() : ?AbstractWidget
    {
        return $this->getData('builder_widget');
    }

    /**
     * @return Escaper
     */
    public function getEscaper() : Escaper
    {
        if (isset($this->_escaper) && $this->_escaper instanceof Escaper) {
            return $this->_escaper;
        }

        return ObjectManagerHelper::get(Escaper::class);
    }

    /**
     * Check whether builder mode or not
     *
     * @return bool
     */
    public function isEnteredBuilderMode() : bool
    {
        return (bool) StateHelper::isBuildable();
    }

    /**
     * @return mixed
     */
    public function setWidget(AbstractWidget $widget)
    {
        return $this->setData('builder_widget', $widget);
    }

    /**
     * @return array|string
     */
    public function getSettingsForDisplay(?string $key = null)
    {
        if (empty($settings = $this->getData('settings')) && $this->getWidget()) {
            $settings = $this->getWidget()->getSettingsForDisplay();
            $this->setData('settings', $settings);
        }

        return $key ? ($settings[$key] ?? null) : $settings;
    }
}
