<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Choose extends AbstractControlData
{
    const NAME = 'choose';

    /**
     * Render choose control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $controlUid = $this->getControlUid('{{value}}'); ?>
        <div class="gmt-control-field">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <div class="gmt-choices">
                    <# _.each( data.options, function( options, value ) { #>
                    <input id="<?= $controlUid; ?>" type="radio" name="gmt-choose-{{ data.name }}-{{ data._cid }}" value="{{ value }}">
                    <label class="gmt-choices-label tooltip-target" for="<?= $controlUid; ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
                        <i class="{{ options.icon }}" aria-hidden="true"></i>
                        <span class="gmt-screen-only">{{{ options.title }}}</span>
                    </label>
                    <# } ); #>
                </div>
            </div>
        </div>

        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    /**
     * Get choose control default settings.
     *
     * Retrieve the default settings of the choose control. Used to return the
     * default settings while initializing the choose control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'options' => [],
            'toggle' => true,
        ];
    }
}
