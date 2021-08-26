<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Core\Settings\General\Manager;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;

/**
 * Class Typography
 * @package Goomento\PageBuilder\Builder\Controls\Groups
 */
class Typography extends Base
{

    /**
     * Fields.
     *
     * Holds all the typography control fields.
     *
     *
     * @var array Typography control fields.
     */
    protected static $fields;

    /**
     * Scheme fields keys.
     *
     * Holds all the typography control scheme fields keys.
     * Default is an array containing `font_family` and `font_weight`.
     *
     *
     * @var array Typography control scheme fields keys.
     */
    private static $_scheme_fields_keys = [ 'font_family', 'font_weight' ];

    /**
     * Get scheme fields keys.
     *
     * Retrieve all the available typography control scheme fields keys.
     *
     *
     * @return array Scheme fields keys.
     */
    public static function getSchemeFieldsKeys()
    {
        return self::$_scheme_fields_keys;
    }

    /**
     * Get typography control type.
     *
     * Retrieve the control type, in this case `typography`.
     *
     *
     * @return string Control type.
     */
    public static function getType()
    {
        return 'typography';
    }

    /**
     * Init fields.
     *
     * Initialize typography control fields.
     *
     *
     * @return array Control fields.
     */
    protected function initFields()
    {
        $fields = [];

        /** @var Manager $general */
        $general = SettingsManager::getSettingsManagers('general');
        $default_fonts = $general->getSettingModel()->getSettings('default_generic_fonts');

        if ($default_fonts) {
            $default_fonts = ', ' . $default_fonts;
        }

        $fields['font_family'] = [
            'label' => __('Family'),
            'type' => Controls::FONT,
            'default' => '',
            'selector_value' => 'font-family: "{{VALUE}}"' . $default_fonts . ';',
        ];

        $fields['font_size'] = [
            'label' => __('Size'),
            'type' => Controls::SLIDER,
            'size_units' => [ 'px', 'em', 'rem', 'vw' ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 200,
                ],
                'vw' => [
                    'min' => 0.1,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ],
            'responsive' => true,
            'selector_value' => 'font-size: {{SIZE}}{{UNIT}}',
        ];

        $typo_weight_options = [
            '' => __('Default'),
        ];

        foreach (array_merge([ 'normal', 'bold' ], range(100, 900, 100)) as $weight) {
            $typo_weight_options[ $weight ] = ucfirst((string) $weight);
        }

        $fields['font_weight'] = [
            'label' => __('Weight'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => $typo_weight_options,
        ];

        $fields['text_transform'] = [
            'label' => __('Transform'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default'),
                'uppercase' => __('Uppercase'),
                'lowercase' => __('Lowercase'),
                'capitalize' => __('Capitalize'),
                'none' => __('Normal'),
            ],
        ];

        $fields['font_style'] = [
            'label' => __('Style'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default'),
                'normal' => __('Normal'),
                'italic' => __('Italic'),
                'oblique' => __('Oblique'),
            ],
        ];

        $fields['text_decoration'] = [
            'label' => __('Decoration'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Default'),
                'underline' => __('Underline'),
                'overline' => __('Overline'),
                'line-through' => __('Line Through'),
                'none' => __('None'),
            ],
        ];

        $fields['line_height'] = [
            'label' => __('Line-Height', 'Typography Control'),
            'type' => Controls::SLIDER,
            'desktop_default' => [
                'unit' => 'em',
            ],
            'tablet_default' => [
                'unit' => 'em',
            ],
            'mobile_default' => [
                'unit' => 'em',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                ],
            ],
            'responsive' => true,
            'size_units' => [ 'px', 'em' ],
            'selector_value' => 'line-height: {{SIZE}}{{UNIT}}',
        ];

        $fields['letter_spacing'] = [
            'label' => __('Letter Spacing'),
            'type' => Controls::SLIDER,
            'range' => [
                'px' => [
                    'min' => -5,
                    'max' => 10,
                    'step' => 0.1,
                ],
            ],
            'responsive' => true,
            'selector_value' => 'letter-spacing: {{SIZE}}{{UNIT}}',
        ];

        return $fields;
    }

    /**
     * Prepare fields.
     *
     * Process typography control fields before adding them to `add_control()`.
     *
     *
     * @param array $fields Typography control fields.
     *
     * @return array Processed fields.
     */
    protected function prepareFields($fields)
    {
        array_walk(
            $fields,
            function (&$field, $field_name) {
                if (in_array($field_name, [ 'typography', 'popover_toggle' ])) {
                    return;
                }

                $selector_value = ! empty($field['selector_value']) ? $field['selector_value'] : str_replace('_', '-', $field_name) . ': {{VALUE}};';

                $field['selectors'] = [
                    '{{SELECTOR}}' => $selector_value,
                ];
            }
        );

        return parent::prepareFields($fields);
    }

    /**
     * Add group arguments to field.
     *
     * Register field arguments to typography control.
     *
     *
     * @param string $control_id Typography control id.
     * @param array  $field_args Typography control field arguments.
     *
     * @return array Field arguments.
     */
    protected function addGroupArgsToField($control_id, $field_args)
    {
        $field_args = parent::addGroupArgsToField($control_id, $field_args);

        $args = $this->getArgs();

        if (in_array($control_id, self::getSchemeFieldsKeys()) && ! empty($args['scheme'])) {
            $field_args['scheme'] = [
                'type' => self::getType(),
                'value' => $args['scheme'],
                'key' => $control_id,
            ];
        }

        return $field_args;
    }

    /**
     * Get default options.
     *
     * Retrieve the default options of the typography control. Used to return the
     * default options while initializing the typography control.
     *
     *
     * @return array Default typography control options.
     */
    protected function getDefaultOptions()
    {
        return [
            'popover' => [
                'starter_name' => 'typography',
                'starter_title' => __('Typography'),
                'settings' => [
                    'render_type' => 'ui',
                ],
            ],
        ];
    }
}
