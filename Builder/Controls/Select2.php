<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class Select2
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Select2 extends BaseData
{

    /**
     * Get select2 control type.
     *
     * Retrieve the control type, in this case `select2`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'select2';
    }

    /**
     * Get select2 control default settings.
     *
     * Retrieve the default settings of the select2 control. Used to return the
     * default settings while initializing the select2 control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'options' => [],
            'multiple' => false,
            'select2options' => [],
        ];
    }

    /**
     * Render select2 control output in the editor.
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
			<# if ( data.label ) {#>
				<label for="<?= $control_uid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="gmt-control-input-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?= $control_uid; ?>" class="gmt-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<#
                    if ( _.isEmpty( data.options ) && data.select2options.ajax ) {
                        _.each( data.controlValue , function( label ) {
                            #>
                            <option selected value="{{ label }}">{{{ label }}}</option>
                            <#
                        } );
                    } else {
                        _.each( data.options, function( option_title, option_value ) {
                            var value = data.controlValue;
                            if ( typeof value == 'string' ) {
                                var selected = ( option_value === value ) ? 'selected' : '';
                            } else if ( null !== value ) {
                                var value = _.values( value );
                                var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
                            }
                            #>
                        <option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
                        <# } );
                    } #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }
}
