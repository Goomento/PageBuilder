<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\ThemeHelper;

class Color extends AbstractControlData
{
    const NAME = 'color';

    /**
     * Enqueue color control scripts and styles.
     *
     * Used to register and enqueue custom scripts and styles used by the color
     * control.
     *
     */
    public function enqueue()
    {
        ThemeHelper::registerScript(
            'color-picker-alpha',
            'Goomento_PageBuilder/lib/color-picker/color-picker-alpha',
            ['iris']
        );

        ThemeHelper::enqueueScript('color-picker-alpha');
    }

    /**
     * Render color control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <# var defaultValue = '', dataAlpha = '';
            if ( data.default ) {
                defaultValue = ' data-default-color=' + data.default; // Quotes added automatically.
            }

            if ( data.alpha ) {
                dataAlpha = ' data-alpha=true';
            } #>
        <div class="gmt-control-field">
            <label class="gmt-control-title">
                <# if ( data.label ) { #>
                    {{{ data.label }}}
                <# } #>
                <# if ( data.description ) { #>
                    <span class="gmt-control-field-description">{{{ data.description }}}</span>
                <# } #>
            </label>
            <div class="gmt-control-input-wrapper">
                <input data-setting="{{ name }}" type="text" placeholder="Hex/rgba" {{ defaultValue }}{{ dataAlpha }} />
            </div>
        </div>
        <?php
    }

    /**
     * Get color control default settings.
     *
     * Retrieve the default settings of the color control. Used to return the default
     * settings while initializing the color control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'alpha' => true,
            'scheme' => '',
        ];
    }
}
