<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Textarea extends AbstractControlData
{

    const NAME = 'textarea';

    /**
     * Get textarea control default settings.
     *
     * Retrieve the default settings of the textarea control. Used to return the
     * default settings while initializing the textarea control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'rows' => 5,
            'placeholder' => '',
            'dynamic' => [
                'categories' => [TagsModule::TEXT_CATEGORY],
                'active' => true,
            ],
        ];
    }

    /**
     * Render textarea control output in the editor.
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
                <textarea id="<?= $controlUid; ?>" class="gmt-control-tag-area" rows="{{ data.rows }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}"></textarea>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
