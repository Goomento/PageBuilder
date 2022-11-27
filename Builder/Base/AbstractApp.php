<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;

abstract class AbstractApp extends AbstractModule
{
    /**
     * Print config.
     *
     * Used to print the app and its components settings as a JavaScript object.
     *
     * @param null $handle Optional
     *
     */
    protected function printConfig($handle = null)
    {
        $name = $this->getName();

        $jsVar = 'goomento' . str_replace(' ', '', ucwords(str_replace('-', ' ', $name))) . 'Config';

        $config = $this->getSettings() + $this->getComponentsConfig();

        if (!$handle) {
            $handle = 'goomento-' . $name;
        }

        $config = HooksHelper::applyFilters('pagebuilder/' . static::NAME . '/js_variables', $config)->getResult();

        DataHelper::printJsConfig($handle, $jsVar, $config);
    }

    /**
     * Get components config.
     *
     * Retrieves the app components settings.
     *
     *
     * @return array
     */
    private function getComponentsConfig()
    {
        $settings = [];

        foreach ($this->getComponents() as $id => $instance) {
            $settings[ $id ] = $instance->getSettings();
        }

        return $settings;
    }
}
