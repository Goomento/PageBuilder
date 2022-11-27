<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Button extends AbstractControlUi
{
    const NAME = 'button';

    /**
     * Get button control default settings.
     *
     * Retrieve the default settings of the button control. Used to
     * return the default settings while initializing the button
     * control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'text' => '',
            'event' => '',
            'button_type' => 'default',
        ];
    }

    /**
     * Render button control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <button type="button" class="gmt-button gmt-button-{{{ data.button_type }}}" data-event="{{{ data.event }}}">{{{ data.text }}}</button>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
