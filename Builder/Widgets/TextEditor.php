<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;

class TextEditor extends AbstractWidget
{

    const NAME = 'text-editor';

    /**
     * @var string
     */
    protected $template = 'Goomento_PageBuilder::widgets/text_editor.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Text Editor');
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-paragraph';
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'text', 'editor' ];
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerTextEditorInterface(AbstractWidget $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'editor',
            $args + [
                'label' => '',
                'type' => Controls::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap',
            $args + [
                'label' => __('Drop Cap'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'prefix_class' => 'gmt-drop-cap-',
                'frontend_available' => true,
                'frontend_name' => 'drop_cap',
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerTextEditorStyle(AbstractWidget $widget, string $prefix = self::NAME . '_',
                                                   string         $cssTarget = '.gmt-text-editor', array $args = [])
    {
        $widget->addResponsiveControl(
            $prefix . 'align',
            $args + [
                'label' => __('Alignment'),
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
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'color',
            $args + [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' =>  $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $textColumns = range(1, 10);
        $textColumns = array_combine($textColumns, $textColumns);
        $textColumns[''] = __('Default');

        $widget->addResponsiveControl(
            $prefix . 'columns',
            $args + [
                'label' => __('Columns'),
                'type' => Controls::SELECT,
                'separator' => 'before',
                'options' => $textColumns,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'columns: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'column_gap',
            $args + [
                'label' => __('Columns Gap'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'vw' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    '%' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'vw' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'em' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerDropCapStyles(AbstractWidget $widget, string $prefix = self::NAME . '_',
                                                 string         $cssTarget = '.gmt-drop-cap', array $args = [])
    {

        $widget->addControl(
            $prefix . 'drop_cap_view',
            $args + [
                'label' => __('View'),
                'type' => Controls::SELECT,
                'options' => [
                    'default' => __('Default'),
                    'stacked' => __('Stacked'),
                    'framed' => __('Framed'),
                ],
                'default' => 'default',
                'prefix_class' => 'gmt-drop-cap-view-',
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_primary_color',
            $args + [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-framed ' . $cssTarget . ', {{WRAPPER}}.gmt-drop-cap-view-default ' . $cssTarget => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $conditionArgs = [
            'condition' => [
                self::NAME . '_drop_cap_view!' => 'default',
            ],
        ];

        if (isset($args['condition'])) {
            $conditionArgs = array_merge($conditionArgs, $args['condition']);
        }

        $widget->addControl(
            $prefix . 'drop_cap_secondary_color',
            $conditionArgs + $args + [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-framed ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_size',
            $conditionArgs + $args + [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_space',
            $args + [
                'label' => __('Space'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} ' . $cssTarget => 'margin-right: {{SIZE}}{{UNIT}};',
                    'body.rtl {{WRAPPER}} ' . $cssTarget => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_border_radius',
            $args + [
                'label' => __('Border Radius'),
                'type' => Controls::SLIDER,
                'size_units' => [ '%', 'px' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        unset($conditionArgs['condition'][self::NAME . '_drop_cap_view!']);

        $conditionArgs['condition'][self::NAME . '_drop_cap_view'] = 'framed';

        $widget->addControl(
            $prefix . 'drop_cap_border_width',
            $conditionArgs + $args + [
                'label' => __('Border Width'),
                'type' => Controls::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            $args + [
                'name' => $prefix . 'drop_cap_typography',
                'selector' => '{{WRAPPER}} .gmt-drop-cap-letter',
                'exclude' => [
                    'letter_spacing',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_editor',
            [
                'label' => __('Text Editor'),
            ]
        );

        self::registerTextEditorInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Text Editor'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTextEditorStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_drop_cap',
            [
                'label' => __('Drop Cap'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_drop_cap' => 'yes',
                ],
            ]
        );

        self::registerDropCapStyles($this, self::NAME . '_', '.gmt-drop-cap', [
            'condition' => [
                self::NAME . '_drop_cap' => 'yes',
            ],
        ]);

        $this->endControlsSection();
    }


    /**
     * @inheritDoc
     */
    public function renderPlainContent()
    {
        // In plain mode, render without shortcode
        echo $this->getSettings('editor');
    }
}
