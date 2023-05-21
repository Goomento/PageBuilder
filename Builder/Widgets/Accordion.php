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

class Accordion extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'accordion';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/accordion.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Accordion');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-plus';
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
        return ['goomento-widget-accordion'];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'accordion', 'tabs', 'toggle' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerAccordionItemInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'tab_title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('Lorem ipsum dolor sit amet'),
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'tab_content',
            [
                'label' => __('Content'),
                'type' => Controls::WYSIWYG,
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'show_label' => false,
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerAccordionInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $repeater = new Repeater();

        self::registerAccordionItemInterface($repeater, $prefix);

        $widget->addControl(
            $prefix . 'tabs',
            [
                'label' => __('Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        $prefix . 'tab_title' => __('Lorem ipsum dolor sit amet #1'),
                        $prefix . 'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        $prefix . 'tab_title' => __('Lorem ipsum dolor sit amet #2'),
                        $prefix . 'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                ],
                'title_field' => '{{{ ' . $prefix . 'tab_title }}}',
            ]
        );

        $widget->addControl(
            $prefix . 'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'separator' => 'before',
                'default' => [
                    'value' => 'fas fa-plus',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'selected_active_icon',
            [
                'label' => __('Active Icon'),
                'type' => Controls::ICONS,
                'default' => [
                    'value' => 'fas fa-minus',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    $prefix . 'selected_icon.value!' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_html_tag',
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
                ],
                'default' => 'div',
                'separator' => 'before',
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
    public static function registerAccordionItemStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-accordion .gmt-accordion-item'
    ) {
        $widget->addControl(
            $prefix . 'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-tab-title.gmt-active' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-tab-content' => 'border-top-color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-tab-title.gmt-active' => 'border-bottom-color: {{VALUE}};',
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
    public static function registerAccordionTitleStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-accordion .gmt-tab-title'
    ) {
        $widget->addControl(
            $prefix . 'title_background',
            [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} ' . $cssTarget . '.gmt-active' => 'color: {{VALUE}};',
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
                'name' => $prefix . 'title_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'title_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssActiveTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerAccordionIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-accordion .gmt-accordion-icon',
        string $cssActiveTarget = '.gmt-accordion .gmt-tab-title.gmt-active .gmt-accordion-icon'
    ) {
        $widget->addControl(
            $prefix . 'icon_align',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Start'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'right' => [
                        'title' => __('End'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'default' => DataHelper::isRtl() ? 'right' : 'left',
                'toggle' => false,
                'label_block' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ' svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'icon_active_color',
            [
                'label' => __('Active Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssActiveTarget . ' i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssActiveTarget . ' svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_space',
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
                    '{{WRAPPER}} ' . $cssTarget . '-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . '-right' => 'margin-left: {{SIZE}}{{UNIT}};',
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
    public static function registerAccordionContentStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-accordion .gmt-tab-content'
    ) {
        $widget->addControl(
            $prefix . 'content_background_color',
            [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'content_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
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
                'name' => $prefix . 'content_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'content_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'section_title',
            [
                'label' => __('Accordion'),
            ]
        );

        self::registerAccordionInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('General'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerAccordionItemStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerAccordionTitleStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_icon',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_selected_icon.value!' => '',
                ],
            ]
        );

        self::registerAccordionIconStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_content',
            [
                'label' => __('Content'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerAccordionContentStyle($this);

        $this->endControlsSection();
    }
}
