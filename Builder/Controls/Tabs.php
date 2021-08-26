<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class Tabs
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Tabs extends BaseUi
{

    /**
     * Get tabs control type.
     *
     * Retrieve the control type, in this case `tabs`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'tabs';
    }

    /**
     * Render tabs control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
    }

    /**
     * Get tabs control default settings.
     *
     * Retrieve the default settings of the tabs control. Used to return the
     * default settings while initializing the tabs control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'separator' => 'none',
        ];
    }
}
