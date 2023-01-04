<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Select2 extends AbstractControlData
{

    const NAME = 'select2';

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
        $controlUid = $this->getControlUid(); ?>
        <div class="gmt-control-field">
            <# if ( data.label ) {#>
                <label for="<?= $controlUid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
            <# } #>
            <div class="gmt-control-input-wrapper">
                <# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
                <select id="<?= $controlUid; ?>" class="gmt-select2" type="select2" {{ multiple }} data-setting="{{ data.name }}">
                    <#
                    if ( _.isEmpty( data.options ) && data.select2options.ajax ) {
                        let controlValue = _.isArray(data.controlValue) ? data.controlValue : [data.controlValue];
                        controlValue = _.filter(controlValue);
                        _.each( controlValue , function( label ) {
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
