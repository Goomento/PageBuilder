<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;


abstract class AbstractSettings extends ControlsStack
{

    /**
     * Get CSS wrapper selector.
     *
     * Retrieve the wrapper selector for the current panel.
     *
     * @abstract
     */
    abstract public function getCssWrapperSelector();

    /**
     * Get panel page settings.
     *
     * Retrieve the page setting for the current panel.
     *
     * @abstract
     */
    abstract public function getPanelPageSettings();
}
