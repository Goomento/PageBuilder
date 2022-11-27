<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\DataHelper;

class Accordion extends AbstractWidget
{
    const NAME = 'accordion';

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
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'accordion', 'tabs', 'toggle' ];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerAccordionContentInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'tab_title',
            $args + [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('Accordion Title'),
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'tab_content',
            $args + [
                'label' => __('Content'),
                'type' => Controls::WYSIWYG,
                'default' => __('Accordion Content'),
                'show_label' => false,
            ]
        );
    }


    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerAccordionInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $repeater = new Repeater;

        self::registerAccordionContentInterface($repeater);

        $widget->addControl(
            $prefix . 'tabs',
            $args + [
                'label' => __('Accordion Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'tab_title' => __('Accordion #1'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        'tab_title' => __('Accordion #2'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                ],
                'title_field' => '{{{ ' . $prefix . 'tab_title }}}',
            ]
        );

        $widget->addControl(
            $prefix . 'selected_icon',
            $args + [
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
            $args + [
                'label' => __('Active Icon'),
                'type' => Controls::ICONS,
                'default' => [
                    'value' => 'fas fa-minus',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    $prefix . 'selected_icon[value]!' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_html_tag',
            $args + [
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
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerAccordionItemStyle(
        AbstractElement $widget,
        string $prefix = self::NAME . '_',
        string          $cssTarget = '.gmt-accordion .gmt-accordion-item',
        array $args = []
    ) {
        $widget->addControl(
            $prefix . 'border_width',
            $args + [
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
            $args + [
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
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerAccordionTitleStyle(
        AbstractElement $widget,
        string $prefix = self::NAME . '_',
        string          $cssTarget = '.gmt-accordion .gmt-tab-title',
        array $args = []
    ) {
        $widget->addControl(
            $prefix . 'title_background',
            $args + [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title_color',
            $args + [
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
            $args + [
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
            $args + [
                'name' => $prefix . 'title_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'title_padding',
            $args + [
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
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerAccordionIconStyle(
        AbstractElement $widget,
        string          $prefix = self::NAME . '_',
        string          $cssTarget = '.gmt-accordion .gmt-accordion-icon',
        string          $cssActiveTarget = '.gmt-accordion .gmt-tab-title.gmt-active .gmt-accordion-icon',
        array           $args = []
    ) {
        $widget->addControl(
            $prefix . 'icon_align',
            $args + [
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
            $args + [
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
            $args + [
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
            $args + [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . '.gmt-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . '.gmt-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerAccordionContentStyle(
        AbstractElement $widget,
        string $prefix = self::NAME . '_',
        string          $cssTarget = '.gmt-accordion .gmt-tab-content',
        array $args = []
    ) {
        $widget->addControl(
            $prefix . 'content_background_color',
            $args + [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'content_color',
            $args + [
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
            $args + [
                'name' => $prefix . 'content_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'content_padding',
            $args + [
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
                'label' => __('Accordion'),
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
                    'accordion_selected_icon[value]!' => '',
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
