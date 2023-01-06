<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Text extends AbstractControlData
{
    const NAME = 'text';

    /**
     * Render text control output in the editor.
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
                <input id="<?= $controlUid; ?>" type="{{ data.input_type }}" class="tooltip-target gmt-control-tag-area" data-tooltip="{{ data.title }}" title="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" />
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    /**
     * Get text control default settings.
     *
     * Retrieve the default settings of the text control. Used to return the
     * default settings while initializing the text control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'input_type' => 'text',
            'placeholder' => '',
            'title' => '',
            'dynamic' => [
                'categories' => [
                    TagsModule::TEXT_CATEGORY,
                ],
                'active' => true,
            ],
        ];
    }
}
