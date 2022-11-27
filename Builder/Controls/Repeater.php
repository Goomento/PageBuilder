<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Base\ImportInterface;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Repeater extends AbstractControlData implements ImportInterface
{
    const NAME = 'repeater';

    /**
     * Get repeater control default value.
     *
     * Retrieve the default value of the data control. Used to return the default
     * values while initializing the repeater control.
     *
     *
     * @return array Control default value.
     */
    public static function getDefaultValue()
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

        if (!empty($value)) {
            foreach ($value as &$item) {
                foreach ($control['fields'] as $field) {
                    $controlObj = ObjectManagerHelper::get(Controls::class)->getControl($field['type']);

                    // Prior to 1.5.0 the fields may contains non-data controls.
                    if (!$controlObj instanceof \Goomento\PageBuilder\Builder\Controls\AbstractControlData) {
                        continue;
                    }

                    $item[ $field['name'] ] = $controlObj->getValue($field, $item);
                }
            }
        }

        return $value;
    }

    /**
     * Import repeater.
     *
     * Used as a wrapper method for inner controls while importing Goomento
     * template JSON file, and replacing the old data.
     *
     *
     * @param array $data     Control settings.
     * @param array $extraData Optional. Control data. Default is an empty array.
     *
     * @return array Control settings.
     */
    public function onImport($data, $extraData = [])
    {
        if (empty($data) || empty($extraData['fields'])) {
            return $data;
        }

        $method = 'onImport';

        foreach ($data as &$item) {
            foreach ($extraData['fields'] as $field) {
                if (empty($field['name']) || empty($item[ $field['name'] ])) {
                    continue;
                }
                /** @var Controls $controlManager */
                $controlManager = ObjectManagerHelper::get(Controls::class);
                $controlObj = $controlManager->getControl($field['type']);

                if (!$controlObj) {
                    continue;
                }

                if (method_exists($controlObj, $method)) {
                    $item[ $field['name'] ] = $controlObj->{$method}($item[ $field['name'] ], $field);
                }
            }
        }

        return $data;
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
