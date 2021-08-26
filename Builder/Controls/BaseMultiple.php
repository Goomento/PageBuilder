<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class BaseMultiple
 * @package Goomento\PageBuilder\Builder\Controls
 */
abstract class BaseMultiple extends BaseData
{

    /**
     * Get multiple control default value.
     *
     * Retrieve the default value of the multiple control. Used to return the default
     * values while initializing the multiple control.
     *
     *
     * @return array Control default value.
     */
    public function getDefaultValue()
    {
        return [];
    }

    /**
     * Get multiple control value.
     *
     * Retrieve the value of the multiple control from a specific Controls_Stack settings.
     *
     *
     * @param array $control  Control
     * @param array $settings Settings
     *
     * @return mixed Control values.
     */
    public function getValue($control, $settings)
    {
        $value = parent::getValue($control, $settings);

        if (empty($control['default'])) {
            $control['default'] = [];
        }

        if (! is_array($value)) {
            $value = [];
        }

        $control['default'] = array_merge(
            $this->getDefaultValue(),
            $control['default']
        );

        return array_merge(
            $control['default'],
            $value
        );
    }

    /**
     * Get multiple control style value.
     *
     * Retrieve the style of the control. Used when adding CSS rules to the control
     * while extracting CSS from the `selectors` data argument.
     *
     *
     * @param string $css_property  CSS property.
     * @param array $control_value Control value.
     * @param array  $control_data Control Data.
     *
     * @return array Control style value.
     */
    public function getStyleValue($css_property, $control_value, array $control_data)
    {
        return $control_value[ strtolower($css_property) ];
    }
}
