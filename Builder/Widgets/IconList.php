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
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class IconList extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'icon_list';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/icon_list.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Icon List');
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
        return 'fas fa-icons';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'icon list', 'icon', 'list' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerIconItemInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'text',
            [
                'label' => __('Text'),
                'type' => Controls::TEXT,
                'label_block' => true,
                'placeholder' => __('List Item'),
                'default' => __('List Item')
            ]
        );

        $widget->addControl(
            $prefix . 'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'label_block' => true,
                'default' => [
                    'value' => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'label_block' => true,
                'placeholder' => __('https://your-link.com'),
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerIconListInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'view',
            [
                'label' => __('Layout'),
                'type' => Controls::CHOOSE,
                'default' => 'traditional',
                'options' => [
                    'traditional' => [
                        'title' => __('Default'),
                        'icon' => 'fas fa-list-ul',
                    ],
                    'inline' => [
                        'title' => __('Inline'),
                        'icon' => 'fas fa-ellipsis-h',
                    ],
                ],
                'render_type' => 'template',
                'classes' => 'gmt-control-start-end',
                'label_block' => false,
                'style_transfer' => true,
                'prefix_class' => 'gmt-icon-list--layout-',
            ]
        );

        $repeater = new Repeater;

        self::registerIconItemInterface($repeater, '');

        $widget->addControl(
            $prefix . 'icon_list',
            [
                'label' => '',
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'text' => __('List Item #1'),
                        'selected_icon' => [
                            'value' => 'fas fa-check',
                            'library' => 'fa-solid',
                        ],
                    ],
                    [
                        'text' => __('List Item #2'),
                        'selected_icon' => [
                            'value' => 'fas fa-times',
                            'library' => 'fa-solid',
                        ],
                    ],
                    [
                        'text' => __('List Item #3'),
                        'selected_icon' => [
                            'value' => 'fas fa-dot-circle',
                            'library' => 'fa-solid',
                        ],
                    ],
                ],
                'title_field' => '{{{ goomento.helpers.renderIcon( this, selected_icon, {}, "i", "panel" ) ||
\'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} {{{ text }}}',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssListContainerTarget
     * @param string $cssItemTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerIconListStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssListContainerTarget = '.gmt-icon-list-items',
        string $cssItemTarget = '.gmt-icon-list-item'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'space_between',
            [
                'label' => __('Space Between'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssListContainerTarget . ':not(.gmt-inline-items) ' . $cssItemTarget . ':not(:last-child)' => 'padding-bottom: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} ' . $cssListContainerTarget . ':not(.gmt-inline-items) ' . $cssItemTarget . ':not(:first-child)' => 'margin-top: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} ' . $cssListContainerTarget . '.gmt-inline-items ' . $cssItemTarget => 'margin-right: calc({{SIZE}}{{UNIT}}/2); margin-left: calc({{SIZE}}{{UNIT}}/2)',
                    '{{WRAPPER}} ' . $cssListContainerTarget . '.gmt-inline-items' => 'margin-right: calc(-{{SIZE}}{{UNIT}}/2); margin-left: calc(-{{SIZE}}{{UNIT}}/2)',
                    'body.rtl {{WRAPPER}} ' . $cssListContainerTarget . '.gmt-inline-items ' . $cssItemTarget . ':after' => 'left: calc(-{{SIZE}}{{UNIT}}/2)',
                    'body:not(.rtl) {{WRAPPER}} ' . $cssListContainerTarget . '.gmt-inline-items ' . $cssItemTarget . ':after' => 'right: calc(-{{SIZE}}{{UNIT}}/2)',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_align',
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
                ],
                'prefix_class' => 'gmt%s-align-',
            ]
        );

        $widget->addControl(
            $prefix . 'divider',
            [
                'label' => __('Divider'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'selectors' => [
                    '{{WRAPPER}} ' . $cssItemTarget . ':not(:last-child):after' => 'content: ""',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'divider_style',
            [
                'label' => __('Style'),
                'type' => Controls::SELECT,
                'options' => [
                    'solid' => __('Solid'),
                    'double' => __('Double'),
                    'dotted' => __('Dotted'),
                    'dashed' => __('Dashed'),
                ],
                'default' => 'solid',
                'condition' => [
                    $prefix . 'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssListContainerTarget . ':not(.gmt-inline-items) ' . $cssItemTarget . ':not(:last-child):after' => 'border-top-style: {{VALUE}}',
                    '{{WRAPPER}} ' . $cssListContainerTarget . '.gmt-inline-items ' . $cssItemTarget . ':not(:last-child):after' => 'border-left-style: {{VALUE}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'divider_weight',
            [
                'label' => __('Weight'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 20,
                    ],
                ],
                'condition' => [
                    $prefix . 'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssListContainerTarget . ':not(.gmt-inline-items) ' . $cssItemTarget . ':not(:last-child):after' => 'border-top-width: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .gmt-inline-items ' . $cssItemTarget . ':not(:last-child):after' => 'border-left-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'divider_width',
            [
                'label' => __('Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'condition' => [
                    $prefix . 'divider' => 'yes',
                    $prefix . 'view!' => 'inline',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssItemTarget . ':not(:last-child):after' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'divider_height',
            [
                'label' => __('Height'),
                'type' => Controls::SLIDER,
                'size_units' => [ '%', 'px' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'condition' => [
                    $prefix . 'divider' => 'yes',
                    $prefix . 'view' => 'inline',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssItemTarget . ':not(:last-child):after' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'divider_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '#ddd',
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
                'condition' => [
                    $prefix . 'divider' => 'yes',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssItemTarget . ':not(:last-child):after' => 'border-color: {{VALUE}}',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerIconIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-icon-list-icon'
    ) {
        $widget->addControl(
            $prefix . 'icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ' svg' => 'fill: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'icon_color_hover',
            [
                'label' => __('Hover'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-list-item:hover ' . $cssTarget . ' i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-icon-list-item:hover ' . $cssTarget . ' svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 14,
                ],
                'range' => [
                    'px' => [
                        'min' => 6,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . ' svg' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_self_align',
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
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'text-align: {{VALUE}};',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssItemTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerIconTextStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-icon-list-text',
        string $cssItemTarget = '.gmt-icon-list-item'
    ) {
        $widget->addControl(
            $prefix . 'text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_2,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'text_color_hover',
            [
                'label' => __('Hover'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssItemTarget . ':hover ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'text_indent',
            [
                'label' => __('Text Indent'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => DataHelper::isRtl() ? 'padding-right: {{SIZE}}{{UNIT}};' : 'padding-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix .  'icon_typography',
                'selector' => '{{WRAPPER}} ' . $cssItemTarget,
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
                'label' => __('Icon List'),
            ]
        );

        self::registerIconListInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_icon_list',
            [
                'label' => __('List'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerIconListStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_icon_style',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerIconIconStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_text_style',
            [
                'label' => __('Text'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerIconTextStyle($this);

        $this->endControlsSection();
    }
}
