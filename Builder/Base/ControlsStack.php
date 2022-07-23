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
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Tags;
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

    private $activeSettings;

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
     * Holds the configuration used to generate the SagoTheme editor. It includes
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
     * @var Controls
     */
    private $controlManager;

    /**
     * @var Tags
     */
    private $tagsManager;

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->getName();
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
     * @param string $control_id The ID of the requested control. Optional field,
     *                           when set it will return a specific control.
     *                           Default is null.
     *
     * @return mixed Controls list.
     */
    public function getControls($control_id = null)
    {
        return self::getItems($this->getStack()['controls'], $control_id);
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
            function ($active_controls, $control_key) use ($controls, $settings) {
                $control = $controls[ $control_key ];

                if ($this->isControlVisible($control, $settings)) {
                    $active_controls[ $control_key ] = $control;
                }

                return $active_controls;
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
        $default_options = [
            'overwrite' => false,
            'position' => null,
        ];

        $options = array_merge($default_options, $options);

        if ($options['position']) {
            $this->startInjection($options['position']);
        }

        if ($this->injectionPoint) {
            $options['index'] = $this->injectionPoint['index']++;
        }

        if (empty($args['type']) || $args['type'] !== Controls::SECTION) {
            $target_section_args = $this->currentSection;

            $target_tab = $this->currentTab;

            if ($this->injectionPoint) {
                $target_section_args = $this->injectionPoint['section'];

                if (!empty($this->injectionPoint['tab'])) {
                    $target_tab = $this->injectionPoint['tab'];
                }
            }
            /** @var Controls  $controlsManager */
            $controlsManager = ObjectManagerHelper::get(Controls::class);
            if (null !== $target_section_args) {
                if (!empty($args['section']) || ! empty($args['tab'])) {
                    throw new Exception(
                        sprintf('Cannot redeclare control with `tab` or `section` args inside section "%s".', $id)
                    );
                }

                $args = array_replace_recursive($target_section_args, $args);

                if (null !== $target_tab) {
                    $args = array_replace_recursive($target_tab, $args);
                }
            } elseif (empty($args['section']) && (! $options['overwrite'] || !$controlsManager->getControlFromStack($this->getUniqueName(), $id))) {
                throw new Exception(
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

        return $this->controlManager->addControlToStack($this, $id, $args, $options);
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
        return $this->controlManager->removeControlFromStack($this->getUniqueName(), $controlId);
    }

    /**
     * Update control in stack.
     *
     * Change the value of an existing control in the stack. When you add new
     * control you set the `$args` parameter, this method allows you to update
     * the arguments by passing new data.
     *
     * @param string $control_id Control ID.
     * @param array $args Control arguments. Only the new fields you want
     *                           to update.
     * @param array $options Optional. Some additional options. Default is
     *                           an empty array.
     *
     * @return bool
     *
     */
    public function updateControl($control_id, array $args, array $options = [])
    {
        $is_updated = $this->controlManager->updateControlInStack($this, $control_id, $args, $options);

        if (!$is_updated) {
            return false;
        }

        $control = $this->getControls($control_id);

        if (Controls::SECTION === $control['type']) {
            $section_args = $this->getSectionArgs($control_id);

            $section_controls = $this->getSectionControls($control_id);

            foreach ($section_controls as $section_control_id => $section_control) {
                $this->updateControl($section_control_id, $section_args, $options);
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
        $stack = $this->controlManager->getElementStack($this);

        if (null === $stack) {
            $this->initControls();
            return $this->controlManager->getElementStack($this);
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
    final public function getPositionInfo(array $position)
    {
        $default_position = [
            'type' => 'control',
            'at' => 'after',
        ];

        if (!empty($position['type']) && 'section' === $position['type']) {
            $default_position['at'] = 'end';
        }

        $position = array_merge($default_position, $position);

        if (
            'control' === $position['type'] && in_array($position['at'], [ 'start', 'end' ], true) ||
            'section' === $position['type'] && in_array($position['at'], [ 'before', 'after' ], true)
        ) {
            throw new Exception(
                'Invalid position arguments. Use `before` / `after` for control or `start` / `end` for section.'
            );
        }

        $target_control_index = $this->getControlIndex($position['of']);

        if (false === $target_control_index) {
            if (!empty($position['fallback'])) {
                return $this->getPositionInfo($position['fallback']);
            }

            return false;
        }

        $target_section_index = $target_control_index;

        $registered_controls = $this->getControls();

        $controls_keys = array_keys($registered_controls);

        while (Controls::SECTION !== $registered_controls[ $controls_keys[ $target_section_index ] ]['type']) {
            $target_section_index--;
        }

        if ('section' === $position['type']) {
            $target_control_index++;

            if ('end' === $position['at']) {
                while (Controls::SECTION !== $registered_controls[ $controls_keys[ $target_control_index ] ]['type']) {
                    if (++$target_control_index >= count($registered_controls)) {
                        break;
                    }
                }
            }
        }

        $target_control = $registered_controls[ $controls_keys[ $target_control_index ] ];

        if ('after' === $position['at']) {
            $target_control_index++;
        }

        $section_id = $registered_controls[ $controls_keys[ $target_section_index ] ]['name'];

        $position_info = [
            'index' => $target_control_index,
            'section' => $this->getSectionArgs($section_id),
        ];

        if (!empty($target_control['tabs_wrapper'])) {
            $position_info['tab'] = [
                'tabs_wrapper' => $target_control['tabs_wrapper'],
                'inner_tab' => $target_control['inner_tab'],
            ];
        }

        return $position_info;
    }

    /**
     * Get control key.
     *
     * Retrieve the key of the control based on a given index of the control.
     *
     *
     * @param string $control_index Control index.
     *
     * @return int Control key.
     */
    final public function getControlKey($control_index)
    {
        $registered_controls = $this->getControls();

        $controls_keys = array_keys($registered_controls);

        return $controls_keys[ $control_index ];
    }

    /**
     * Get control index.
     *
     * Retrieve the index of the control based on a given key of the control.
     *
     *
     * @param string $control_key Control key.
     *
     * @return false|int Control index.
     */
    final public function getControlIndex($control_key)
    {
        $controls = $this->getControls();

        $controls_keys = array_keys($controls);

        return array_search($control_key, $controls_keys);
    }

    /**
     * Get section controls.
     *
     * Retrieve all controls under a specific section.
     *
     *
     * @param string $section_id Section ID.
     *
     * @return array Section controls
     */
    final public function getSectionControls($section_id)
    {
        $section_index = $this->getControlIndex($section_id);

        $section_controls = [];

        $registered_controls = $this->getControls();

        $controls_keys = array_keys($registered_controls);

        while (true) {
            $section_index++;

            if (!isset($controls_keys[ $section_index ])) {
                break;
            }

            $control_key = $controls_keys[ $section_index ];

            if (Controls::SECTION === $registered_controls[ $control_key ]['type']) {
                break;
            }

            $section_controls[ $control_key ] = $registered_controls[ $control_key ];
        }

        return $section_controls;
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
     * @throws Exception
     */
    final public function addGroupControl(?string $groupName, array $args = [], array $options = [])
    {
        $group = $this->controlManager->getControlGroups($groupName);
        if (!$group) {
            throw new Exception(
                sprintf('Group "%s" not found.',  $groupName)
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
    final public function getSchemeControls()
    {
        $enabled_schemes = Schemes::getEnabledSchemes();

        return array_filter(
            $this->getControls(),
            function ($control) use ($enabled_schemes) {
                return (! empty($control['scheme']) && in_array($control['scheme']['type'], $enabled_schemes));
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
    final public function getStyleControls(array $controls = null, array $settings = null)
    {
        $controls = $this->getActiveControls($controls, $settings);

        $styleControls = [];

        foreach ($controls as $controlName => $control) {
            $controlObj = $this->controlManager->getControl($control['type']);

            if (!$controlObj instanceof AbstractControlData) {
                continue;
            }

            $control = array_merge($controlObj->getSettings(), $control);

            if (Controls::REPEATER === $control['type']) {
                $style_fields = [];

                foreach ($this->getSettings($controlName) as $item) {
                    $style_fields[] = $this->getStyleControls($control['fields'], $item);
                }

                $control['style_fields'] = $style_fields;
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
    final public function getTabsControls()
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
     * @throws Exception
     */
    final public function addResponsiveControl($id, array $args, $options = [])
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

        foreach ($devices as $device_name) {
            $control_args = $args;

            if (isset($control_args['device_args'])) {
                if (!empty($control_args['device_args'][ $device_name ])) {
                    $control_args = array_merge($control_args, $control_args['device_args'][ $device_name ]);
                }

                unset($control_args['device_args']);
            }

            if (!empty($args['prefix_class'])) {
                $device_to_replace = self::RESPONSIVE_DESKTOP === $device_name ? '' : '-' . $device_name;

                $control_args['prefix_class'] = sprintf($args['prefix_class'], $device_to_replace);
            }

            $control_args['responsive']['max'] = $device_name;

            if (isset($control_args['min_affected_device'])) {
                if (!empty($control_args['min_affected_device'][ $device_name ])) {
                    $control_args['responsive']['min'] = $control_args['min_affected_device'][ $device_name ];
                }

                unset($control_args['min_affected_device']);
            }

            if (isset($control_args[ $device_name . '_default' ])) {
                $control_args['default'] = $control_args[ $device_name . '_default' ];
            }

            unset($control_args['desktop_default']);
            unset($control_args['tablet_default']);
            unset($control_args['mobile_default']);

            $id_suffix = self::RESPONSIVE_DESKTOP === $device_name ? '' : '_' . $device_name;

            if (!empty($options['overwrite'])) {
                $this->updateControl($id . $id_suffix, $control_args, [
                    'recursive' => ! empty($options['recursive']),
                ]);
            } else {
                $this->addControl($id . $id_suffix, $control_args, $options);
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
    final public function updateResponsiveControl($id, array $args, array $options = [])
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
    final public function removeResponsiveControl($id)
    {
        $devices = [
            self::RESPONSIVE_DESKTOP,
            self::RESPONSIVE_TABLET,
            self::RESPONSIVE_MOBILE,
        ];

        foreach ($devices as $device_name) {
            $id_suffix = self::RESPONSIVE_DESKTOP === $device_name ? '' : '_' . $device_name;

            $this->removeControl($id . $id_suffix);
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
    final public function getConfig()
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
    final public function getFrontendSettingsKeys()
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
        $is_first_request = ! $settings && ! $this->activeSettings;

        if (!$settings) {
            if ($this->activeSettings) {
                return $this->activeSettings;
            }

            $settings = $this->getControlsSettings();

            $controls = $this->getControls();
        }

        $active_settings = [];
        foreach ($settings as $setting_key => $setting) {
            if (!isset($controls[ $setting_key ])) {
                $active_settings[ $setting_key ] = $setting;

                continue;
            }

            $control = $controls[ $setting_key ];

            if ($this->isControlVisible($control, $settings)) {
                if (Controls::REPEATER === $control['type']) {
                    foreach ($setting as & $item) {
                        $item = $this->getActiveSettings($item, $control['fields']);
                    }
                }

                $active_settings[ $setting_key ] = $setting;
            } else {
                $active_settings[ $setting_key ] = null;
            }
        }

        if ($is_first_request) {
            $this->activeSettings = $active_settings;
        }

        return $active_settings;
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
     * @param string $setting_key Optional. The key of the requested setting.
     *                            Default is null.
     *
     * @return mixed The settings.
     */
    public function getSettingsForDisplay($setting_key = null)
    {
        if (!$this->parsedActiveSettings) {
            $this->parsedActiveSettings = $this->getActiveSettings($this->getParsedDynamicSettings(), $this->getControls());
        }

        return self::getItems($this->parsedActiveSettings, $setting_key);
    }

    /**
     * Parse dynamic settings.
     *
     * Retrieve the settings with rendered dynamic tags.
     *
     *
     * @param array $settings     Optional. The requested setting. Default is null.
     * @param array $controls     Optional. The controls array. Default is null.
     * @param array $all_settings Optional. All the settings. Default is null.
     *
     * @return array The settings with rendered dynamic tags.
     */
    public function parseDynamicSettings($settings, $controls = null, $all_settings = null)
    {
        if (null === $all_settings) {
            $all_settings = $this->getSettings();
        }

        if (null === $controls) {
            $controls = $this->getControls();
        }

        foreach ($controls as $control) {
            $control_name = $control['name'];
            $control_obj = $this->controlManager->getControl($control['type']);

            if (!$control_obj instanceof AbstractControlData) {
                continue;
            }

            if ('repeater' === $control_obj->getType()) {
                foreach ($settings[ $control_name ] as & $field) {
                    $field = $this->parseDynamicSettings($field, $control['fields'], $field);
                }

                continue;
            }

            if (empty($control['dynamic']) || ! isset($all_settings[ Tags::DYNAMIC_SETTING_KEY ][ $control_name ])) {
                continue;
            }

            $dynamic_settings = array_merge($control_obj->getSettings('dynamic'), $control['dynamic']);

            if (!empty($dynamic_settings['active']) && ! empty($all_settings[ Tags::DYNAMIC_SETTING_KEY ][ $control_name ])) {
                $parsed_value = $control_obj->parseTags($all_settings[ Tags::DYNAMIC_SETTING_KEY ][ $control_name ], $dynamic_settings);

                $dynamic_property = ! empty($dynamic_settings['property']) ? $dynamic_settings['property'] : null;

                if ($dynamic_property) {
                    $settings[ $control_name ][ $dynamic_property ] = $parsed_value;
                } else {
                    $settings[ $control_name ] = $parsed_value;
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
        $frontend_settings = array_intersect_key($this->getActiveSettings(), array_flip($this->getFrontendSettingsKeys()));

        foreach ($frontend_settings as $key => $setting) {
            if (in_array($setting, [ null, '' ], true)) {
                unset($frontend_settings[ $key ]);
            }
        }

        return $frontend_settings;
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
            function ($filtered_settings, $setting_key) use ($controls, $settings, $callback) {
                if (isset($controls[ $setting_key ])) {
                    $result = $callback($settings[ $setting_key ], $controls[ $setting_key ]);

                    if (null !== $result) {
                        $filtered_settings[ $setting_key ] = $result;
                    }
                }

                return $filtered_settings;
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

        foreach ($control['condition'] as $condition_key => $condition_value) {
            preg_match('/([a-z_\-0-9]+)(?:\[([a-z_]+)])?(!?)$/i', $condition_key, $condition_key_parts);

            $pure_condition_key = $condition_key_parts[1];
            $condition_sub_key = $condition_key_parts[2];
            $is_negative_condition = ! ! $condition_key_parts[3];

            if (!isset($values[ $pure_condition_key ]) || null === $values[ $pure_condition_key ]) {
                return false;
            }

            $instance_value = $values[ $pure_condition_key ];

            if ($condition_sub_key && is_array($instance_value)) {
                if (!isset($instance_value[ $condition_sub_key ])) {
                    return false;
                }

                $instance_value = $instance_value[ $condition_sub_key ];
            }

            /**
             * If the $condition_value is a non empty array - check if the $condition_value contains the $instance_value,
             * If the $instance_value is a non empty array - check if the $instance_value contains the $condition_value
             * otherwise check if they are equal. ( and give the ability to check if the value is an empty array )
             */
            if (is_array($condition_value) && ! empty($condition_value)) {
                $is_contains = in_array($instance_value, $condition_value, true);
            } elseif (is_array($instance_value) && ! empty($instance_value)) {
                $is_contains = in_array($condition_value, $instance_value, true);
            } else {
                $is_contains = $instance_value === $condition_value;
            }

            if ($is_negative_condition && $is_contains || ! $is_negative_condition && ! $is_contains) {
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
     *
     */
    public function startControlsSection($sectionId, array $args = [])
    {
        $section_name = $this->getName();

        /**
         * Before section start.
         *
         * Fires before SagoTheme section starts in the editor panel.
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
         * Fires before SagoTheme section starts in the editor panel.
         *
         * The dynamic portions of the hook name, `$section_name` and `$section_id`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$section_name}/{$sectionId}/before_section_start", $this, $args);

        $args['type'] = Controls::SECTION;

        $this->addControl($sectionId, $args);

        if (null !== $this->currentSection) {
            exit(sprintf('Goomento: You can\'t start a section before the end of the previous section "%s".', $this->currentSection['section'])); // XSS ok.
        }

        $this->currentSection = $this->getSectionArgs($sectionId);

        if ($this->injectionPoint) {
            $this->injectionPoint['section'] = $this->currentSection;
        }

        /**
         * After section start.
         *
         * Fires after SagoTheme section starts in the editor panel.
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
         * Fires after SagoTheme section starts in the editor panel.
         *
         * The dynamic portions of the hook name, `$section_name` and `$section_id`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$section_name}/{$sectionId}/after_section_start", $this, $args);
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
        $stack_name = $this->getName();

        // Save the current section for the action.
        $current_section = $this->currentSection;
        $section_id = $current_section['section'];
        $args = [
            'tab' => $current_section['tab'],
        ];

        /**
         * Before section end.
         *
         * Fires before SagoTheme section ends in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $section_id Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/before_section_end', $this, $section_id, $args);

        /**
         * Before section end.
         *
         * Fires before SagoTheme section ends in the editor panel.
         *
         * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the stack name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$stack_name}/{$section_id}/before_section_end", $this, $args);

        $this->currentSection = null;

        /**
         * After section end.
         *
         * Fires after SagoTheme section ends in the editor panel.
         *
         *
         * @param ControlsStack $this       The control.
         * @param string         $section_id Section ID.
         * @param array          $args       Section arguments.
         */
        HooksHelper::doAction('pagebuilder/element/after_section_end', $this, $section_id, $args);

        /**
         * After section end.
         *
         * Fires after SagoTheme section ends in the editor panel.
         *
         * The dynamic portions of the hook name, `$stack_name` and `$section_id`, refers to the section name and section ID, respectively.
         *
         *
         * @param ControlsStack $this The control.
         * @param array          $args Section arguments.
         */
        HooksHelper::doAction("pagebuilder/element/{$stack_name}/{$section_id}/after_section_end", $this, $args);
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
     * @param string $tabs_id Tabs ID.
     * @param array $args Tabs arguments.
     * @throws Exception
     */
    public function startControlsTabs($tabs_id, array $args = [])
    {
        if (null !== $this->currentTab) {
            throw new Exception(
                sprintf('You can\'t start tabs before the end of the previous tabs "%s".', $this->currentTab['tabs_wrapper'])
            );
        }

        $args['type'] = Controls::TABS;

        $this->addControl($tabs_id, $args);

        $this->currentTab = [
            'tabs_wrapper' => $tabs_id,
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
     * @param string $tab_id Tab ID.
     * @param array $args Tab arguments.
     * @throws Exception
     */
    public function startControlsTab($tab_id, $args)
    {
        if (!empty($this->currentTab['inner_tab'])) {
            throw new Exception(
                sprintf('Goomento: You can\'t start a tab before the end of the previous tab "%s".', $this->currentTab['inner_tab'])
            );
        }

        $args['type'] = Controls::TAB;
        $args['tabs_wrapper'] = $this->currentTab['tabs_wrapper'];

        $this->addControl($tab_id, $args);

        $this->currentTab['inner_tab'] = $tab_id;

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
    final public function startPopover()
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
    final public function endPopover()
    {
        $this->currentPopover = null;

        $last_control_key = $this->getControlKey($this->getPointerIndex() - 1);

        $args = [
            'popover' => [
                'end' => true,
            ],
        ];

        $options = [
            'recursive' => true,
        ];

        $this->updateControl($last_control_key, $args, $options);
    }

    /**
     * Print element template.
     *
     * Used to generate the element template on the editor.
     *
     */
    public function printTemplate()
    {
        ob_start();

        $template = $this->contentTemplate();

        $template_content = ob_get_clean();

        if (empty($template_content) && $template) {
            $template_content = $template;
        }

        $element_type = self::TYPE;

        /**
         * Template content.
         *
         * Filters the controls stack template content before it's printed in the editor.
         *
         * The dynamic portion of the hook name, `$element_type`, refers to the element type.
         *
         *
         * @param string         $content_template The controls stack template in the editor.
         * @param ControlsStack $this             The controls stack.
         */
        $template_content = HooksHelper::applyFilters("pagebuilder/{$element_type}/print_template", $template_content, $this);

        if (empty($template_content)) {
            return;
        }
        ?>
		<script type="text/html" id="tmpl-gmt-<?= EscaperHelper::escapeHtmlAttr($this->getName()); ?>-content">
			<?php $this->printTemplateContent($template_content); ?>
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
    final public function startInjection(array $position)
    {
        if ($this->injectionPoint) {
            throw new \Exception(
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
    final public function endInjection()
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
    final public function getInjectionPoint()
    {
        return $this->injectionPoint;
    }

    /**
     * Register controls.
     *
     * Used to add new controls to any element type. For example, external
     * developers use this method to register controls in a widget.
     *
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
            $control_obj = $this->controlManager->getControl($control['type']);

            if (!$control_obj instanceof AbstractControlData) {
                continue;
            }

            $control = array_merge_recursive($control_obj->getSettings(), $control);

            $settings[ $control['name'] ] = $control_obj->getValue($control, $settings);
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
     * @param string $section_id Section ID.
     *
     * @return array Section arguments.
     */
    protected function getSectionArgs($section_id)
    {
        $section_control = $this->getControls($section_id);

        $section_args_keys = [ 'tab', 'condition' ];

        $args = array_intersect_key($section_control, array_flip($section_args_keys));

        $args['section'] = $section_id;

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
     * @param string $template_content Template content.
     */
    protected function printTemplateContent($template_content)
    {
        echo $template_content;
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
        $this->controlManager->openStack($this);
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
            if ('repeater' === $control['type']) {
                if (empty($settings[ $control['name'] ])) {
                    continue;
                }

                foreach ($settings[ $control['name'] ] as $index => $repeater_row_data) {
                    $sanitized_row_data = $this->sanitizeSettings($repeater_row_data, $control['fields']);

                    $settings[ $control['name'] ][ $index ] = $sanitized_row_data;
                }

                continue;
            }

            $is_dynamic = isset($settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ]);

            if (!$is_dynamic) {
                continue;
            }

            $value_to_check = $settings[ Tags::DYNAMIC_SETTING_KEY ][ $control['name'] ];

            $tag_text_data = $this->tagsManager->tagTextToTagData($value_to_check);

            if (!$this->tagsManager->getTag($tag_text_data['name'])) {
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

        $this->controlManager = ObjectManagerHelper::get(Controls::class);
        $this->tagsManager = ObjectManagerHelper::get(Tags::class);
    }
}
