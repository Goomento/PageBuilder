<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Icon extends AbstractControlData
{

    const NAME = 'icon';

    /**
     * Get icons control default settings.
     *
     * Retrieve the default settings of the icons control. Used to return the default
     * settings while initializing the icons control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'options' => [],
            'include' => '',
            'exclude' => '',
        ];
    }

    /**
     * Render icons control output in the editor.
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
				<select id="<?= $control_uid; ?>" class="gmt-control-icon" data-setting="{{ data.name }}" data-placeholder="<?= __('Select Icon'); ?>">
					<option value=""><?= __('Select Icon'); ?></option>
					<# _.each( data.options, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="gmt-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
    }
}
