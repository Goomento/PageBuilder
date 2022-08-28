<?php
namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Base\AbstractControl;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

abstract class AbstractControlData extends AbstractControl
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
    public static function getDefaultValue()
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
        $defaultSettings = parent::getDefaultSettings();

        $defaultSettings['dynamic'] = false;

        return $defaultSettings;
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
        if (!isset($control['default'])) {
            $control['default'] = AbstractControlData::getDefaultValue();
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
     * @param string $dynamicValue    The dynamic tag text.
     * @param array  $dynamicSettings The dynamic tag settings.
     *
     * @return string|string[]|mixed A string or an array of strings with the
     *                               return value from each tag callback function.
     */
    public function parseTags($dynamicValue, $dynamicSettings)
    {
        $currentDynamicSettings = $this->getSettings('dynamic');

        if (is_array($currentDynamicSettings)) {
            $dynamicSettings = array_merge($currentDynamicSettings, $dynamicSettings);
        }

        $tagsManager = ObjectManagerHelper::getTagsManager();

        return ObjectManagerHelper::getTagsManager()->parseTagsText($dynamicValue, $dynamicSettings, [ $tagsManager, 'getTagDataContent' ]);
    }

    /**
     * Get data control style value.
     *
     * Retrieve the style of the control. Used when adding CSS rules to the control
     * while extracting CSS from the `selectors` data argument.
     *
     *
     * @param string $cssProperty  CSS property.
     * @param string $controlValue Control value.
     * @param array  $controlData Control Data.
     *
     * @return string Control style value.
     */
    public function getStyleValue($cssProperty, $controlValue, array $controlData)
    {
        if ('DEFAULT' === $cssProperty) {
            return $controlData['default'];
        }

        return $controlValue;
    }

    /**
     * Get data control unique ID.
     *
     * Retrieve the unique ID of the control. Used to set a uniq CSS ID for the
     * element.
     *
     *
     * @param string $inputType Input type. Default is 'default'.
     *
     * @return string Unique ID.
     */
    protected function getControlUid($inputType = 'default')
    {
        return 'gmt-control-' . $inputType . '-{{{ data._cid }}}';
    }
}
