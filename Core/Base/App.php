<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Base;

use Goomento\PageBuilder\Builder\Utils;

/**
 * Class App
 * @package Goomento\PageBuilder\Core\Base
 */
abstract class App extends Module
{

    /**
     * Print config.
     *
     * Used to print the app and its components settings as a JavaScript object.
     *
     * @param null $handle Optional
     *
     */
    final protected function printConfig($handle = null)
    {
        $name = $this->getName();

        $js_var = 'goomento' . str_replace(' ', '', ucwords(str_replace('-', ' ', $name))) . 'Config';

        $config = $this->getSettings() + $this->getComponentsConfig();

        if (! $handle) {
            $handle = 'goomento-' . $name;
        }

        Utils::printJsConfig($handle, $js_var, $config);
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
