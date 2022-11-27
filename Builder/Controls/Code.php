<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

class Code extends AbstractControlData
{
    const NAME = 'code';

    /**
     * Get code control default settings.
     *
     * Retrieve the default settings of the code control. Used to return the default
     * settings while initializing the code control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'label_block' => true,
            'language' => 'html', // html/css
            'rows' => 10,
        ];
    }

    /**
     * Render code control output in the editor.
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
                <textarea id="<?= $controlUid; ?>" rows="{{ data.rows }}" class="gmt-input-style gmt-code-editor" data-setting="{{ data.name }}"></textarea>
            </div>
        </div>
        <# if ( data.description ) { #>
            <div class="gmt-control-field-description">{{{ data.description }}}</div>
        <# } #>
        <?php
    }
}
