<?php
namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Core\DynamicTags\Manager;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class BaseData
 * @package Goomento\PageBuilder\Builder\Controls
 */
abstract class BaseData extends Base
{

    /**
     * Get data control default value.
     *
     * Retrieve the default value of the data control. Used to return the default
     * values while initializing the data control.
     *
     *
     * @return string Control default value.
     */
    public function getDefaultValue()
    {
        return '';
    }

    /**
     * Retrieve default control settings.
     *
     * Get the default settings of the control. Used to return the default
     * settings while initializing the control.
     *
     *
     * @return array Control default settings.
     */
    protected function getDefaultSettings()
    {
        $default_settings = parent::getDefaultSettings();

        $default_settings['dynamic'] = false;

        return $default_settings;
    }

    /**
     * Get data control value.
     *
     * Retrieve the value of the data control from a specific Controls_Stack settings.
     *
     *
     * @param array $control  Control
     * @param array $settings Element settings
     *
     * @return mixed Control values.
     */
    public function getValue($control, $settings)
    {
        if (! isset($control['default'])) {
            $control['default'] = $this->getDefaultValue();
        }

        if (isset($settings[ $control['name'] ])) {
            $value = $settings[ $control['name'] ];
        } else {
            $value = $control['default'];
        }

        return $value;
    }

    /**
     * Parse dynamic tags.
     *
     * Iterates through all the controls and renders all the dynamic tags.
     *
     *
     * @param string $dynamic_value    The dynamic tag text.
     * @param array  $dynamic_settings The dynamic tag settings.
     *
     * @return string|string[]|mixed A string or an array of strings with the
     *                               return value from each tag callback function.
     */
    public function parseTags($dynamic_value, $dynamic_settings)
    {
        $current_dynamic_settings = $this->getSettings('dynamic');

        if (is_array($current_dynamic_settings)) {
            $dynamic_settings = array_merge($current_dynamic_settings, $dynamic_settings);
        }

        /** @var Manager $tagsManager */
        $tagsManager = StaticObjectManager::get(Manager::class);

        return $tagsManager->parseTagsText($dynamic_value, $dynamic_settings, [ $tagsManager, 'getTagDataContent' ]);
    }

    /**
     * Get data control style value.
     *
     * Retrieve the style of the control. Used when adding CSS rules to the control
     * while extracting CSS from the `selectors` data argument.
     *
     *
     * @param string $css_property  CSS property.
     * @param string $control_value Control value.
     * @param array  $control_data Control Data.
     *
     * @return string Control style value.
     */
    public function getStyleValue($css_property, $control_value, array $control_data)
    {
        if ('DEFAULT' === $css_property) {
            return $control_data['default'];
        }

        return $control_value;
    }

    /**
     * Get data control unique ID.
     *
     * Retrieve the unique ID of the control. Used to set a uniq CSS ID for the
     * element.
     *
     *
     * @param string $input_type Input type. Default is 'default'.
     *
     * @return string Unique ID.
     */
    protected function getControlUid($input_type = 'default')
    {
        return 'gmt-control-' . $input_type . '-{{{ data._cid }}}';
    }
}
