<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 * @noinspection ALL
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Builder\Conditions;
use Goomento\PageBuilder\Builder\Controls\AbstractControlData;
use Goomento\PageBuilder\Builder\Controls\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

abstract class ControlsStack extends AbstractBase
{
    /**
     * Shared type
     */
    const TYPE = 'stack';

    /**
     * Shared name
     */
    const NAME = 'controls_stack';

    /**
     * Responsive 'desktop' device name.
     */
    const RESPONSIVE_DESKTOP = 'desktop';

    /**
     * Responsive 'tablet' device name.
     */
    const RESPONSIVE_TABLET = 'tablet';

    /**
     * Responsive 'mobile' device name.
     */
    const RESPONSIVE_MOBILE = 'mobile';

    /**
     * Generic ID.
     *
     * Holds the unique ID.
     *
     *
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $activeSettings;

    /**
     * @var array
     */
    private $parsedActiveSettings;

    /**
     * Parsed Dynamic Settings.
     *
     *
     * @var null|array
     */
    private $parsedDynamicSettings;

    /**
     * Raw Data.
     *
     * Holds all the raw data including the element type, the child elements,
     * the user data.
     *
     *
     * @var null|array
     */
    private $data;

    /**
     * The configuration.
     *
     * Holds the configuration used to generate the Goomento editor. It includes
     * the element name, icon, categories, etc.
     *
     *
     * @var null|array
     */
    private $config;

    /**
     * Current section.
     *
     * Holds the current section while inserting a set of controls sections.
     *
     *
     * @var null|array
     */
    private $currentSection;

    /**
     * Current tab.
     *
     * Holds the current tab while inserting a set of controls tabs.
     *
     *
     * @var null|array
     */
    private $currentTab;

    /**
     * Current popover.
     *
     * Holds the current popover while inserting a set of controls.
     *
     *
     * @var null|array
     */
    private $currentPopover;

    /**
     * Injection point.
     *
     * Holds the injection point in the stack where the control will be inserted.
     *
     *
     * @var null|array
     */
    private $injectionPoint;

    /**
     * Data sanitized.
     *
     *
     * @var bool
     */
    private $settingsSanitized = false;

    /**
     * Stores the dynamic keypath
     *
     * @var array
     */
    private $dynamicKeys = [];

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return implode('_', [$this->getType(), $this->getName()]);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float|int
     */
    public function getIdInt()
    {
        return hexdec($this->id);
    }


    /**
     * @return array|null
     */
    public function getCurrentSection()
    {
        return $this->currentSection;
    }

    /**
     * @return array|null
     */
    public function getCurrentTab()
    {
        return $this->currentTab;
    }

    /**
     * Get controls.
     *
     * Retrieve all the controls or, when requested, a specific control.
     *
     *
     * @param string $controlId The ID of the requested control. Optional field,
     *                           when set it will return a specific control.
     *                           Default is null.
     *
     * @return mixed Controls list.
     */
    public function getControls($controlId = null)
    {
        return self::getItems($this->getStack()['controls'], $controlId);
    }

    /**
     * Get active controls.
     *
     * Retrieve an array of active controls that meet the condition field.
     *
     * If specific controls was given as a parameter, retrieve active controls
     * from that list, otherwise check for all the controls available.
     *
     *
     * @param array|null $controls Optional. An array of controls. Default is null.
     * @param array|null $settings Optional. Controls settings. Default is null.
     *
     * @return array Active controls.
     */
    public function getActiveControls(array $controls = null, array $settings = null)
    {
        if (!$controls) {
            $controls = $this->getControls();
        }

        if (!$settings) {
            $settings = $this->getControlsSettings();
        }

        return array_reduce(
            array_keys($controls),
            function ($activeControls, $controlKey) use ($controls, $settings) {
                $control = $controls[ $controlKey ];

                if ($this->isControlVisible($control, $settings)) {
                    $activeControls[ $controlKey ] = $control;
                }

                return $activeControls;
            },
            []
        );
    }

    /**
     * Get controls settings.
     *
     * Retrieve the settings for all the controls that represent them.
     *
     *
     * @return array Controls settings.
     */
    public function getControlsSettings()
    {
        return array_intersect_key($this->getSettings(), $this->getControls());
    }

