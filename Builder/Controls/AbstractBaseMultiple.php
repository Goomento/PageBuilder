<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

abstract class AbstractBaseMultiple extends AbstractControlData
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
    public static function getDefaultValue()
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
            AbstractBaseMultiple::getDefaultValue(),
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
     * @param string $cssProperty  CSS property.
     * @param array $controlValue Control value.
     * @param array  $controlData Control Data.
     *
     * @return array Control style value.
     */
    public function getStyleValue($cssProperty, $controlValue, array $controlData)
    {
        return $controlValue[ strtolower($cssProperty) ];
    }
}
