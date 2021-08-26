<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Core\DynamicTags\Module as TagsModule;

/**
 * Class Number
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Number extends BaseData
{

    /**
     * Get number control type.
     *
     * Retrieve the control type, in this case `number`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'number';
    }

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
                'categories' => [ TagsModule::NUMBER_CATEGORY ],
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
        $control_uid = $this->getControlUid(); ?>
		<div class="gmt-control-field">
			<label for="<?= $control_uid; ?>" class="gmt-control-title">{{{ data.label }}}</label>
			<div class="gmt-control-input-wrapper">
				<input id="<?= $control_uid; ?>" type="number" min="{{ data.min }}" max="{{ data.max }}" step="{{ data.step }}" class="tooltip-target gmt-control-tag-area" data-tooltip="{{ data.title }}" title="{{ data.title }}" data-setting="{{ data.name }}" placeholder="{{ data.placeholder }}" />
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="gmt-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
    }
}
