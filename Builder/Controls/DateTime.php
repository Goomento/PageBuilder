<?php
namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class DateTime
 * @package Goomento\PageBuilder\Builder\Controls
 */
class DateTime extends BaseData
{

    /**
     * Get date time control type.
     *
     * Retrieve the control type, in this case `date_time`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'date_time';
    }

    /**
     * Get date time control default settings.
     *
     * Retrieve the default settings of the date time control. Used to return the
     * default settings while initializing the date time control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'picker_options' => [],
        ];
    }

    /**
     * Render date time control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $control_uid = $this->getControlUid(); ?>
		<div class="gmt-control-field">
			<label for="<?= $control_uid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
			<div class="gmt-control-input-wrapper">
				<input id="<?= $control_uid; ?>" placeholder="{{ data.placeholder }}" class="gmt-date-time-picker flatpickr" type="text" data-setting="{{ data.name }}">
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }
}
