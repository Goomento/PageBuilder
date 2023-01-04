<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Icons extends AbstractBaseMultiple
{

    const NAME = 'icons';

    /**
     * Get Icons control default values.
     *
     * Retrieve the default value of the Icons control. Used to return the default
     * values while initializing the Icons control.
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [
            'value'   => '',
            'library' => '',
        ];
    }

    /**
     * Render Icons control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
        <div class="gmt-control-field gmt-control-media">
            <label class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper gmt-aspect-ratio-219">
                <div class="gmt-control-media__content gmt-control-tag-area gmt-control-preview-area gmt-fit-aspect-ratio">
                    <div class="gmt-control-media-upload-button gmt-fit-aspect-ratio">
                        <i class="fas fa-plus-circle" aria-hidden="true"></i>
                    </div>
                    <div class="gmt-control-media-area gmt-fit-aspect-ratio">
                        <div class="gmt-control-media__remove" title="<?= __('Remove'); ?>">
                            <i class="fas fa-trash"></i>
                        </div>
                        <div class="gmt-control-media__preview gmt-fit-aspect-ratio"></div>
                    </div>
                    <div class="gmt-control-media__tools">
                        <div class="gmt-control-icon-picker gmt-control-media__tool"><?= __('Icon Library'); ?></div>
                    </div>
                </div>
            </div>
            <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
            <# } #>
            <input type="hidden" data-setting="{{ data.name }}"/>
        </div>
        <?php
    }

    /**
     * Get Icons control default settings.
     *
     * Retrieve the default settings of the Icons control. Used to return the default
     * settings while initializing the Icons control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'dynamic' => [
                'categories' => [TagsModule::ICON_CATEGORY],
                'returnType' => 'object',
            ],
            'search_bar' => true,
            'recommended' => false,
        ];
    }
}
