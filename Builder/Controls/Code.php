<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class Code
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Code extends BaseData
{

    /**
     * Get code control type.
     *
     * Retrieve the control type, in this case `code`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'code';
    }

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
        $control_uid = $this->getControlUid(); ?>
		<div class="gmt-control-field">
			<label for="<?= $control_uid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
			<div class="gmt-control-input-wrapper">
				<textarea id="<?= $control_uid; ?>" rows="{{ data.rows }}" class="gmt-input-style gmt-code-editor" data-setting="{{ data.name }}"></textarea>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }
}