    /**
     * Register a control to allow the user to set/update data.
     *
     * @param string $id Repeater control ID.
     * @param array $args Repeater control arguments.
     * @param array $options Optional. Repeater control options. Default is an
     *                        empty array.
     *
     * @return bool True if repeater control added, False otherwise.
     *
     */
    public function addControl(string $id, array $args, array $options = [])
    {
        $defaultOptions = [
            'overwrite' => false,
            'position' => null,
        ];

        $options = array_merge($defaultOptions, $options);

        if ($options['position']) {
            $this->startInjection($options['position']);
        }

        if ($this->injectionPoint) {
            $options['index'] = $this->injectionPoint['index']++;
        }

        if (empty($args['type']) || $args['type'] !== Controls::SECTION) {
            $targetSectionArgs = $this->currentSection;

            $targetTab = $this->currentTab;

            if ($this->injectionPoint) {
                $targetSectionArgs = $this->injectionPoint['section'];

                if (!empty($this->injectionPoint['tab'])) {
                    $targetTab = $this->injectionPoint['tab'];
                }
            }

            if (null !== $targetSectionArgs) {
                if (!empty($args['section']) || ! empty($args['tab'])) {
                    throw new BuilderException(
                        sprintf('Cannot redeclare control with `tab` or `section` args inside section "%s".', $id)
                    );
                }

                $args = array_replace_recursive($targetSectionArgs, $args);

                if (null !== $targetTab) {
                    $args = array_replace_recursive($targetTab, $args);
                }
            } elseif (empty($args['section']) && (! $options['overwrite'] || !ObjectManagerHelper::getControlsManager()
                        ->getControlFromStack($this->getUniqueName(), $id))) {
                throw new BuilderException(
                    'Cannot add a control outside of a section'
                );
            }
        }

        if ($options['position']) {
            $this->endInjection();
        }

        unset($options['position']);

        if ($this->currentPopover && ! $this->currentPopover['initialized']) {
            $args['popover'] = [
                'start' => true,
            ];

            $this->currentPopover['initialized'] = true;
        }

        return ObjectManagerHelper::getControlsManager()->addControlToStack($this, $id, $args, $options);
    }

    /**
     * Remove control from stack.
     *
     * Unregister an existing control and remove it from the stack.
     *
     * @param string|array $controlId Control ID.
     *
     * @return bool
     *
     */
    public function removeControl($controlId)
    {
        return ObjectManagerHelper::getControlsManager()
            ->removeControlFromStack($this->getUniqueName(), $controlId);
    }

    /**
     * Update control in stack.
     *
     * Change the value of an existing control in the stack. When you add new
     * control you set the `$args` parameter, this method allows you to update
     * the arguments by passing new data.
     *
     * @param string $controlId Control ID.
     * @param array $args Control arguments. Only the new fields you want
     *                           to update.
     * @param array $options Optional. Some additional options. Default is
     *                           an empty array.
     *
     * @return bool
     *
     */
    public function updateControl($controlId, array $args, array $options = [])
    {
        $isUpdated = ObjectManagerHelper::getControlsManager()->updateControlInStack($this, $controlId, $args, $options);

        if (!$isUpdated) {
            return false;
        }

        $control = $this->getControls($controlId);

        if (Controls::SECTION === $control['type']) {
            $sectionArgs = $this->getSectionArgs($controlId);

            $sectionControls = $this->getSectionControls($controlId);

            foreach ($sectionControls as $sectionControlId => $sectionControl) {
                $this->updateControl($sectionControlId, $sectionArgs, $options);
            }
        }

        return true;
    }

    /**
     * Get stack.
     *
     * Retrieve the stack of controls.
     *
     *
     * @return array Stack of controls.
     */
    public function getStack()
    {
        $stack = ObjectManagerHelper::getControlsManager()->getElementStack($this);

        if (null === $stack) {
            $this->initControls();
            $stack = ObjectManagerHelper::getControlsManager()->getElementStack($this);
        }

        return $stack;
    }

    /**
     * Get position information.
     *
     * Retrieve the position while injecting data, based on the element type.
     *
     *
     * @param array $position {
     *     The injection position.
     *
     * @type string $type Injection type, either `control` or `section`.
     *                            Default is `control`.
     * @type string $at Where to inject. If `$type` is `control` accepts
     *                            `before` and `after`. If `$type` is `section`
     *                            accepts `start` and `end`. Default values based on
     *                            the `type`.
     * @type string $of Control/Section ID.
     * @type array $fallback Fallback injection position. When the position is
     *                            not found it will try to fetch the fallback
     *                            position.
     * }
     *
     * @return bool|array Position info.
     * @throws Exception
     */
    public function getPositionInfo(array $position)
    {
        $defaultPosition = [
            'type' => 'control',
            'at' => 'after',
        ];

        if (!empty($position['type']) && 'section' === $position['type']) {
            $defaultPosition['at'] = 'end';
        }

        $position = array_merge($defaultPosition, $position);

        if ('control' === $position['type'] && in_array($position['at'], [ 'start', 'end' ], true) ||
            'section' === $position['type'] && in_array($position['at'], [ 'before', 'after' ], true)
        ) {
            throw new BuilderException(
                'Invalid position arguments. Use `before` / `after` for control or `start` / `end` for section.'
            );
        }

        $targetControlIndex = $this->getControlIndex($position['of']);

        if (false === $targetControlIndex) {
            if (!empty($position['fallback'])) {
                return $this->getPositionInfo($position['fallback']);
            }

            return false;
        }

        $targetSectionIndex = $targetControlIndex;

        $registeredControls = $this->getControls();

        $controlsKeys = array_keys($registeredControls);

        while (Controls::SECTION !== $registeredControls[ $controlsKeys[ $targetSectionIndex ] ]['type']) {
            $targetSectionIndex--;
        }

        if ('section' === $position['type']) {
            $targetControlIndex++;

            if ('end' === $position['at']) {
                while (Controls::SECTION !== $registeredControls[ $controlsKeys[ $targetControlIndex ] ]['type']) {
                    if (++$targetControlIndex >= count($registeredControls)) {
                        break;
                    }
                }
            }
        }

        $targetControl = $registeredControls[ $controlsKeys[ $targetControlIndex ] ];

        if ('after' === $position['at']) {
            $targetControlIndex++;
        }

        $sectionId = $registeredControls[ $controlsKeys[ $targetSectionIndex ] ]['name'];

        $positionInfo = [
            'index' => $targetControlIndex,
            'section' => $this->getSectionArgs($sectionId),
        ];

        if (!empty($targetControl['tabs_wrapper'])) {
            $positionInfo['tab'] = [
                'tabs_wrapper' => $targetControl['tabs_wrapper'],
                'inner_tab' => $targetControl['inner_tab'],
            ];
        }

        return $positionInfo;
    }

