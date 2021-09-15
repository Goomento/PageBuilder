<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Helper\StaticEscaper;

/**
 * Class TextShadow
 * @package Goomento\PageBuilder\Builder\Controls
 */
class TextShadow extends BaseMultiple
{

    /**
     * Get text shadow control type.
     *
     * Retrieve the control type, in this case `text_shadow`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'text_shadow';
    }

    /**
     * Get text shadow control default values.
     *
     * Retrieve the default value of the text shadow control. Used to return the
     * default values while initializing the text shadow control.
     *
     *
     * @return array Control default value.
     */
    public function getDefaultValue()
    {
        return [
            'horizontal' => 0,
            'vertical' => 0,
            'blur' => 10,
            'color' => 'rgba(0,0,0,0.3)',
        ];
    }

    /**
     * Get text shadow control sliders.
     *
     * Retrieve the sliders of the text shadow control. Sliders are used while
     * rendering the control output in the editor.
     *
     *
     * @return array Control sliders.
     */
    public function getSliders()
    {
        return [
            'blur' => [
                'label' => __('Blur'),
                'min' => 0,
                'max' => 100,
            ],
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
        ];
    }

    /**
     * Render text shadow control output in the editor.
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
				<input data-setting="color" class="gmt-shadow-color-picker" type="text" placeholder="<?= StaticEscaper::escapeHtml('Hex/rgba'); ?>" data-alpha="true" {{{ defaultColorValue }}} />
			</div>
		</div>
		<?php
        foreach ($this->getSliders() as $slider_name => $slider) :
            $control_uid = $this->getControlUid($slider_name); ?>
			<div class="gmt-shadow-slider gmt-control-type-slider">
				<label for="<?= StaticEscaper::escapeHtml($control_uid); ?>" class="gmt-control-title"><?= $slider['label']; ?></label>
				<div class="gmt-control-input-wrapper">
					<div class="gmt-slider" data-input="<?= StaticEscaper::escapeHtml($slider_name); ?>"></div>
					<div class="gmt-slider-input">
						<input id="<?= StaticEscaper::escapeHtml($control_uid); ?>" type="number"
                               min="<?= StaticEscaper::escapeHtml($slider['min']); ?>"
                               max="<?= StaticEscaper::escapeHtml($slider['max']); ?>"
                               data-setting="<?= StaticEscaper::escapeHtml($slider_name); ?>"/>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
		<?php
    }
}
