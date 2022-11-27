<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Builder\Managers\Controls;

abstract class AbstractControlGroup extends AbstractEntity
{
    const NAME  = 'base';

    const TYPE = 'control_group';

    /**
     * Arguments.
     *
     * Holds all the group control arguments.
     *
     *
     * @var array Group control arguments.
     */
    private $args = [];

    /**
     * Options.
     *
     * Holds all the group control options.
     *
     * Currently supports only the popover options.
     *
     *
     * @var array Group control options.
     */
    private $options;

    /**
     * Get options.
     *
     * Retrieve group control options. If options are not set, it will initialize default options.
     *
     *
     * @param string|null $option Optional. Single option.
     *
     * @return mixed Group control options. If option parameter was not specified, it will
     *               return an array of all the options. If single option specified, it will
     *               return the option value or `null` if option does not exists.
     */
    public function getOptions(?string $option = null)
    {
        if (null === $this->options) {
            $this->initOptions();
        }

        if ($option) {
            if (isset($this->options[ $option ])) {
                return $this->options[ $option ];
            }

            return null;
        }

        return $this->options;
    }

    /**
     * Add new controls to stack.
     *
     * Register multiple controls to allow the user to set/update data.
     *
     *
     * @param ControlsStack $element   The element stack.
     * @param array          $userArgs The control arguments defined by the user.
     * @param array          $options   Optional. The element options. Default is
     *                                  an empty array.
     */
    public function addControls(ControlsStack $element, array $userArgs, array $options = [])
    {
        $this->initArgs($userArgs);

        // Filter which controls to display
        $filteredFields = $this->filterFields();
        $filteredFields = $this->prepareFields($filteredFields);

        // For php < 7
        reset($filteredFields);

        if (isset($this->args['separator'])) {
            $filteredFields[ key($filteredFields) ]['separator'] = $this->args['separator'];
        }

        $hasInjection = false;

        if (!empty($options['position'])) {
            $hasInjection = true;

            $element->startInjection($options['position']);

            unset($options['position']);
        }

        if ($this->getOptions('popover')) {
            $this->startPopover($element);
        }

        foreach ($filteredFields as $fieldId => $fieldArgs) {
            // Add the global group args to the control
            $fieldArgs = $this->addGroupArgsToField($fieldId, $fieldArgs);

            // Register the control
            $id = $this->getControlsPrefix() . $fieldId;

            if (!empty($fieldArgs['responsive'])) {
                unset($fieldArgs['responsive']);

                $element->addResponsiveControl($id, $fieldArgs, $options);
            } else {
                $element->addControl($id, $fieldArgs, $options);
            }
        }

        if ($this->getOptions('popover')) {
            $element->endPopover();
        }

        if ($hasInjection) {
            $element->endInjection();
        }
    }

    /**
     * Get arguments.
     *
     * Retrieve group control arguments.
     *
     *
     * @return array Group control arguments.
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Get fields.
     *
     * Retrieve group control fields.
     *
     *
     * @return array Control fields.
     */
    public function getFields()
    {
        if (null === static::$fields) {
            static::$fields = $this->initFields();
        }

        return static::$fields;
    }

    /**
     * Get controls prefix.
     *
     * Retrieve the prefix of the group control, which is `{{ControlName}}_`.
     *
     *
     * @return string Control prefix.
     */
    public function getControlsPrefix()
    {
        return $this->args['name'] . '_';
    }

    /**
     * Get group control classes.
     *
     * Retrieve the classes of the group control.
     *
     *
     * @return string Group control classes.
     */
    public function getBaseGroupClasses()
    {
        return 'gmt-group-control-' . static::TYPE . ' gmt-group-control';
    }

    /**
     * Init fields.
     *
     * Initialize group control fields.
     *
     * @abstract
     */
    abstract protected function initFields();

    /**
     * Get default options.
     *
     * Retrieve the default options of the group control. Used to return the
     * default options while initializing the group control.
     *
     *
     * @return array Default group control options.
     */
    protected function getDefaultOptions()
    {
        return [];
    }

