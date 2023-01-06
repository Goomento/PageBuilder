<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Number extends AbstractControlData
{

    const NAME = 'number';

    /**
     * Get number control default settings.
     *
     * Retrieve the default settings of the number control. Used to return the
     * default settings while initializing the number control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'min' => '',
            'max' => '',
            'step' => '',
            'placeholder' => '',
            'title' => '',
            'dynamic' => [
                'categories' => [TagsModule::NUMBER_CATEGORY],
                'active' => true
            ],
        ];
    }

    /**
     * Render number control output in the editor.
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
                <input id="<?= $controlUid; ?>" type="number" min="{{ data.min }}" max="{{ data.max }}" step="{{ data.step }}" class="tooltip-target gmt-control-tag-area" data-tooltip="{{ data.title }}" title="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" />
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
