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
     * @param array $option Optional. Single option.
     *
     * @return mixed Group control options. If option parameter was not specified, it will
     *               return an array of all the options. If single option specified, it will
     *               return the option value or `null` if option does not exists.
     */
    final public function getOptions($option = null)
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
     * @param array          $user_args The control arguments defined by the user.
     * @param array          $options   Optional. The element options. Default is
     *                                  an empty array.
     */
    final public function addControls(ControlsStack $element, array $user_args, array $options = [])
    {
        $this->initArgs($user_args);

        // Filter which controls to display
        $filtered_fields = $this->filterFields();
        $filtered_fields = $this->prepareFields($filtered_fields);

        // For php < 7
        reset($filtered_fields);

        if (isset($this->args['separator'])) {
            $filtered_fields[ key($filtered_fields) ]['separator'] = $this->args['separator'];
        }

        $has_injection = false;

        if (!empty($options['position'])) {
            $has_injection = true;

            $element->startInjection($options['position']);

            unset($options['position']);
        }

        if ($this->getOptions('popover')) {
            $this->startPopover($element);
        }

        foreach ($filtered_fields as $field_id => $field_args) {
            // Add the global group args to the control
            $field_args = $this->addGroupArgsToField($field_id, $field_args);

            // Register the control
            $id = $this->getControlsPrefix() . $field_id;

            if (!empty($field_args['responsive'])) {
                unset($field_args['responsive']);

                $element->addResponsiveControl($id, $field_args, $options);
            } else {
                $element->addControl($id, $field_args, $options);
            }
        }

        if ($this->getOptions('popover')) {
            $element->endPopover();
        }

        if ($has_injection) {
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
    final public function getArgs()
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
    final public function getFields()
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
     * @param string $control_id Group control id.
     * @param array  $field_args Group control field arguments.
     *
     * @return array
     */
    protected function addGroupArgsToField($control_id, $field_args)
    {
        $args = $this->getArgs();

        if (!empty($args['tab'])) {
            $field_args['tab'] = $args['tab'];
        }

        if (!empty($args['section'])) {
            $field_args['section'] = $args['section'];
        }

        $field_args['classes'] = $this->getBaseGroupClasses() . ' gmt-group-control-' . $control_id;

        foreach ([ 'condition', 'conditions' ] as $condition_type) {
            if (!empty($args[ $condition_type ])) {
                if (empty($field_args[ $condition_type ])) {
                    $field_args[ $condition_type ] = [];
                }

                $field_args[ $condition_type ] += $args[ $condition_type ];
            }
        }

        return $field_args;
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
        $popover_options = $this->getOptions('popover');

        $popover_name = ! $popover_options ? null : $popover_options['starter_name'];

        foreach ($fields as $field_key => &$field) {
            if ($popover_name) {
                $field['condition'][ $popover_name . '!' ] = '';
            }

            if (isset($this->args['fields_options']['__all'])) {
                $field = array_merge($field, $this->args['fields_options']['__all']);
            }

            if (isset($this->args['fields_options'][ $field_key ])) {
                $field = array_merge($field, $this->args['fields_options'][ $field_key ]);
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
                foreach ($field['device_args'] as $device => $device_arg) {
                    if (!empty($field['device_args'][ $device ]['condition'])) {
                        $field['device_args'][ $device ] = $this->addConditionPrefix($field['device_args'][ $device ]);
                    }

                    if (!empty($field['device_args'][ $device ]['conditions'])) {
                        $field['device_args'][ $device ]['conditions'] = $this->addConditionsPrefix($field['device_args'][ $device ]['conditions']);
                    }

                    if (!empty($device_arg['selectors'])) {
                        $field['device_args'][ $device ]['selectors'] = $this->handleSelectors($device_arg['selectors']);
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
        $default_options = [
            'popover' => [
                'starter_name' => 'popover_toggle',
                'starter_value' => 'custom',
                'starter_title' => '',
            ],
        ];

        $this->options = array_replace_recursive($default_options, $this->getDefaultOptions());
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
     * This way SagoTheme can apply condition logic to a specific control in a
     * group control.
     *
     *
     * @param array $field Group control field.
     *
     * @return array Group control field.
     */
    private function addConditionPrefix($field)
    {
        $controls_prefix = $this->getControlsPrefix();

        $prefixed_condition_keys = array_map(
            function ($key) use ($controls_prefix) {
                return $controls_prefix . $key;
            },
            array_keys($field['condition'])
        );

        $field['condition'] = array_combine(
            $prefixed_condition_keys,
            $field['condition']
        );

        return $field;
    }

    private function addConditionsPrefix($conditions)
    {
        $controls_prefix = $this->getControlsPrefix();

        foreach ($conditions['terms'] as & $condition) {
            if (isset($condition['terms'])) {
                $condition = $this->addConditionsPrefix($condition);

                continue;
            }

            $condition['name'] = $controls_prefix . $condition['name'];
        }

        return $conditions;
    }

    /**
     * Handle selectors.
     *
     * Used to process the CSS selector of group control fields. When using
     * group control, SagoTheme needs to apply the selector to different fields.
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

        $controls_prefix = $this->getControlsPrefix();

        foreach ($selectors as &$selector) {
            $selector = preg_replace_callback('/\{\{\K(.*?)(?=}})/', function ($matches) use ($controls_prefix) {
                return preg_replace_callback('/[^ ]+(?=\.)/', function ($sub_matches) use ($controls_prefix) {
                    return $controls_prefix . $sub_matches[0];
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
        $popover_options = $this->getOptions('popover');

        $settings = $this->getArgs();

        if (!empty($settings['label'])) {
            $label = $settings['label'];
        } else {
            $label = $popover_options['starter_title'];
        }

        $control_params = [
            'type' => Controls::POPOVER_TOGGLE,
            'label' => $label,
            'return_value' => $popover_options['starter_value'],
        ];

        if (!empty($popover_options['settings'])) {
            $control_params = array_replace_recursive($control_params, $popover_options['settings']);
        }

        foreach ([ 'condition', 'conditions' ] as $key) {
            if (!empty($settings[ $key ])) {
                $control_params[ $key ] = $settings[ $key ];
            }
        }

        $starter_name = $popover_options['starter_name'];

        if (isset($this->args['fields_options'][ $starter_name ])) {
            $control_params = array_merge($control_params, $this->args['fields_options'][ $starter_name ]);
        }

        $element->addControl($this->getControlsPrefix() . $starter_name, $control_params);

        $element->startPopover();
    }
}
