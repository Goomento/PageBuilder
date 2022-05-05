<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;

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