    /**
     * Get control key.
     *
     * Retrieve the key of the control based on a given index of the control.
     *
     *
     * @param string $controlIndex Control index.
     *
     * @return int Control key.
     */
    public function getControlKey($controlIndex)
    {
        $registeredControls = $this->getControls();

        $controlsKeys = array_keys($registeredControls);

        return $controlsKeys[ $controlIndex ];
    }

    /**
     * Get control index.
     *
     * Retrieve the index of the control based on a given key of the control.
     *
     *
     * @param string $controlKey Control key.
     *
     * @return false|int Control index.
     */
    public function getControlIndex($controlKey)
    {
        $controls = $this->getControls();

        $controlsKeys = array_keys($controls);

        return array_search($controlKey, $controlsKeys);
    }

    /**
     * Get section controls.
     *
     * Retrieve all controls under a specific section.
     *
     *
     * @param string $sectionId Section ID.
     *
     * @return array Section controls
     */
    public function getSectionControls($sectionId)
    {
        $sectionIndex = $this->getControlIndex($sectionId);

        $sectionControls = [];

        $registeredControls = $this->getControls();

        $controlsKeys = array_keys($registeredControls);

        while (true) {
            $sectionIndex++;

            if (!isset($controlsKeys[ $sectionIndex ])) {
                break;
            }

            $controlKey = $controlsKeys[ $sectionIndex ];

            if (Controls::SECTION === $registeredControls[ $controlKey ]['type']) {
                break;
            }

            $sectionControls[ $controlKey ] = $registeredControls[ $controlKey ];
        }

        return $sectionControls;
    }

    /**
     * Add new group control to stack.
     *
     * Register a set of related controls grouped together as a single unified
     * control. For example grouping together like typography controls into a
     * single, easy-to-use control.
     *
     *
     * @param string|null $groupName Group control name.
     * @param array $args Group control arguments. Default is an empty array.
     * @param array $options Optional. Group control options. Default is an
     *                           empty array.
     * @throws BuilderException
     */
    public function addGroupControl(?string $groupName, array $args = [], array $options = [])
    {
        $group = ObjectManagerHelper::getControlsManager()->getControlGroups($groupName);
        if (!$group) {
            throw new BuilderException(
                sprintf('Group "%s" not found.', $groupName)
            );
        }

        $group->addControls($this, $args, $options);
    }

    /**
     * Get scheme controls.
     *
     * Retrieve all the controls that use schemes.
     *
     *
     * @return array Scheme controls.
     */
    public function getSchemeControls()
    {
        $enabledSchemes = Schemes::getEnabledSchemes();

        return array_filter(
            $this->getControls(),
            function ($control) use ($enabledSchemes) {
                return (! empty($control['scheme']) && in_array($control['scheme']['type'], $enabledSchemes));
            }
        );
    }

    /**
     * Get style controls.
     *
     * Retrieve style controls for all active controls or, when requested, from
     * a specific set of controls.
     *
     *
     * @param array|null $controls Optional. Controls list. Default is null.
     * @param array|null $settings Optional. Controls settings. Default is null.
     *
     * @return array Style controls.
     */
    public function getStyleControls(array $controls = null, array $settings = null)
    {
        $controls = $this->getActiveControls($controls, $settings);

        $styleControls = [];

        foreach ($controls as $controlName => $control) {
            $controlObj = ObjectManagerHelper::getControlsManager()->getControl($control['type']);

            if (!$controlObj instanceof AbstractControlData) {
                continue;
            }

            $control = array_merge($controlObj->getSettings(), $control);

            if (Controls::REPEATER === $control['type']) {
                $styleFields = [];

                foreach ($this->getSettings($controlName) as $item) {
                    $styleFields[] = $this->getStyleControls($control['fields'], $item);
                }

                $control['style_fields'] = $styleFields;
            }

            if (!empty($control['selectors']) || ! empty($control['dynamic']) || ! empty($control['style_fields'])) {
                $styleControls[ $controlName ] = $control;
            }
        }

        return $styleControls;
    }

    /**
     * Get tabs controls.
     *
     * Retrieve all the tabs assigned to the control.
     *
     *
     * @return array Tabs controls.
     */
    public function getTabsControls()
    {
        return $this->getStack()['tabs'];
    }

