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

class Tabs extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'tabs';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/tabs.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Tabs');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-caret-square-down';
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheirtDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-tabs'];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'tabs', 'accordion', 'toggle' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerTabsInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $repeater = new Repeater;

        Accordion::registerAccordionItemInterface($repeater, $prefix);

        $widget->addControl(
            $prefix . 'tabs',
            [
                'label' => __('Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        $prefix . 'tab_title' => __('Tab #1'),
                        $prefix . 'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        $prefix . 'tab_title' => __('Tab #2'),
                        $prefix . 'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                ],
                'title_field' => '{{{ ' . $prefix . 'tab_title }}}',
            ]
        );

        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
                'prefix_class' => 'gmt-tabs-view-',
                'separator' => 'before',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerTabsStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'navigation_width',
            [
                'label' => __('Navigation Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-tabs-wrapper' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    $prefix . 'type' => 'vertical',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title, {{WRAPPER}} .gmt-tab-title:before, {{WRAPPER}} .gmt-tab-title:after, {{WRAPPER}} .gmt-tab-content, {{WRAPPER}} .gmt-tabs-content-wrapper' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-mobile-title, {{WRAPPER}} .gmt-tab-desktop-title.gmt-active, {{WRAPPER}} .gmt-tab-title:before, {{WRAPPER}} .gmt-tab-title:after, {{WRAPPER}} .gmt-tab-content, {{WRAPPER}} .gmt-tabs-content-wrapper' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-desktop-title.gmt-active' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-tabs-content-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'heading_title',
            [
                'label' => __('Title'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'tab_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'tab_active_color',
            [
                'label' => __('Active Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title.gmt-active' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_4,
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'tab_typography',
                'selector' => '{{WRAPPER}} .gmt-tab-title',
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $widget->addControl(
            $prefix . 'heading_content',
            [
                'label' => __('Content'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'content_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-content' => 'color: {{VALUE}};',
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
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .gmt-tab-content',
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
            'section_tabs',
            [
                'label' => __('Tabs'),
            ]
        );

        self::registerTabsInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_tabs_style',
            [
                'label' => __('Tabs'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTabsStyle($this);

        $this->endControlsSection();
    }
}
