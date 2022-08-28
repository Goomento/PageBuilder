<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls\Groups;

use Goomento\PageBuilder\Builder\Base\AbstractControlGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\GeneralSettings;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class TypographyGroup extends AbstractControlGroup
{
    const NAME = 'typography';
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
    private static $schemeFieldsKeys = [ 'font_family', 'font_weight' ];

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
        return self::$schemeFieldsKeys;
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

        $settingsManager = ObjectManagerHelper::getSettingsManager();
        $general = $settingsManager->getSettingsManagers(GeneralSettings::NAME);
        $defaultFonts = $general->getSettingModel(null)->getSettings('default_generic_fonts');

        if ($defaultFonts) {
            $defaultFonts = ', ' . $defaultFonts;
        }

        $fields['font_family'] = [
            'label' => __('Family'),
            'type' => Controls::FONT,
            'default' => '',
            'selector_value' => 'font-family: {{VALUE}}' . $defaultFonts . ';',
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

        $typoWeightOptions = [
            '' => __('Default'),
        ];

        foreach (array_merge([ 'normal', 'bold' ], range(100, 900, 100)) as $weight) {
            $typoWeightOptions[ $weight ] = ucfirst((string) $weight);
        }

        $fields['font_weight'] = [
            'label' => __('Weight'),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => $typoWeightOptions,
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
            'label' => __('Line-Height', 'Typography AbstractControl'),
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
            function (&$field, $fieldName) {
                if (in_array($fieldName, [ 'typography', 'popover_toggle' ])) {
                    return;
                }

                $selectorValue = ! empty($field['selector_value']) ? $field['selector_value'] : str_replace('_', '-', $fieldName) . ': {{VALUE}};';

                $field['selectors'] = [
                    '{{SELECTOR}}' => $selectorValue,
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
     * @param string $controlId Typography control id.
     * @param array  $fieldArgs Typography control field arguments.
     *
     * @return array Field arguments.
     */
    protected function addGroupArgsToField($controlId, $fieldArgs)
    {
        $fieldArgs = parent::addGroupArgsToField($controlId, $fieldArgs);

        $args = $this->getArgs();

        if (in_array($controlId, self::getSchemeFieldsKeys()) && ! empty($args['scheme'])) {
            $fieldArgs['scheme'] = [
                'type' => self::NAME,
                'value' => $args['scheme'],
                'key' => $controlId,
            ];
        }

        return $fieldArgs;
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