    /**
     * Add new responsive control to stack.
     *
     * Register a set of controls to allow editing based on user screen size.
     * This method registers three screen sizes: Desktop, Tablet and Mobile.
     *
     *
     * @param string $id Responsive control ID.
     * @param array $args Responsive control arguments.
     * @param array $options Optional. Responsive control options. Default is
     *                        an empty array.
     * @throws BuilderException
     */
    public function addResponsiveControl($id, array $args, $options = [])
    {
        $args['responsive'] = [];

        $devices = [
            self::RESPONSIVE_DESKTOP,
            self::RESPONSIVE_TABLET,
            self::RESPONSIVE_MOBILE,
        ];

        if (isset($args['devices'])) {
            $devices = array_intersect($devices, $args['devices']);

            $args['responsive']['devices'] = $devices;

            unset($args['devices']);
        }

        if (isset($args['default'])) {
            $args['desktop_default'] = $args['default'];

            unset($args['default']);
        }

        foreach ($devices as $deviceName) {
            $controlArgs = $args;

            if (isset($controlArgs['device_args'])) {
                if (!empty($controlArgs['device_args'][ $deviceName ])) {
                    $controlArgs = array_merge($controlArgs, $controlArgs['device_args'][ $deviceName ]);
                }

                unset($controlArgs['device_args']);
            }

            if (!empty($args['prefix_class'])) {
                $deviceToReplace = self::RESPONSIVE_DESKTOP === $deviceName ? '' : '-' . $deviceName;

                $controlArgs['prefix_class'] = sprintf($args['prefix_class'], $deviceToReplace);
            }

            $controlArgs['responsive']['max'] = $deviceName;

            if (isset($controlArgs['min_affected_device'])) {
                if (!empty($controlArgs['min_affected_device'][ $deviceName ])) {
                    $controlArgs['responsive']['min'] = $controlArgs['min_affected_device'][ $deviceName ];
                }

                unset($controlArgs['min_affected_device']);
            }

            if (isset($controlArgs[ $deviceName . '_default' ])) {
                $controlArgs['default'] = $controlArgs[ $deviceName . '_default' ];
            }

            unset($controlArgs['desktop_default']);
            unset($controlArgs['tablet_default']);
            unset($controlArgs['mobile_default']);

            $idSuffix = self::RESPONSIVE_DESKTOP === $deviceName ? '' : '_' . $deviceName;

            if (!empty($options['overwrite'])) {
                $this->updateControl($id . $idSuffix, $controlArgs, [
                    'recursive' => ! empty($options['recursive']),
                ]);
            } else {
                $this->addControl($id . $idSuffix, $controlArgs, $options);
            }
        }
    }

    /**
     * Update responsive control in stack.
     *
     * Change the value of an existing responsive control in the stack. When you
     * add new control you set the `$args` parameter, this method allows you to
     * update the arguments by passing new data.
     *
     *
     * @param string $id Responsive control ID.
     * @param array $args Responsive control arguments.
     * @param array $options Optional. Additional options.
     * @throws Exception
     */
    public function updateResponsiveControl($id, array $args, array $options = [])
    {
        $this->addResponsiveControl($id, $args, [
            'overwrite' => true,
            'recursive' => ! empty($options['recursive']),
        ]);
    }

    /**
     * Remove responsive control from stack.
     *
     * Unregister an existing responsive control and remove it from the stack.
     *
     *
     * @param string $id Responsive control ID.
     */
    public function removeResponsiveControl($id)
    {
        $devices = [
            self::RESPONSIVE_DESKTOP,
            self::RESPONSIVE_TABLET,
            self::RESPONSIVE_MOBILE,
        ];

        foreach ($devices as $deviceName) {
            $idSuffix = self::RESPONSIVE_DESKTOP === $deviceName ? '' : '_' . $deviceName;

            $this->removeControl($id . $idSuffix);
        }
    }

    /**
     * Get the config.
     *
     * Retrieve the config or, if non set, use the initial config.
     *
     *
     * @return array|null The config.
     */
    public function getConfig()
    {
        if (null === $this->config) {
            $this->config = $this->_getInitialConfig();
        }

        return $this->config;
    }

    /**
     * Get frontend settings keys.
     *
     * Retrieve settings keys for all frontend controls.
     *
     *
     * @return array Settings keys for each control.
     */
    public function getFrontendSettingsKeys()
    {
        $controls = [];

        foreach ($this->getControls() as $control) {
            if (!empty($control['frontend_available'])) {
                $controls[] = $control['name'];
            }
        }

        return $controls;
    }

    /**
     * Get controls pointer index.
     *
     * Retrieve pointer index where the next control should be added.
     *
     * While using injection point, it will return the injection point index.
     * Otherwise index of the last control plus one.
     *
     *
     * @return int Controls pointer index.
     */
    public function getPointerIndex()
    {
        if (null !== $this->injectionPoint) {
            return $this->injectionPoint['index'];
        }

        return count($this->getControls());
    }

    /**
     * Get the raw data.
     *
     * Retrieve all the items or, when requested, a specific item.
     *
     *
     * @param string $item Optional. The requested item. Default is null.
     *
     * @return mixed The raw data.
     */
    public function getData($item = null)
    {
        if (!$this->settingsSanitized && (! $item || 'settings' === $item)) {
            $this->data['settings'] = $this->sanitizeSettings($this->data['settings']);

            $this->settingsSanitized = true;
        }

        return self::getItems($this->data, $item);
    }

