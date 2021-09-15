<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Elements;

use Goomento\PageBuilder\Builder\Base\Element;
use Goomento\PageBuilder\Builder\Controls\Groups\Background;
use Goomento\PageBuilder\Builder\Controls\Groups\Border;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilter;
use Goomento\PageBuilder\Builder\Embed;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Shapes;
use Goomento\PageBuilder\Helper\StaticEscaper;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Section
 * @package Goomento\PageBuilder\Builder\Elements
 */
class Section extends Element
{

    /**
     * Section predefined columns presets.
     *
     * Holds the predefined columns width for each columns count available by
     * default by SagoTheme. Default is an empty array.
     *
     * Note that when the user creates a section he can define custom sizes for
     * the columns. But SagoTheme sets default values for predefined columns.
     *
     * For example two columns 50% width each one, or three columns 33.33% each
     * one. This property hold the data for those preset values.
     *
     *
     * @var array Section presets.
     */
    private static $presets = [];

    /**
     * Get element type.
     *
     * Retrieve the element type, in this case `section`.
     *
     *
     * @return string The type.
     */
    public static function getType()
    {
        return 'section';
    }

    /**
     * Get section name.
     *
     * Retrieve the section name.
     *
     *
     * @return string Section name.
     */
    public function getName()
    {
        return 'section';
    }

    /**
     * Get section title.
     *
     * Retrieve the section title.
     *
     *
     * @return string Section title.
     */
    public function getTitle()
    {
        return __('Section');
    }

    /**
     * Get section icon.
     *
     * Retrieve the section icon.
     *
     *
     * @return string Section icon.
     */
    public function getIcon()
    {
        return 'fas fa-columns';
    }

    /**
     * Get presets.
     *
     * Retrieve a specific preset columns for a given columns count, or a list
     * of all the preset if no parameters passed.
     *
     *
     * @param int $columns_count Optional. Columns count. Default is null.
     * @param int $preset_index  Optional. Preset index. Default is null.
     *
     * @return array Section presets.
     */
    public static function getPresets($columns_count = null, $preset_index = null)
    {
        if (! self::$presets) {
            self::initPresets();
        }

        $presets = self::$presets;

        if (null !== $columns_count) {
            $presets = $presets[ $columns_count ];
        }

        if (null !== $preset_index) {
            $presets = $presets[ $preset_index ];
        }

        return $presets;
    }

    /**
     * Initialize presets.
     *
     * Initializing the section presets and set the number of columns the
     * section can have by default. For example a column can have two columns
     * 50% width each one, or three columns 33.33% each one.
     *
     * Note that SagoTheme sections have default section presets but the user
     * can set custom number of columns and define custom sizes for each column.

     */
    public static function initPresets()
    {
        $additional_presets = [
            2 => [
                [
                    'preset' => [ 33, 66 ],
                ],
                [
                    'preset' => [ 66, 33 ],
                ],
            ],
            3 => [
                [
                    'preset' => [ 25, 25, 50 ],
                ],
                [
                    'preset' => [ 50, 25, 25 ],
                ],
                [
                    'preset' => [ 25, 50, 25 ],
                ],
                [
                    'preset' => [ 16, 66, 16 ],
                ],
            ],
        ];

        foreach (range(1, 10) as $columns_count) {
            self::$presets[ $columns_count ] = [
                [
                    'preset' => [],
                ],
            ];

            $preset_unit = floor(1 / $columns_count * 100);

            for ($i = 0; $i < $columns_count; $i++) {
                self::$presets[ $columns_count ][0]['preset'][] = $preset_unit;
            }

            if (! empty($additional_presets[ $columns_count ])) {
                self::$presets[ $columns_count ] = array_merge(self::$presets[ $columns_count ], $additional_presets[ $columns_count ]);
            }

            foreach (self::$presets[ $columns_count ] as $preset_index => & $preset) {
                $preset['key'] = $columns_count . $preset_index;
            }
        }
    }

    /**
     * Get initial config.
     *
     * Retrieve the current section initial configuration.
     *
     * Adds more configuration on top of the controls list, the tabs assigned to
     * the control, element name, type, icon and more. This method also adds
     * section presets.
     *
     *
     * @return array The initial config.
     */
    protected function _getInitialConfig()
    {
        $config = parent::_getInitialConfig();

        $config['presets'] = self::getPresets();
        $config['controls'] = $this->getControls();
        $config['tabs_controls'] = $this->getTabsControls();

        return $config;
    }

