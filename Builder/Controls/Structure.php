<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Structure extends AbstractControlData
{

    const NAME = 'structure';

    /**
     * Render structure control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $presetControlUid = $this->getControlUid('{{ preset.key }}'); ?>
        <div class="gmt-control-field">
            <div class="gmt-control-input-wrapper">
                <#
                var morePresets = getMorePresets();

                if ( morePresets.length ) { #>
                    <div class="gmt-control-structure-presets">
                        <# _.each( morePresets, function( preset ) { #>
                            <div class="gmt-control-structure-preset-wrapper">
                                <input id="<?= $presetControlUid; ?>" type="radio" name="gmt-control-structure-preset-{{ data._cid }}" data-setting="structure" value="{{ preset.key }}">
                                <label for="<?= $presetControlUid; ?>" class="gmt-control-structure-preset">
                                    {{{ goomento.presetsFactory.getPresetSVG( preset.preset, 102, 42 ).outerHTML }}}
                                </label>
                                <div class="gmt-control-structure-preset-title">{{{ preset.preset.join( ', ' ) }}}</div>
                            </div>
                        <# } ); #>
                    </div>
                <# } #>
            </div>
            <div class="gmt-control-structure-reset">
                <i class="fas fa-undo"></i>
                <?= __('Reset'); ?>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    /**
     * Get structure control default settings.
     *
     * Retrieve the default settings of the structure control. Used to return the
     * default settings while initializing the structure control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'separator' => 'none',
            'label_block' => true,
            'show_label' => false,
        ];
    }
}