    /**
     * @param $setting
     * @return array|mixed|null
     */
    public function getParsedDynamicSettings($setting = null)
    {
        if (null === $this->parsedDynamicSettings) {
            $this->parsedDynamicSettings = $this->parseDynamicSettings($this->getSettings());
        }

        return self::getItems($this->parsedDynamicSettings, $setting);
    }

    /**
     * Get active settings.
     *
     * Retrieve the settings from all the active controls.
     *
     *
     * @param array $controls Optional. An array of controls. Default is null.
     * @param array $settings Optional. Controls settings. Default is null.
     *
     * @return array Active settings.
     */
    public function getActiveSettings($settings = null, $controls = null)
    {
        $isFirstRequest = ! $settings && ! $this->activeSettings;

        if (!$settings) {
            if ($this->activeSettings) {
                return $this->activeSettings;
            }

            $settings = $this->getControlsSettings();

            $controls = $this->getControls();
        }

        $activeSettings = [];
        foreach ($settings as $settingKey => $setting) {
            if (!isset($controls[ $settingKey ])) {
                $activeSettings[ $settingKey ] = $setting;

                continue;
            }

            $control = $controls[ $settingKey ];

            if ($this->isControlVisible($control, $settings)) {
                if (Controls::REPEATER === $control['type']) {
                    foreach ($setting as & $item) {
                        $item = $this->getActiveSettings($item, $control['fields']);
                    }
                }

                $activeSettings[ $settingKey ] = $setting;
            } else {
                $activeSettings[ $settingKey ] = null;
            }
        }

        if ($isFirstRequest) {
            $this->activeSettings = $activeSettings;
        }

        return $activeSettings;
    }

    /**
     * Get settings for display.
     *
     * Retrieve all the settings or, when requested, a specific setting for display.
     *
     * Unlike `get_settings()` method, this method retrieves only active settings
     * that passed all the conditions, rendered all the shortcodes and all the dynamic
     * tags.
     *
     *
     * @param string $settingKey Optional. The key of the requested setting.
     *                            Default is null.
     *
     * @return mixed The settings.
     */
    public function getSettingsForDisplay($settingKey = null)
    {
        if (!$this->parsedActiveSettings) {
            $this->parsedActiveSettings = $this->getActiveSettings($this->getParsedDynamicSettings(), $this->getControls());
        }

        return self::getItems($this->parsedActiveSettings, $settingKey);
    }

    /**
     * Parse dynamic settings.
     *
     * Retrieve the settings with rendered dynamic tags.
     *
     *
     * @param array $settings     Optional. The requested setting. Default is null.
     * @param array $controls     Optional. The controls array. Default is null.
     * @param array $allSettings Optional. All the settings. Default is null.
     *
     * @return array The settings with rendered dynamic tags.
     */
    public function parseDynamicSettings($settings, $controls = null, $allSettings = null)
    {
        if (null === $allSettings) {
            $allSettings = $this->getSettings();
        }

        if (null === $controls) {
            $controls = $this->getControls();
        }

        foreach ($controls as $control) {
            $controlName = $control['name'];
            $controlObj = ObjectManagerHelper::getControlsManager()->getControl($control['type']);

            if (!$controlObj instanceof AbstractControlData) {
                continue;
            }

            if (Repeater::NAME === $controlObj->getName()) {
                foreach ($settings[ $controlName ] as &$field) {
                    $field = $this->parseDynamicSettings($field, $control['fields'], $field);
                }

                continue;
            }

            if (!isset($allSettings[ Tags::DYNAMIC_SETTING_KEY ][ $controlName ])) {
                continue;
            }

            $dynamicSettings = array_merge($controlObj->getSettings('dynamic'), $control['dynamic'] ?? []);

            if (!empty($dynamicSettings['active']) && ! empty($allSettings[ Tags::DYNAMIC_SETTING_KEY ][ $controlName ])) {
                $parsedValue = $controlObj->parseTags($allSettings[ Tags::DYNAMIC_SETTING_KEY ][ $controlName ], $dynamicSettings);

                $dynamicProperty = ! empty($dynamicSettings['property']) ? $dynamicSettings['property'] : null;

                if ($dynamicProperty) {
                    $settings[ $controlName ][ $dynamicProperty ] = $parsedValue;
                } else {
                    $settings[ $controlName ] = $parsedValue;
                }
            }
        }

        return $settings;
    }

    /**
     * Get frontend settings.
     *
     * Retrieve the settings for all frontend controls.
     *
     *
     * @return array Frontend settings.
     */
    public function getFrontendSettings()
    {
        $frontendSettings = array_intersect_key($this->getActiveSettings(), array_flip($this->getFrontendSettingsKeys()));

        foreach ($frontendSettings as $key => $setting) {
            if (in_array($setting, [ null, '' ], true)) {
                unset($frontendSettings[ $key ]);
            }
        }

        return $frontendSettings;
    }

