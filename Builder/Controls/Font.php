<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Font extends AbstractControlData
{
    const NAME = 'font';

    /**
     * Get font control default settings.
     *
     * Retrieve the default settings of the font control. Used to return the default
     * settings while initializing the font control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'groups' => \Goomento\PageBuilder\Helper\Fonts::getFontGroups(),
            'options' => \Goomento\PageBuilder\Helper\Fonts::getFonts(),
        ];
    }

    /**
     * Render font control output in the editor.
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
            <div class="gmt-control-input-wrapper">
                <select id="<?= $controlUid; ?>" class="gmt-control-font-family" data-setting="{{ data.name }}">
                    <option value=""><?= __('Default'); ?></option>
                    <# _.each( data.groups, function( group_label, group_name ) {
                        var groupFonts = getFontsByGroups( group_name );
                        if ( ! _.isEmpty( groupFonts ) ) { #>
                        <optgroup label="{{ group_label }}">
                            <# _.each( groupFonts, function( fontType, fontName ) { #>
                                <option value="{{ fontName }}">{{{ fontName }}}</option>
                            <# } ); #>
                        </optgroup>
                        <# }
                    }); #>
                </select>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
