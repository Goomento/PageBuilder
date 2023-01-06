<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Tags as TagsModule;

class Url extends AbstractBaseMultiple
{

    const NAME = 'url';

    /**
     * Get url control default values.
     *
     * Retrieve the default value of the url control. Used to return the default
     * values while initializing the url control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
    {
        return [
            'url' => '',
            'is_external' => '',
            'nofollow' => '',
        ];
    }

    /**
     * Get url control default settings.
     *
     * Retrieve the default settings of the url control. Used to return the default
     * settings while initializing the url control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'show_external' => true,
            'placeholder' => __('Paste URL or type'),
            'autocomplete' => true,
            'dynamic' => [
                'categories' => [TagsModule::URL_CATEGORY],
                'property' => 'url',
                'returnType' => 'object',
                'active' => true,
            ],
        ];
    }

    /**
     * Render url control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        $controlUid = $this->getControlUid();

        $moreInputControlUid = $this->getControlUid('more-input');

        $isExternalControlUid = $this->getControlUid('is_external');

        $nofollowControlUid = $this->getControlUid('nofollow'); ?>
        <div class="gmt-control-field gmt-control-url-external-{{{ data.show_external ? 'show' : 'hide' }}}">
            <label for="<?= /** @noEscape */ $controlUid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
            <div class="gmt-control-input-wrapper">
                <i class="fa-circle-notch fas spinning"></i>
                <input id="<?= /** @noEscape */ $controlUid; ?>" class="gmt-control-tag-area gmt-input" data-setting="url" placeholder="{{ data.placeholder }}" />

                <label for="<?= /** @noEscape */ $moreInputControlUid; ?>" class="gmt-control-url-more tooltip-target" data-tooltip="<?= __('Link Options'); ?>">
                    <i class="fas fa-cog" aria-hidden="true"></i>
                </label>
                <input id="<?= /** @noEscape */ $moreInputControlUid; ?>" type="checkbox" class="gmt-control-url-more-input">
                <div class="gmt-control-url-more-options">
                    <div class="gmt-control-url-option">
                        <input id="<?= /** @noEscape */ $isExternalControlUid; ?>" type="checkbox" class="gmt-control-url-option-input" data-setting="is_external">
                        <label for="<?= /** @noEscape */ $isExternalControlUid; ?>"><?= __('Open in new window'); ?></label>
                    </div>
                    <div class="gmt-control-url-option">
                        <input id="<?= /** @noEscape */ $nofollowControlUid; ?>" type="checkbox" class="gmt-control-url-option-input" data-setting="nofollow">
                        <label for="<?= /** @noEscape */ $nofollowControlUid; ?>"><?= __('Add nofollow'); ?></label>
                    </div>
                </div>
            </div>
        </div>
        <# if ( data.description ) { #>
        <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
