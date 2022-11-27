<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

abstract class AbstractBaseUnits extends AbstractBaseMultiple
{

    /**
     * Get units control default value.
     *
     * Retrieve the default value of the units control. Used to return the default
     * values while initializing the units control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [
            'unit' => 'px',
        ];
    }

    /**
     * Get units control default settings.
     *
     * Retrieve the default settings of the units control. Used to return the default
     * settings while initializing the units control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'size_units' => [ 'px' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0.1,
                    'max' => 10,
                    'step' => 0.1,
                ],
                'rem' => [
                    'min' => 0.1,
                    'max' => 10,
                    'step' => 0.1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                'deg' => [
                    'min' => 0,
                    'max' => 360,
                    'step' => 1,
                ],
                'vh' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                'vw' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
        ];
    }

    /**
     * Print units control settings.
     *
     * Used to generate the units control template in the editor.
     *
     */
    protected function printUnitsTemplate()
    {
        ?>
        <# if ( data.size_units && data.size_units.length > 1 ) { #>
        <div class="gmt-units-choices">
            <# _.each( data.size_units, function( unit ) { #>
            <input id="gmt-choose-{{ data._cid + data.name + unit }}" type="radio" name="gmt-choose-{{ data.name }}" data-setting="unit" value="{{ unit }}">
            <label class="gmt-units-choices-label" for="gmt-choose-{{ data._cid + data.name + unit }}">{{{ unit }}}</label>
            <# } ); #>
        </div>
        <# } #>
        <?php
    }
}