    /**
     * Register section controls.
     *
     * Used to add new controls to the section element.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_layout',
            [
                'label' => __('Layout'),
                'tab' => Controls::TAB_LAYOUT,
            ]
        );

        // Element Name for the Navigator
        $this->addControl(
            '_title',
            [
                'label' => __('Title'),
                'type' => Controls::HIDDEN,
                'render_type' => 'none',
            ]
        );

        $this->addControl(
            'stretch_section',
            [
                'label' => __('Stretch Section'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'return_value' => 'section-stretched',
                'prefix_class' => 'gmt-',
                'hide_in_inner' => true,
                'description' => __('Stretch the section to the full width of the page using JS.'),
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'layout',
            [
                'label' => __('Content Width'),
                'type' => Controls::SELECT,
                'default' => 'boxed',
                'options' => [
                    'boxed' => __('Boxed'),
                    'full_width' => __('Full Width'),
                ],
                'prefix_class' => 'gmt-section-',
            ]
        );

        $this->addControl(
            'content_width',
            [
                'label' => __('Content Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 500,
                        'max' => 1600,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-container' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'layout' => [ 'boxed' ],
                ],
                'show_label' => false,
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'gap',
            [
                'label' => __('Columns Gap'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'no' => __('No Gap'),
                    'narrow' => __('Narrow'),
                    'extended' => __('Extended'),
                    'wide' => __('Wide'),
                    'wider' => __('Wider'),
                ],
            ]
        );

        $this->addControl(
            'height',
            [
                'label' => __('Height'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'full' => __('Fit To Screen'),
                    'min-height' => __('Min Height'),
                ],
                'prefix_class' => 'gmt-section-height-',
                'hide_in_inner' => true,
            ]
        );

        $this->addResponsiveControl(
            'custom_height',
            [
                'label' => __('Minimum Height'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 400,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1440,
                    ],
                    'vh' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'size_units' => [ 'px', 'vh', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-container' => 'min-height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} > .gmt-container:after' => 'content: ""; min-height: inherit;', // Hack for IE11
                ],
                'condition' => [
                    'height' => [ 'min-height' ],
                ],
                'hide_in_inner' => true,
            ]
        );

        $this->addControl(
            'height_inner',
            [
                'label' => __('Height'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'min-height' => __('Min Height'),
                ],
                'prefix_class' => 'gmt-section-height-',
                'hide_in_top' => true,
            ]
        );

        $this->addResponsiveControl(
            'custom_height_inner',
            [
                'label' => __('Minimum Height'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 400,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1440,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-container' => 'min-height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'height_inner' => [ 'min-height' ],
                ],
                'hide_in_top' => true,
            ]
        );

        $this->addControl(
            'column_position',
            [
                'label' => __('Column Position'),
                'type' => Controls::SELECT,
                'default' => 'middle',
                'options' => [
                    'stretch' => __('Stretch'),
                    'top' => __('Top'),
                    'middle' => __('Middle'),
                    'bottom' => __('Bottom'),
                ],
                'prefix_class' => 'gmt-section-items-',
                'condition' => [
                    'height' => [ 'full', 'min-height' ],
                ],
            ]
        );

        $this->addControl(
            'content_position',
            [
                'label' => __('Vertical Align'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'top' => __('Top'),
                    'middle' => __('Middle'),
                    'bottom' => __('Bottom'),
                    'space-between' => __('Space Between'),
                    'space-around' => __('Space Around'),
                    'space-evenly' => __('Space Evenly'),
                ],
                'selectors_dictionary' => [
                    'top' => 'flex-start',
                    'middle' => 'center',
                    'bottom' => 'flex-end',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-container > .gmt-row > .gmt-column > .gmt-column-wrap > .gmt-widget-wrap' => 'align-content: {{VALUE}}; align-items: {{VALUE}};',
                ],
                // TODO: The following line is for BC since 2.7.0
                'prefix_class' => 'gmt-section-content-',
            ]
        );

        $this->addControl(
            'overflow',
            [
                'label' => __('Overflow'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'hidden' => __('Hidden'),
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'overflow: {{VALUE}}',
                ],
            ]
        );

        $possible_tags = [
            'div',
            'header',
            'footer',
            'main',
            'article',
            'section',
            'aside',
            'nav',
        ];

        $options = [
            '' => __('Default'),
        ] + array_combine($possible_tags, $possible_tags);

        $this->addControl(
            'html_tag',
            [
                'label' => __('HTML Tag'),
                'type' => Controls::SELECT,
                'options' => $options,
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        // Section Structure
        $this->startControlsSection(
            'section_structure',
            [
                'label' => __('Structure'),
                'tab' => Controls::TAB_LAYOUT,
            ]
        );

        $this->addControl(
            'structure',
            [
                'label' => __('Structure'),
                'type' => Controls::STRUCTURE,
                'default' => '10',
                'render_type' => 'none',
            ]
        );

        $this->endControlsSection();

        // Section background
        $this->startControlsSection(
            'section_background',
            [
                'label' => __('Background'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_background');

        $this->startControlsTab(
            'tab_background_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            Background::getType(),
            [
                'name' => 'background',
                'types' => [ 'classic', 'gradient', 'video', 'slideshow' ],
                'fields_options' => [
                    'background' => [
                        'frontend_available' => true,
                    ],
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_background_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            Background::getType(),
            [
                'name' => 'background_hover',
                'selector' => '{{WRAPPER}}:hover',
            ]
        );

        $this->addControl(
            'background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'render_type' => 'ui',
                'separator' => 'before',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        // Background Overlay
        $this->startControlsSection(
            'section_background_overlay',
            [
                'label' => __('Background Overlay'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_background_overlay');

        $this->startControlsTab(
            'tab_background_overlay_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            Background::getType(),
            [
                'name' => 'background_overlay',
                'selector' => '{{WRAPPER}} > .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_opacity',
            [
                'label' => __('Opacity'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => .5,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-background-overlay' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'background_overlay_background' => [ 'classic', 'gradient' ],
                ],
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'overlay_blend_mode',
            [
                'label' => __('Blend Mode'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Normal'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-background-overlay' => 'mix-blend-mode: {{VALUE}}',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_background_overlay_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            Background::getType(),
            [
                'name' => 'background_overlay_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_hover_opacity',
            [
                'label' => __('Opacity'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => .5,
                ],
                'range' => [
                    'px' => [
                        'max' => 1,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}:hover > .gmt-background-overlay' => 'opacity: {{SIZE}};',
                ],
                'condition' => [
                    'background_overlay_hover_background' => [ 'classic', 'gradient' ],
                ],
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover > .gmt-background-overlay',
            ]
        );

        $this->addControl(
            'background_overlay_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'render_type' => 'ui',
                'separator' => 'before',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        // Section border
        $this->startControlsSection(
            'section_border',
            [
                'label' => __('Border'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_border');

        $this->startControlsTab(
            'tab_border_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            Border::getType(),
            [
                'name' => 'border',
            ]
        );

        $this->addResponsiveControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}, {{WRAPPER}} > .gmt-background-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadow::getType(),
            [
                'name' => 'box_shadow',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_border_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            Border::getType(),
            [
                'name' => 'border_hover',
                'selector' => '{{WRAPPER}}:hover',
            ]
        );

        $this->addResponsiveControl(
            'border_radius_hover',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}:hover, {{WRAPPER}}:hover > .gmt-background-overlay' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadow::getType(),
            [
                'name' => 'box_shadow_hover',
                'selector' => '{{WRAPPER}}:hover',
            ]
        );

        $this->addControl(
            'border_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'separator' => 'before',
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'background_background',
                            'operator' => '!==',
                            'value' => '',
                        ],
                        [
                            'name' => 'border_border',
                            'operator' => '!==',
                            'value' => '',
                        ],
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'transition: background {{background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
                    '{{WRAPPER}} > .gmt-background-overlay' => 'transition: background {{background_overlay_hover_transition.SIZE}}s, border-radius {{SIZE}}s, opacity {{background_overlay_hover_transition.SIZE}}s',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        // Section Shape Divider
        $this->startControlsSection(
            'section_shape_divider',
            [
                'label' => __('Shape Divider'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('tabs_shape_dividers');

        $shapes_options = [
            '' => __('None'),
        ];

        foreach (Shapes::getShapes() as $shape_name => $shape_props) {
            $shapes_options[ $shape_name ] = $shape_props['title'];
        }

        foreach ([
            'top' => __('Top'),
            'bottom' => __('Bottom'),
        ] as $side => $side_label) {
            $base_control_key = "shape_divider_$side";

            $this->startControlsTab(
                "tab_$base_control_key",
                [
                    'label' => $side_label,
                ]
            );

            $this->addControl(
                $base_control_key,
                [
                    'label' => __('Type'),
                    'type' => Controls::SELECT,
                    'options' => $shapes_options,
                    'render_type' => 'none',
                    'frontend_available' => true,
                ]
            );

            $this->addControl(
                $base_control_key . '_color',
                [
                    'label' => __('Color'),
                    'type' => Controls::COLOR,
                    'condition' => [
                        "shape_divider_$side!" => '',
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .gmt-shape-$side .gmt-shape-fill" => 'fill: {{UNIT}};',
                    ],
                ]
            );

            $this->addResponsiveControl(
                $base_control_key . '_width',
                [
                    'label' => __('Width'),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => '%',
                    ],
                    'tablet_default' => [
                        'unit' => '%',
                    ],
                    'mobile_default' => [
                        'unit' => '%',
                    ],
                    'range' => [
                        '%' => [
                            'min' => 100,
                            'max' => 300,
                        ],
                    ],
                    'condition' => [
                        "shape_divider_$side" => array_keys(Shapes::filterShapes('height_only', Shapes::FILTER_EXCLUDE)),
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .gmt-shape-$side svg" => 'width: calc({{SIZE}}{{UNIT}} + 1.3px)',
                    ],
                ]
            );

            $this->addResponsiveControl(
                $base_control_key . '_height',
                [
                    'label' => __('Height'),
                    'type' => Controls::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 500,
                        ],
                    ],
                    'condition' => [
                        "shape_divider_$side!" => '',
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .gmt-shape-$side svg" => 'height: {{SIZE}}{{UNIT}};',
                    ],
                ]
            );

            $this->addControl(
                $base_control_key . '_flip',
                [
                    'label' => __('Flip'),
                    'type' => Controls::SWITCHER,
                    'condition' => [
                        "shape_divider_$side" => array_keys(Shapes::filterShapes('has_flip')),
                    ],
                    'selectors' => [
                        "{{WRAPPER}} > .gmt-shape-$side svg" => 'transform: translateX(-50%) rotateY(180deg)',
                    ],
                ]
            );

            $this->addControl(
                $base_control_key . '_negative',
                [
                    'label' => __('Invert'),
                    'type' => Controls::SWITCHER,
                    'frontend_available' => true,
                    'condition' => [
                        "shape_divider_$side" => array_keys(Shapes::filterShapes('has_negative')),
                    ],
                    'render_type' => 'none',
                ]
            );

            $this->addControl(
                $base_control_key . '_above_content',
                [
                    'label' => __('Bring to Front'),
                    'type' => Controls::SWITCHER,
                    'selectors' => [
                        "{{WRAPPER}} > .gmt-shape-$side" => 'z-index: 2; pointer-events: none',
                    ],
                    'condition' => [
                        "shape_divider_$side!" => '',
                    ],
                ]
            );

            $this->endControlsTab();
        }

        $this->endControlsTabs();

        $this->endControlsSection();

        // Section Typography
        $this->startControlsSection(
            'section_typo',
            [
                'label' => __('Typography'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        if (in_array(Color::getType(), Schemes::getEnabledSchemes(), true)) {
            $this->addControl(
                'colors_warning',
                [
                    'type' => Controls::RAW_HTML,
                    'raw' => __('Note: The following colors won\'t work if Default Colors are enabled.'),
                    'content_classes' => 'gmt-panel-alert gmt-panel-alert-warning',
                ]
            );
        }

        $this->addControl(
            'heading_color',
            [
                'label' => __('Heading Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-heading-title' => 'color: {{VALUE}};',
                ],
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'color_text',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'color_link',
            [
                'label' => __('Link Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'color_link_hover',
            [
                'label' => __('Link Hover Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'text_align',
            [
                'label' => __('Text Align'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-container' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        // Section Advanced
        $this->startControlsSection(
            'section_advanced',
            [
                'label' => __('Advanced'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addResponsiveControl(
            'margin',
            [
                'label' => __('Margin'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'allowed_dimensions' => 'vertical',
                'placeholder' => [
                    'top' => '',
                    'right' => 'auto',
                    'bottom' => '',
                    'left' => 'auto',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'margin-top: {{TOP}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'z_index',
            [
                'label' => __('Z-Index'),
                'type' => Controls::NUMBER,
                'min' => 0,
                'selectors' => [
                    '{{WRAPPER}}' => 'z-index: {{VALUE}};',
                ],
                'label_block' => false,
            ]
        );

        $this->addControl(
            '_element_id',
            [
                'label' => __('CSS ID'),
                'type' => Controls::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'label_block' => false,
                'style_transfer' => false,
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        $this->addControl(
            'css_classes',
            [
                'label' => __('CSS Classes'),
                'type' => Controls::TEXT,
                'default' => '',
                'dynamic' => [
                    'active' => true,
                ],
                'prefix_class' => '',
                'title' => __('Add your custom class WITHOUT the dot. e.g: my-class'),
                'label_block' => false,
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_effects',
            [
                'label' => __('Motion Effects'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addResponsiveControl(
            'animation',
            [
                'label' => __('Entrance Animation'),
                'type' => Controls::ANIMATION,
                'frontend_available' => true,
            ]
        );

        $this->addControl(
            'animation_duration',
            [
                'label' => __('Animation Duration'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    'slow' => __('Slow'),
                    '' => __('Normal'),
                    'fast' => __('Fast'),
                ],
                'prefix_class' => 'animated-',
                'condition' => [
                    'animation!' => '',
                ],
            ]
        );

        $this->addControl(
            'animation_delay',
            [
                'label' => __('Animation Delay') . ' (ms)',
                'type' => Controls::NUMBER,
                'default' => '',
                'min' => 0,
                'step' => 100,
                'condition' => [
                    'animation!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        // Section Responsive
        $this->startControlsSection(
            '_section_responsive',
            [
                'label' => __('Responsive'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addControl(
            'reverse_order_tablet',
            [
                'label' => __('Reverse Columns') . ' (' . __('Tablet') . ')',
                'type' => Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'return_value' => 'reverse-tablet',
            ]
        );

        $this->addControl(
            'reverse_order_mobile',
            [
                'label' => __('Reverse Columns') . ' (' . __('Mobile') . ')',
                'type' => Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'return_value' => 'reverse-mobile',
            ]
        );

        $this->addControl(
            'heading_visibility',
            [
                'label' => __('Visibility'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'responsive_description',
            [
                'raw' => __('Responsive visibility will take effect only on preview or live page, and not while editing in SagoTheme.'),
                'type' => Controls::RAW_HTML,
                'content_classes' => 'gmt-descriptor',
            ]
        );

        $this->addControl(
            'hide_desktop',
            [
                'label' => __('Hide On Desktop'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => __('Hide'),
                'label_off' => __('Show'),
                'return_value' => 'hidden-desktop',
            ]
        );

        $this->addControl(
            'hide_tablet',
            [
                'label' => __('Hide On Tablet'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => __('Hide'),
                'label_off' => __('Show'),
                'return_value' => 'hidden-tablet',
            ]
        );

        $this->addControl(
            'hide_mobile',
            [
                'label' => __('Hide On Mobile'),
                'type' => Controls::SWITCHER,
                'default' => '',
                'prefix_class' => 'gmt-',
                'label_on' => __('Hide'),
                'label_off' => __('Show'),
                'return_value' => 'hidden-phone',
            ]
        );

        $this->endControlsSection();

        StaticObjectManager::get(Controls::class)->addCustomCssControls($this);
    }

    /**
     * Render section output in the editor.
     *
     * Used to generate the live preview, using a Backbone JavaScript template.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		if ( settings.background_video_link ) {
			let videoAttributes = 'autoplay muted playsinline';

			if ( ! settings.background_play_once ) {
				videoAttributes += ' loop';
			}

			view.addRenderAttribute( 'background-video-container', 'class', 'gmt-background-video-container' );

			if ( ! settings.background_play_on_mobile ) {
				view.addRenderAttribute( 'background-video-container', 'class', 'gmt-hidden-phone' );
			}
		#>
			<div {{{ view.getRenderAttributeString( 'background-video-container' ) }}}>
				<div class="gmt-background-video-embed"></div>
				<video class="gmt-background-video-hosted gmt-html5-video" {{ videoAttributes }}></video>
			</div>
		<# } #>
		<div class="gmt-background-overlay"></div>
		<div class="gmt-shape gmt-shape-top"></div>
		<div class="gmt-shape gmt-shape-bottom"></div>
		<div class="gmt-container gmt-column-gap-{{ settings.gap }}">
			<div class="gmt-row"></div>
		</div>
		<?php
    }

    /**
     * Before section rendering.
     *
     * Used to add stuff before the section element.
     *
     */
    public function beforeRender()
    {
        $settings = $this->getSettingsForDisplay(); ?>
		<<?= StaticEscaper::escapeHtml($this->getHtmlTag()); ?> <?php $this->printRenderAttributeString('_wrapper'); ?>>
			<?php
            if ('video' === $settings['background_background']) :
                if ($settings['background_video_link']) :
                    $video_properties = Embed::getVideoProperties($settings['background_video_link']);

        $this->addRenderAttribute('background-video-container', 'class', 'gmt-background-video-container');

        if (! $settings['background_play_on_mobile']) {
            $this->addRenderAttribute('background-video-container', 'class', 'gmt-hidden-phone');
        } ?>
					<div <?= $this->getRenderAttributeString('background-video-container'); ?>>
						<?php if ($video_properties) : ?>
							<div class="gmt-background-video-embed"></div>
							<?php
                        else :
                            $video_tag_attributes = 'autoplay muted playsinline';
        if ('yes' !== $settings['background_play_once']) :
                                $video_tag_attributes .= ' loop';
        endif; ?>
							<video class="gmt-background-video-hosted gmt-html5-video" <?= $video_tag_attributes; ?>></video>
						<?php endif; ?>
					</div>
					<?php
                endif;
        endif;

        $has_background_overlay = in_array($settings['background_overlay_background'], [ 'classic', 'gradient' ], true) ||
                                    in_array($settings['background_overlay_hover_background'], [ 'classic', 'gradient' ], true);

        if ($has_background_overlay) :
                ?>
				<div class="gmt-background-overlay"></div>
				<?php
            endif;

        if ($settings['shape_divider_top']) {
            $this->printShapeDivider('top');
        }

        if ($settings['shape_divider_bottom']) {
            $this->printShapeDivider('bottom');
        } ?>
			<div class="gmt-container gmt-column-gap-<?= StaticEscaper::escapeHtml($settings['gap']); ?>">
				<div class="gmt-row">
		<?php
    }