    /**
     * Filter controls settings.
     *
     * Receives controls, settings and a callback function to filter the settings by
     * and returns filtered settings.
     *
     *
     * @param callable $callback The callback function.
     * @param array    $settings Optional. Control settings. Default is an empty
     *                           array.
     * @param array    $controls Optional. Controls list. Default is an empty
     *                           array.
     *
     * @return array Filtered settings.
     */
    public function filterControlsSettings(callable $callback, array $settings = [], array $controls = [])
    {
        if (!$settings) {
            $settings = $this->getSettings();
        }

        if (!$controls) {
            $controls = $this->getControls();
        }

        return array_reduce(
            array_keys($settings),
            function ($filteredSettings, $settingKey) use ($controls, $settings, $callback) {
                if (isset($controls[ $settingKey ])) {
                    $result = $callback($settings[ $settingKey ], $controls[ $settingKey ]);

                    if (null !== $result) {
                        $filteredSettings[ $settingKey ] = $result;
                    }
                }

                return $filteredSettings;
            },
            []
        );
    }

    /**
     * Whether the control is visible or not.
     *
     * Used to determine whether the control is visible or not.
     *
     *
     * @param array $control The control.
     * @param array $values  Optional. Condition values. Default is null.
     *
     * @return bool Whether the control is visible.
     */
    public function isControlVisible($control, $values = null)
    {
        if (null === $values) {
            $values = $this->getSettings();
        }

        if (!empty($control['conditions']) && !DataHelper::check($control['conditions'], $values)) {
            return false;
        }

        if (empty($control['condition'])) {
            return true;
        }

        foreach ($control['condition'] as $conditionKey => $conditionValue) {
            $isNegativeCondition = substr($conditionKey, -1, 1) === '!';
            if ($isNegativeCondition) {
                $conditionKey = substr($conditionKey, 0, strlen($conditionKey) - 1);
            }

            $instanceValue = DataHelper::arrayGetValue($values, $conditionKey, '.');


            /**
             * If the $conditionValue is a non empty array - check if the $conditionValue contains the $instanceValue,
             * If the $instanceValue is a non empty array - check if the $instanceValue contains the $conditionValue
             * otherwise check if they are equal. ( and give the ability to check if the value is an empty array )
             */
            if (is_array($conditionValue) && ! empty($conditionValue)) {
                $isContains = in_array($instanceValue, $conditionValue, true);
            } elseif (is_array($instanceValue) && ! empty($instanceValue)) {
                $isContains = in_array($conditionValue, $instanceValue, true);
            } else {
                $isContains = $instanceValue === $conditionValue;
            }

            if ($isNegativeCondition && $isContains || ! $isNegativeCondition && ! $isContains) {
                return false;
            }
        }

        return true;
    }

