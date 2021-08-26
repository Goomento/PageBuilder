<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class ImageDimensions
 * @package Goomento\PageBuilder\Builder\Controls
 */
class ImageDimensions extends BaseMultiple
{

    /**
     * Get image dimensions control type.
     *
     * Retrieve the control type, in this case `image_dimensions`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'image_dimensions';
    }

    /**
     * Get image dimensions control default values.
     *
     * Retrieve the default value of the image dimensions control. Used to return the
     * default values while initializing the image dimensions control.
     *
     *
     * @return array Control default value.
     */
    public function getDefaultValue()
    {
        return [
            'width' => '',
            'height' => '',
        ];
    }

    /**
     * Get image dimensions control default settings.
     *
     * Retrieve the default settings of the image dimensions control. Used to return
     * the default settings while initializing the image dimensions control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'show_label' => false,
            'label_block' => true,
        ];
    }

    /**
     * Render image dimensions control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
		<# if ( data.description ) { #>
			<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<div class="gmt-control-field">
			<label class="gmt-control-title">{{{ data.label }}}</label>
			<div class="gmt-control-input-wrapper">
				<div class="gmt-image-dimensions-field">
					<?php $control_uid = $this->getControlUid('width'); ?>
					<input id="<?= $control_uid; ?>" type="text" data-setting="width" />
					<label for="<?= $control_uid; ?>" class="gmt-image-dimensions-field-description"><?= __('Width'); ?></label>
				</div>
				<div class="gmt-image-dimensions-separator">x</div>
				<div class="gmt-image-dimensions-field">
					<?php $control_uid = $this->getControlUid('height'); ?>
					<input id="<?= $control_uid; ?>" type="text" data-setting="height" />
					<label for="<?= $control_uid; ?>" class="gmt-image-dimensions-field-description"><?= __('Height'); ?></label>
				</div>
				<button class="gmt-button gmt-button-success gmt-image-dimensions-apply-button"><?= __('Apply'); ?></button>
			</div>
		</div>
		<?php
    }
}
