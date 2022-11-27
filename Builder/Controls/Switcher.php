<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Switcher extends AbstractControlData
{
    const NAME = 'switcher';

    /**
     * Render switcher control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $controlUid = $this->getControlUid(); ?>
        <div class="gmt-control-field">
            <label for="<?= $controlUid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <label class="gmt-switch">
                    <input id="<?= $controlUid; ?>" type="checkbox" data-setting="{{ data.name }}" class="gmt-switch-input" value="{{ data.return_value }}">
                    <span class="gmt-switch-label" data-on="{{ data.label_on }}" data-off="{{ data.label_off }}"></span>
                    <span class="gmt-switch-handle"></span>
                </label>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    /**
     * Get switcher control default settings.
     *
     * Retrieve the default settings of the switcher control. Used to return the
     * default settings while initializing the switcher control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_off' => __('No'),
            'label_on' => __('Yes'),
            'return_value' => 'yes',
        ];
    }
}