    /**
     * Start controls section.
     *
     * Used to add a new section of controls. When you use this method, all the
     * registered controls from this point will be assigned to this section,
     * until you close the section using `end_controls_section()` method.
     *
     * This method should be used inside `_register_controls()`.
     *
     * @param string $sectionId Section ID.
     * @param array $args Section arguments Optional.
     * @throws BuilderException
     *
     */
    public function startControlsSection($sectionId, array $args = [])
    {
        $sectionName = $this->getName();

        /**
         * Before section start.
         *
         * Fires before Goomento section starts in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $sectionId Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/before_section_start', $this, $sectionId, $args);

        /**
         * Before section start.
         *
         * Fires before Goomento section starts in the editor panel.
         *
         * The dynamic portions of the hook name, `$sectionName` and `$sectionId`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$sectionName}/{$sectionId}/before_section_start", $this, $args);

        $args['type'] = Controls::SECTION;

        $this->addControl($sectionId, $args);

        if (null !== $this->currentSection) {
            throw new BuilderException(sprintf(
                'Goomento: You can\'t start a section before the end of the previous section "%s".',
                $this->currentSection['section']
            ));
        }

        $this->currentSection = $this->getSectionArgs($sectionId);

        if ($this->injectionPoint) {
            $this->injectionPoint['section'] = $this->currentSection;
        }

        /**
         * After section start.
         *
         * Fires after Goomento section starts in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $sectionId Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/after_section_start', $this, $sectionId, $args);

        /**
         * After section start.
         *
         * Fires after Goomento section starts in the editor panel.
         *
         * The dynamic portions of the hook name, `$sectionName` and `$sectionId`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$sectionName}/{$sectionId}/after_section_start", $this, $args);
    }

    /**
     * End controls section.
     *
     * Used to close an existing open controls section. When you use this method
     * it stops adding new controls to this section.
     *
     * This method should be used inside `_register_controls()`.
     *
     */
    public function endControlsSection()
    {
        $stackName = $this->getName();

        // Save the current section for the action.
        $currentSection = $this->currentSection;
        $sectionId = $currentSection['section'];
        $args = [
            'tab' => $currentSection['tab'],
        ];

        /**
         * Before section end.
         *
         * Fires before Goomento section ends in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $sectionId Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/before_section_end', $this, $sectionId, $args);

        /**
         * Before section end.
         *
         * Fires before Goomento section ends in the editor panel.
         *
         * The dynamic portions of the hook name, `$stackName` and `$sectionId`, refers to the stack name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$stackName}/{$sectionId}/before_section_end", $this, $args);

        $this->currentSection = null;

        /**
         * After section end.
         *
         * Fires after Goomento section ends in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $sectionId Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/after_section_end', $this, $sectionId, $args);

        /**
         * After section end.
         *
         * Fires after Goomento section ends in the editor panel.
         *
         * The dynamic portions of the hook name, `$stackName` and `$sectionId`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$stackName}/{$sectionId}/after_section_end", $this, $args);
    }

    /**
     * Start controls tabs.
     *
     * Used to add a new set of tabs inside a section. You should use this
     * method before adding new individual tabs using `start_controls_tab()`.
     * Each tab added after this point will be assigned to this group of tabs,
     * until you close it using `end_controls_tabs()` method.
     *
     * This method should be used inside `_register_controls()`.
     *
     *
     * @param string $tabsId Tabs ID.
     * @param array $args Tabs arguments.
     * @throws BuilderException
     */
    public function startControlsTabs($tabsId, array $args = [])
    {
        if (null !== $this->currentTab) {
            throw new BuilderException(
                sprintf('You can\'t start tabs before the end of the previous tabs "%s".', $this->currentTab['tabs_wrapper'])
            );
        }

        $args['type'] = Controls::TABS;

        $this->addControl($tabsId, $args);

        $this->currentTab = [
            'tabs_wrapper' => $tabsId,
        ];

        foreach ([ 'condition', 'conditions' ] as $key) {
            if (!empty($args[ $key ])) {
                $this->currentTab[ $key ] = $args[ $key ];
            }
        }

        if ($this->injectionPoint) {
            $this->injectionPoint['tab'] = $this->currentTab;
        }
    }

    /**
     * End controls tabs.
     *
     * Used to close an existing open controls tabs. When you use this method it
     * stops adding new controls to this tabs.
     *
     * This method should be used inside `_register_controls()`.
     *
     */
    public function endControlsTabs()
    {
        $this->currentTab = null;
    }

    /**
     * Start controls tab.
     *
     * Used to add a new tab inside a group of tabs. Use this method before
     * adding new individual tabs using `start_controls_tab()`.
     * Each tab added after this point will be assigned to this group of tabs,
     * until you close it using `end_controls_tab()` method.
     *
     * This method should be used inside `_register_controls()`.
     *
     *
     * @param string $tabId Tab ID.
     * @param array $args Tab arguments.
     * @throws BuilderException
     */
    public function startControlsTab($tabId, $args)
    {
        if (!empty($this->currentTab['inner_tab'])) {
            throw new BuilderException(
                sprintf('Goomento: You can\'t start a tab before the end of the previous tab "%s".', $this->currentTab['inner_tab'])
            );
        }

        $args['type'] = Controls::TAB;
        $args['tabs_wrapper'] = $this->currentTab['tabs_wrapper'];

        $this->addControl($tabId, $args);

        $this->currentTab['inner_tab'] = $tabId;

        if ($this->injectionPoint) {
            $this->injectionPoint['tab']['inner_tab'] = $this->currentTab['inner_tab'];
        }
    }

    /**
     * End controls tab.
     *
     * Used to close an existing open controls tab. When you use this method it
     * stops adding new controls to this tab.
     *
     * This method should be used inside `_register_controls()`.
     *
     */
    public function endControlsTab()
    {
        unset($this->currentTab['inner_tab']);
    }

    /**
     * Start popover.
     *
     * Used to add a new set of controls in a popover. When you use this method,
     * all the registered controls from this point will be assigned to this
     * popover, until you close the popover using `end_popover()` method.
     *
     * This method should be used inside `_register_controls()`.
     *
     */
    public function startPopover()
    {
        $this->currentPopover = [
            'initialized' => false,
        ];
    }

    /**
     * End popover.
     *
     * Used to close an existing open popover. When you use this method it stops
     * adding new controls to this popover.
     *
     * This method should be used inside `_register_controls()`.
     *
     */
    public function endPopover()
    {
        $this->currentPopover = null;

        $lastControlKey = $this->getControlKey($this->getPointerIndex() - 1);

        $args = [
            'popover' => [
                'end' => true,
            ],
        ];

        $options = [
            'recursive' => true,
        ];

        $this->updateControl($lastControlKey, $args, $options);
    }

    /**
     * Print element template.
     *
     * Used to generate the element template on the editor.
     *
     */
    public function printTemplate()
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        ob_start();

        $template = $this->contentTemplate();

        $templateContent = ob_get_clean();

        if (empty($templateContent) && $template) {
            $templateContent = $template;
        }

        $elementType = self::TYPE;

        /**
         * Template content.
         *
         * Filters the controls stack template content before it's printed in the editor.
         *
         * The dynamic portion of the hook name, `$elementType`, refers to the element type.
         *
         *
         * @param string         $contentTemplate The controls stack template in the editor.
         * @param ControlsStack $this             The controls stack.
         */
        $templateContent = HooksHelper::applyFilters("pagebuilder/{$elementType}/print_template", $templateContent, $this)->getResult();

        if (empty($templateContent)) {
            return;
        }
        ?>
        <script type="text/html" id="tmpl-gmt-<?= EscaperHelper::escapeHtmlAttr($this->getName()); ?>-content">
            <?php $this->printTemplateContent($templateContent); ?>
        </script>
        <?php
    }

    /**
     * Start injection.
     *
     * Used to inject controls and sections to a specific position in the stack.
     *
     * When you use this method, all the registered controls and sections will
     * be injected to a specific position in the stack, until you stop the
     * injection using `end_injection()` method.
     *
     *
     * @param array $position {
     *     The position where to start the injection.
     *
     */
    public function startInjection(array $position)
    {
        if ($this->injectionPoint) {
            throw new BuilderException(
                'A controls injection is already opened. Please close current injection before starting a new one (use `endInjection`).'
            );
        }

        $this->injectionPoint = $this->getPositionInfo($position);
    }

    /**
     * End injection.
     *
     * Used to close an existing opened injection point.
     *
     * When you use this method it stops adding new controls and sections to
     * this point and continue to add controls to the regular position in the
     * stack.
     *
     */
    public function endInjection()
    {
        $this->injectionPoint = null;
    }

    /**
     * Get injection point.
     *
     * Retrieve the injection point in the stack where new controls and sections
     * will be inserted.
     *
     *
     * @return array|null An array when an injection point is defined, null
     *                    otherwise.
     */
    public function getInjectionPoint()
    {
        return $this->injectionPoint;
    }

    /**
     * Register controls.
     *
     * Used to add new controls to any element type. For example, external
     * developers use this method to register controls in a widget.
     * @return void
     * @throws BuilderException
     */
    protected function registerControls()
    {
    }

    /**
     * Get default data.
     *
     * Retrieve the default data. Used to reset the data on initialization.
     *
     *
     * @return array Default data.
     */
    protected function getDefaultData()
    {
        return [
            'id' => 0,
            'settings' => [],
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getInitSettings()
    {
        $settings = $this->getData('settings');

        foreach ($this->getControls() as $control) {
            $controlObj = ObjectManagerHelper::getControlsManager()->getControl($control['type']);

            if (!$controlObj instanceof AbstractControlData) {
                continue;
            }

            $control = array_merge_recursive($controlObj->getSettings(), $control);

            $settings[ $control['name'] ] = $controlObj->getValue($control, $settings);
        }

        return $settings;
    }

    /**
     * Get initial config.
     *
     * Retrieve the current element initial configuration - controls list and
     * the tabs assigned to the control.
     *
     *
     * @return array The initial config.
     */
    protected function _getInitialConfig()
    {
        return [
            'controls' => $this->getControls(),
        ];
    }

    /**
     * Get section arguments.
     *
     * Retrieve the section arguments based on section ID.
     *
     *
     * @param string $sectionId Section ID.
     *
     * @return array Section arguments.
     */
    protected function getSectionArgs($sectionId)
    {
        $sectionControl = $this->getControls($sectionId);

        $sectionArgsKeys = [ 'tab', 'condition' ];

        $args = array_intersect_key($sectionControl, array_flip($sectionArgsKeys));

        $args['section'] = $sectionId;

        return $args;
    }

    /**
     * Render element.
     *
     * Generates the final HTML on the frontend.
     * @return string|void
     */
    protected function render()
    {
    }

    /**
     * Print content template.
     *
     * Used to generate the content template on the editor, using a
     * Backbone JavaScript template.
     *
     *
     * @param string $templateContent Template content.
     */
    protected function printTemplateContent($templateContent)
    {
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $templateContent;
    }

    /**
     * Render element output in the editor.
     *
     * Used to generate the live preview, using a Backbone JavaScript template.
     * @return string|void
     */
    protected function contentTemplate()
    {
    }

    /**
     * Initialize controls.
     *
     * Register the all controls added by `_register_controls()`.
     *
     */
    protected function initControls()
    {
        ObjectManagerHelper::getControlsManager()->openStack($this);
        $this->registerControls();
    }

    /**
     * Initialize the class.
     *
     * Set the raw data, the ID and the parsed settings.
     *
     *
     * @param array $data Initial data.
     */
    protected function _init($data)
    {
        $this->data = array_merge($this->getDefaultData(), $data);

        $this->id = $data['id'];
    }

    /**
     * Sanitize initial data.
     *
     * Performs settings cleaning and sanitization.
     *
     *
     * @param array $settings Settings to sanitize.
     * @param array $controls Optional. An array of controls. Default is an
     *                        empty array.
     *
     * @return array Sanitized settings.
     */
    private function sanitizeSettings(array $settings, array $controls = [])
    {
        if (!$controls) {
            $controls = $this->getControls();
        }

        foreach ($controls as $control) {
            if (Repeater::NAME === $control['type']) {
                if (empty($settings[ $control['name'] ])) {
                    continue;
                }

                foreach ($settings[ $control['name'] ] as $index => $repeaterRowData) {
                    $sanitizedRowData = $this->sanitizeSettings($repeaterRowData, $control['fields']);

                    $settings[ $control['name'] ][ $index ] = $sanitizedRowData;
                }

                continue;
            }

            $isDynamic = isset($settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ]);

            if (!$isDynamic) {
                continue;
            }

            $valueToCheck = $settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ];

            $tagTextData = ObjectManagerHelper::getTagsManager()->tagTextToTagData($valueToCheck);

            if (!ObjectManagerHelper::getTagsManager()->getTag($tagTextData['name'])) {
                unset($settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ]);
            }
        }

        return $settings;
    }

    /**
     * Controls stack constructor.
     *
     * Initializing the control stack class using `$data`. The `$data` is required
     * for a normal instance. It is optional only for internal `type instance`.
     *
     *
     * @param array $data Optional. Control stack data. Default is an empty array.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->_init($data);
        }
    }
}
