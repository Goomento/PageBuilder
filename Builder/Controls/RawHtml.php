<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class RawHtml extends AbstractControlUi
{
    const NAME = 'raw_html';

    /**
     * Render raw html control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <# if ( data.label ) { #>
        <span class="gmt-control-title">{{{ data.label }}}</span>
        <# } #>
        <div class="gmt-control-raw-html {{ data.content_classes }}">{{{ data.raw }}}</div>
        <?php
    }

    /**
     * Get raw html control default settings.
     *
     * Retrieve the default settings of the raw html control. Used to return the
     * default settings while initializing the raw html control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'raw' => '',
            'content_classes' => '',
        ];
    }
}