    /**
     * Get child default arguments.
     *
     * Retrieve the default arguments for all the child controls for a specific group
     * control.
     *
     *
     * @return array Default arguments for all the child controls.
     */
    protected function getChildDefaultArgs()
    {
        return [];
    }

    /**
     * Filter fields.
     *
     * Filter which controls to display, using `include`, `exclude` and the
     * `condition` arguments.
     *
     *
     * @return array Control fields.
     */
    protected function filterFields()
    {
        $args = $this->getArgs();

        $fields = $this->getFields();

        if (!empty($args['include'])) {
            $fields = array_intersect_key($fields, array_flip($args['include']));
        }

        if (!empty($args['exclude'])) {
            $fields = array_diff_key($fields, array_flip($args['exclude']));
        }

        return $fields;
    }

    /**
     * Add group arguments to field.
     *
     * Register field arguments to group control.
     *
     *
     * @param string $controlId Group control id.
     * @param array  $fieldArgs Group control field arguments.
     *
     * @return array
     */
    protected function addGroupArgsToField($controlId, $fieldArgs)
    {
        $args = $this->getArgs();

        if (!empty($args['tab'])) {
            $fieldArgs['tab'] = $args['tab'];
        }

        if (!empty($args['section'])) {
            $fieldArgs['section'] = $args['section'];
        }

        $fieldArgs['classes'] = $this->getBaseGroupClasses() . ' gmt-group-control-' . $controlId;

        foreach ([ 'condition', 'conditions' ] as $conditionType) {
            if (!empty($args[ $conditionType ])) {
                if (empty($fieldArgs[ $conditionType ])) {
                    $fieldArgs[ $conditionType ] = [];
                }

                $fieldArgs[ $conditionType ] += $args[ $conditionType ];
            }
        }

        return $fieldArgs;
    }

    /**
     * Prepare fields.
     *
     * Process group control fields before adding them to `add_control()`.
     *
     *
     * @param array $fields Group control fields.
     *
     * @return array Processed fields.
     */
    protected function prepareFields($fields)
    {
        $popoverOptions = $this->getOptions('popover');

        $popoverName = ! $popoverOptions ? null : $popoverOptions['starter_name'];

        foreach ($fields as $fieldKey => &$field) {
            if ($popoverName) {
                $field['condition'][ $popoverName . '!' ] = '';
            }

            if (isset($this->args['fields_options']['__all'])) {
                $field = array_merge($field, $this->args['fields_options']['__all']);
            }

            if (isset($this->args['fields_options'][ $fieldKey ])) {
                $field = array_merge($field, $this->args['fields_options'][ $fieldKey ]);
            }

            if (!empty($field['condition'])) {
                $field = $this->addConditionPrefix($field);
            }

            if (!empty($field['conditions'])) {
                $field['conditions'] = $this->addConditionsPrefix($field['conditions']);
            }

            if (!empty($field['selectors'])) {
                $field['selectors'] = $this->handleSelectors($field['selectors']);
            }

            if (!empty($field['device_args'])) {
                foreach ($field['device_args'] as $device => $deviceArg) {
                    if (!empty($field['device_args'][ $device ]['condition'])) {
                        $field['device_args'][ $device ] = $this->addConditionPrefix($field['device_args'][ $device ]);
                    }

                    if (!empty($field['device_args'][ $device ]['conditions'])) {
                        $field['device_args'][ $device ]['conditions'] = $this->addConditionsPrefix($field['device_args'][ $device ]['conditions']);
                    }

                    if (!empty($deviceArg['selectors'])) {
                        $field['device_args'][ $device ]['selectors'] = $this->handleSelectors($deviceArg['selectors']);
                    }
                }
            }
        }

        return $fields;
    }

    /**
     * Init options.
     *
     * Initializing group control options.
     *
     */
    private function initOptions()
    {
        $defaultOptions = [
            'popover' => [
                'starter_name' => 'popover_toggle',
                'starter_value' => 'custom',
                'starter_title' => '',
            ],
        ];

        $this->options = array_replace_recursive($defaultOptions, $this->getDefaultOptions());
    }

