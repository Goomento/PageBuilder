<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Divider extends AbstractControlUi
{
    const NAME = 'divider';

    /**
     * Get divider control default settings.
     *
     * Retrieve the default settings of the divider control. Used to
     * return the default settings while initializing the divider
     * control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'separator' => 'none',
        ];
    }

    /**
     * Render divider control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
    }
}
