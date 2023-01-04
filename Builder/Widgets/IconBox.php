<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;

class IconBox extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'icon-box';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/icon_box.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Icon Box');
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
        return 'fas fa-star';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'icon box', 'icon' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerIconBoxInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        Icon::registerIconInterface($widget, $prefix . 'icon_');

        $widget->addControl(
            $prefix . 'shape',
            [
                'label' => __('Shape'),
                'type' => Controls::SELECT,
                'options' => [
                    'circle' => __('Circle'),
                    'square' => __('Square'),
                ],
                'default' => 'circle',
                'condition' => [
                    $prefix . 'view!' => 'default',
                    $prefix . 'icon_selected_icon.value!' => '',
                ],
                'prefix_class' => 'gmt-shape-',
            ]
        );

        $widget->addControl(
            $prefix . 'title_text',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('This is the heading'),
                'placeholder' => __('Enter your title'),
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'description_text',
            [
                'label' => '',
                'type' => Controls::TEXTAREA,
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your description'),
                'rows' => 10,
                'separator' => 'none',
                'show_label' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'position',
            [
                'label' => __('Icon Position'),
                'type' => Controls::CHOOSE,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-angle-left',
                    ],
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'fas fa-angle-up',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-angle-right',
                    ],
                ],
                'prefix_class' => 'gmt-position-',
                'toggle' => false,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => $prefix . 'icon_selected_icon.value',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_size',
            [
                'label' => __('Title HTML Tag'),
                'type' => Controls::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssIconTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerIconBoxIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-icon',
        string $cssIconTarget = '.gmt-icon-box-icon'
    ) {
        $widget->startControlsTabs('icon_colors');

        $widget->startControlsTab(
            $prefix . 'icon_colors_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $widget->addControl(
            $prefix . 'primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ', {{WRAPPER}}.gmt-view-default ' . $cssTarget => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            'icon_colors_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $widget->addControl(
            $prefix . 'hover_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget . ':hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ':hover, {{WRAPPER}}.gmt-view-default .gmt-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ':hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget . ':hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $widget->endControlsTab();

        $widget->endControlsTabs();

        $widget->addResponsiveControl(
            $prefix . 'icon_space',
            [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-position-right ' . $cssIconTarget => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-left ' . $cssIconTarget => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-top ' . $cssIconTarget => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} ' . $cssIconTarget => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'icon_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'rotate',
            [
                'label' => __('Rotate'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'view' => 'framed',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssTitleTarget
     * @param string $cssDescriptionTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerIconBoxContentStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-icon-box-wrapper',
        string $cssTitleTarget = '.gmt-icon-box-content .gmt-icon-box-title',
        string $cssDescriptionTarget = '.gmt-icon-box-content .gmt-icon-box-description'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'text_align',
            [
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
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'content_vertical_alignment',
            [
                'label' => __('Vertical Alignment'),
                'type' => Controls::SELECT,
                'options' => [
                    'top' => __('Top'),
                    'middle' => __('Middle'),
                    'bottom' => __('Bottom'),
                ],
                'default' => 'top',
                'prefix_class' => 'gmt-vertical-align-',
                'condition' => [
                    $prefix . 'position' => ['left', 'right']
                ]
            ]
        );

        $widget->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'title_bottom_space',
            [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTitleTarget => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTitleTarget => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'title_typography',
                'selector' => '{{WRAPPER}} ' . $cssTitleTarget,
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $widget->addControl(
            $prefix . 'heading_description',
            [
                'label' => __('Description'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'description_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssDescriptionTarget => 'color: {{VALUE}};',
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
                'name' => $prefix . 'description_typography',
                'selector' => '{{WRAPPER}} ' . $cssDescriptionTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_icon',
            [
                'label' => __('Icon Box'),
            ]
        );

        self::registerIconBoxInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => __('Icon'),
                'tab'   => Controls::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => self::NAME . '_icon_selected_icon.value',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        self::registerIconBoxIconStyle($this, self::buildPrefixKey('icon'));

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Content'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerIconBoxContentStyle($this);

        $this->endControlsSection();
    }
}
