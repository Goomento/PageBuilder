<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Repeater
 * @package Goomento\PageBuilder\Builder\Controls
 */
class Repeater extends BaseData
{

    /**
     * Get repeater control type.
     *
     * Retrieve the control type, in this case `repeater`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'repeater';
    }

    /**
     * Get repeater control default value.
     *
     * Retrieve the default value of the data control. Used to return the default
     * values while initializing the repeater control.
     *
     *
     * @return array Control default value.
     */
    public function getDefaultValue()
    {
        return [];
    }

    /**
     * Get repeater control default settings.
     *
     * Retrieve the default settings of the repeater control. Used to return the
     * default settings while initializing the repeater control.
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        return [
            'fields' => [],
            'title_field' => '',
            'prevent_empty' => true,
            'is_repeater' => true,
            'item_actions' => [
                'add' => true,
                'duplicate' => true,
                'remove' => true,
                'sort' => true,
            ],
        ];
    }

    /**
     * Get repeater control value.
     *
     * Retrieve the value of the repeater control from a specific Controls_Stack.
     *
     *
     * @param array $control  Control
     * @param array $settings Controls_Stack settings
     *
     * @return mixed Control values.
     */
    public function getValue($control, $settings)
    {
        $value = parent::getValue($control, $settings);

        if (! empty($value)) {
            foreach ($value as &$item) {
                foreach ($control['fields'] as $field) {
                    $control_obj = StaticObjectManager::get(Controls::class)->getControl($field['type']);

                    // Prior to 1.5.0 the fields may contains non-data controls.
                    if (! $control_obj instanceof \Goomento\PageBuilder\Builder\Controls\BaseData) {
                        continue;
                    }

                    $item[ $field['name'] ] = $control_obj->getValue($field, $item);
                }
            }
        }

        return $value;
    }

    /**
     * Import repeater.
     *
     * Used as a wrapper method for inner controls while importing SagoTheme
     * template JSON file, and replacing the old data.
     *
     *
     * @param array $settings     Control settings.
     * @param array $control_data Optional. Control data. Default is an empty array.
     *
     * @return array Control settings.
     */
    public function onImport($settings, $control_data = [])
    {
        if (empty($settings) || empty($control_data['fields'])) {
            return $settings;
        }

        $method = 'onImport';

        foreach ($settings as &$item) {
            foreach ($control_data['fields'] as $field) {
                if (empty($field['name']) || empty($item[ $field['name'] ])) {
                    continue;
                }
                /** @var Controls $controlManager */
                $controlManager = StaticObjectManager::get(Controls::class);
                $control_obj = $controlManager->getControl($field['type']);

                if (! $control_obj) {
                    continue;
                }

                if (method_exists($control_obj, $method)) {
                    $item[ $field['name'] ] = $control_obj->{$method}($item[ $field['name'] ], $field);
                }
            }
        }

        return $settings;
    }

    /**
     * Render repeater control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     */
    public function contentTemplate()
    {
        ?>
		<label>
			<span class="gmt-control-title">{{{ data.label }}}</span>
		</label>
		<ol class="gmt-repeater-fields-wrapper"></ol>
		<# if ( itemActions.add ) { #>
			<div class="gmt-button-wrapper">
				<button class="gmt-button gmt-button-default gmt-repeater-add" type="button">
					<i class="fas fa-plus" aria-hidden="true"></i><?= __('Add Item'); ?>
				</button>
			</div>
		<# } #>
		<?php
    }
}
