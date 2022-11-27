<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\EscaperHelper;

class BoxShadow extends AbstractBaseMultiple
{
    const NAME = 'box_shadow';

    /**
     * Get box shadow control default value.
     *
     * Retrieve the default value of the box shadow control. Used to return the
     * default values while initializing the box shadow control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [
            'horizontal' => 0,
            'vertical' => 0,
            'blur' => 10,
            'spread' => 0,
            'color' => 'rgba(0,0,0,0.5)',
        ];
    }

    /**
     * Get box shadow control sliders.
     *
     * Retrieve the sliders of the box shadow control. Sliders are used while
     * rendering the control output in the editor.
     *
     *
     * @return array Control sliders.
     */
    public function getSliders()
    {
        return [
            'horizontal' => [
                'label' => __('Horizontal'),
                'min' => -100,
                'max' => 100,
            ],
            'vertical' => [
                'label' => __('Vertical'),
                'min' => -100,
                'max' => 100,
            ],
            'blur' => [
                'label' => __('Blur'),
                'min' => 0,
                'max' => 100,
            ],
            'spread' => [
                'label' => __('Spread'),
                'min' => -100,
                'max' => 100,
            ],
        ];
    }

    /**
     * Render box shadow control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <#
        var defaultColorValue = '';

        if ( data.default.color ) {
            defaultColorValue = ' data-default-color=' + data.default.color; // Quotes added automatically.
        }
        #>
        <div class="gmt-control-field">
            <label class="gmt-control-title"><?= __('Color'); ?></label>
            <div class="gmt-control-input-wrapper">
                <input data-setting="color" class="gmt-shadow-color-picker" type="text" placeholder="<?= EscaperHelper::escapeHtml('Hex/rgba'); ?>" data-alpha="true" {{{ defaultColorValue }}} />
            </div>
        </div>
        <?php
        foreach ($this->getSliders() as $sliderName => $slider):
            $controlUid = $this->getControlUid($sliderName); ?>
            <div class="gmt-shadow-slider gmt-control-type-slider">
                <label for="<?= EscaperHelper::escapeHtml($controlUid); ?>" class="gmt-control-title"><?= $slider['label']; ?></label>
                <div class="gmt-control-input-wrapper">
                    <div class="gmt-slider" data-input="<?= EscaperHelper::escapeHtml($sliderName); ?>"></div>
                    <div class="gmt-slider-input">
                        <input id="<?= EscaperHelper::escapeHtml($controlUid); ?>" type="number"
                               min="<?= EscaperHelper::escapeHtml($slider['min']); ?>"
                               max="<?= EscaperHelper::escapeHtml($slider['max']); ?>"
                               data-setting="<?= EscaperHelper::escapeHtml($sliderName); ?>"/>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php
    }
}
