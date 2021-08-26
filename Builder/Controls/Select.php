<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);
namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class Select
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Select extends BaseData
{

    /**
     * Get select control type.
     *
     * Retrieve the control type, in this case `select`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'select';
    }

    /**
     * Get select control default settings.
     *
     * Retrieve the default settings of the select control. Used to return the
     * default settings while initializing the select control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'options' => [],
        ];
    }

    /**
     * Render select control output in the editor.
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
				<select id="<?= $control_uid; ?>" data-setting="{{ data.name }}">
				<#
					var printOptions = function( options ) {
						_.each( options, function( option_title, option_value ) { #>
								<option value="{{ option_value }}">{{{ option_title }}}</option>
						<# } );
					};

					if ( data.groups ) {
						for ( var groupIndex in data.groups ) {
							var groupArgs = data.groups[ groupIndex ];
								if ( groupArgs.options ) { #>
									<optgroup label="{{ groupArgs.label }}">
										<# printOptions( groupArgs.options ) #>
									</optgroup>
								<# } else if ( _.isString( groupArgs ) ) { #>
									<option value="{{ groupIndex }}">{{{ groupArgs }}}</option>
								<# }
						}
					} else {
						printOptions( data.options );
					}
				#>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }
}