    /**
     * After section rendering.
     *
     * Used to add stuff after the section element.
     *
     */
    public function afterRender()
    {
        ?>
				</div>
			</div>
		</<?= StaticEscaper::escapeHtml($this->getHtmlTag()); ?>>
		<?php
    }

    /**
     * Add section render attributes.
     *
     * Used to add attributes to the current section wrapper HTML tag.
     *
     */
    protected function _addRenderAttributes()
    {
        parent::_addRenderAttributes();

        $section_type = $this->getData('isInner') ? 'inner' : 'top';

        $this->addRenderAttribute(
            '_wrapper',
            'class',
            [
                'gmt-section',
                'gmt-' . $section_type . '-section',
            ]
        );
    }

    /**
     * Get default child type.
     *
     * Retrieve the section child type based on element data.
     *
     *
     * @param array $element_data Element ID.
     */
    protected function _getDefaultChildType(array $element_data)
    {
        /** @var Elements $elements */
        $elements = StaticObjectManager::get(Elements::class);
        return $elements->getElementTypes('column');
    }

    /**
     * Get HTML tag.
     *
     * Retrieve the section element HTML tag.
     *
     *
     * @return string Section HTML tag.
     */
    private function getHtmlTag()
    {
        $html_tag = $this->getSettings('html_tag');

        if (empty($html_tag)) {
            $html_tag = 'section';
        }

        return $html_tag;
    }

    /**
     * Print section shape divider.
     *
     * Used to generate the shape dividers HTML.
     *
     *
     * @param string $side Shape divider side, used to set the shape key.
     */
    private function printShapeDivider($side)
    {
        $settings = $this->getActiveSettings();
        $base_setting_key = "shape_divider_$side";
        $negative = ! empty($settings[ $base_setting_key . '_negative' ]);
        $shape_path = Shapes::getShapePath($settings[ $base_setting_key ], $negative);
        if (! is_file($shape_path) || ! is_readable($shape_path)) {
            return;
        } ?>
		<div class="gmt-shape gmt-shape-<?= StaticEscaper::escapeHtml($side); ?>" data-negative="<?= var_export($negative); ?>">
			<?= file_get_contents($shape_path); ?>
		</div>
		<?php
    }
}
