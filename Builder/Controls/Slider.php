<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Slider extends AbstractBaseUnits
{
    const NAME = 'slider';

    /**
     * Get slider control default values.
     *
     * Retrieve the default value of the slider control. Used to return the default
     * values while initializing the slider control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return array_merge(
            AbstractBaseUnits::getDefaultValue(),
            [
                'size' => '',
                'sizes' => [],
            ]
        );
    }

    /**
     * Get slider control default settings.
     *
     * Retrieve the default settings of the slider control. Used to return the
     * default settings while initializing the slider control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return array_merge(
            parent::getDefaultSettings(),
            [
                'label_block' => true,
                'labels' => [],
                'scales' => 0,
                'handles' => 'default',
                'dynamic' => [
                    'categories' => [TagsModule::NUMBER_CATEGORY],
                    'property' => 'size',
                ],
            ]
        );
    }

    /**
     * Render slider control output in the editor.
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
            <?php $this->printUnitsTemplate(); ?>
            <div class="gmt-control-input-wrapper gmt-clearfix">
                <div class="gmt-control-tag-area ">
                <# if ( isMultiple && ( data.labels.length || data.scales ) ) { #>
                    <div class="gmt-slider__extra">
                        <# if ( data.labels.length ) { #>
                        <div class="gmt-slider__labels">
                            <# jQuery.each( data.labels, ( index, label ) => { #>
                                <div class="gmt-slider__label">{{{ label }}}</div>
                            <# } ); #>
                        </div>
                        <# } if ( data.scales ) { #>
                        <div class="gmt-slider__scales">
                            <# for ( var i = 0; i < data.scales; i++ ) { #>
                                <div class="gmt-slider__scale"></div>
                            <# } #>
                        </div>
                        <# } #>
                    </div>
                <# } #>
                <div class="gmt-slider"></div>
                <# if ( ! isMultiple ) { #>
                    <div class="gmt-slider-input">
                        <input id="<?= $controlUid; ?>" type="number" min="{{ data.min }}" max="{{ data.max }}" step="{{ data.step }}" data-setting="size" />
                    </div>
                <# } #>
                </div>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
