<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Wysiwyg extends AbstractControlData
{
    const NAME = 'wysiwyg';

    /**
     * Render wysiwyg control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field">
            <div class="gmt-control-title">{{{ data.label }}}</div>
            <div class="gmt-control-input-wrapper gmt-control-tag-area"></div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }

    /**
     * Retrieve textarea control default settings.
     *
     * Get the default settings of the textarea control. Used to return the
     * default settings while initializing the textarea control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'dynamic' => [
                'categories' => [TagsModule::WYSIWYG_CATEGORY, TagsModule::TEXT_CATEGORY],
                'active' => true
            ],
        ];
    }
}