    /**
     * Init arguments.
     *
     * Initializing group control base class.
     *
     *
     * @param array $args Group control settings value.
     */
    protected function initArgs($args)
    {
        $this->args = array_merge($this->getDefaultArgs(), $this->getChildDefaultArgs(), $args);
    }

    /**
     * Get default arguments.
     *
     * Retrieve the default arguments of the group control. Used to return the
     * default arguments while initializing the group control.
     *
     *
     * @return array Control default arguments.
     */
    private function getDefaultArgs()
    {
        return [
            'default' => '',
            'selector' => '{{WRAPPER}}',
            'fields_options' => [],
        ];
    }

    /**
     * Add condition prefix.
     *
     * Used to add the group prefix to controls with conditions, to
     * distinguish them from other controls with the same name.
     *
     * This way Goomento can apply condition logic to a specific control in a
     * group control.
     *
     *
     * @param array $field Group control field.
     *
     * @return array Group control field.
     */
    private function addConditionPrefix($field)
    {
        $controlsPrefix = $this->getControlsPrefix();

        $prefixedConditionKeys = array_map(
            function ($key) use ($controlsPrefix) {
                return $controlsPrefix . $key;
            },
            array_keys($field['condition'])
        );

        $field['condition'] = array_combine(
            $prefixedConditionKeys,
            $field['condition']
        );

        return $field;
    }

    private function addConditionsPrefix($conditions)
    {
        $controlsPrefix = $this->getControlsPrefix();

        foreach ($conditions['terms'] as & $condition) {
            if (isset($condition['terms'])) {
                $condition = $this->addConditionsPrefix($condition);

                continue;
            }

            $condition['name'] = $controlsPrefix . $condition['name'];
        }

        return $conditions;
    }

    /**
     * Handle selectors.
     *
     * Used to process the CSS selector of group control fields. When using
     * group control, Goomento needs to apply the selector to different fields.
     * This method handles the process.
     *
     * In addition, it handles selector values from other fields and process the
     * css.
     *
     *
     * @param array $selectors An array of selectors to process.
     *
     * @return array Processed selectors.
     */
    private function handleSelectors($selectors)
    {
        $args = $this->getArgs();

        $selectors = array_combine(
            array_map(
                function ($key) use ($args) {
                    return str_replace('{{SELECTOR}}', $args['selector'], $key);
                },
                array_keys($selectors)
            ),
            $selectors
        );

        if (!$selectors) {
            return $selectors;
        }

        $controlsPrefix = $this->getControlsPrefix();

        foreach ($selectors as &$selector) {
            $selector = preg_replace_callback('/\{\{\K(.*?)(?=}})/', function ($matches) use ($controlsPrefix) {
                return preg_replace_callback('/[^ ]+(?=\.)/', function ($subMatches) use ($controlsPrefix) {
                    return $controlsPrefix . $subMatches[0];
                }, $matches[1]);
            }, $selector);
        }

        return $selectors;
    }

    /**
     * Start popover.
     *
     * Starts a group controls popover.
     *
     * @param ControlsStack $element Element.
     */
    private function startPopover(ControlsStack $element)
    {
        $popoverOptions = $this->getOptions('popover');

        $settings = $this->getArgs();

        if (!empty($settings['label'])) {
            $label = $settings['label'];
        } else {
            $label = $popoverOptions['starter_title'];
        }

        $controlParams = [
            'type' => Controls::POPOVER_TOGGLE,
            'label' => $label,
            'return_value' => $popoverOptions['starter_value'],
        ];

        if (!empty($popoverOptions['settings'])) {
            $controlParams = array_replace_recursive($controlParams, $popoverOptions['settings']);
        }

        foreach ([ 'condition', 'conditions' ] as $key) {
            if (!empty($settings[ $key ])) {
                $controlParams[ $key ] = $settings[ $key ];
            }
        }

        $starterName = $popoverOptions['starter_name'];

        if (isset($this->args['fields_options'][ $starterName ])) {
            $controlParams = array_merge($controlParams, $this->args['fields_options'][ $starterName ]);
        }

        $element->addControl($this->getControlsPrefix() . $starterName, $controlParams);

        $element->startPopover();
    }
}
