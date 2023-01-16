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
use Goomento\PageBuilder\Exception\BuilderException;

class Progress extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'progress';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/process.phtml';

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
        return ['goomento-widget-progress'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Progress Bar');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-percentage';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'progress', 'bar', 'process' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerProcessInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your title'),
                'default' => __('My Skill'),
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'percent',
            [
                'label' => __('Percentage'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'display_percentage',
            [
            'label' => __('Display Percentage'),
            'type' => Controls::SELECT,
            'default' => 'show',
            'options' => [
                'show' => __('Show'),
                'hide' => __('Hide'),
            ],
            ]
        );

        $widget->addControl(
            $prefix . 'inner_text',
            [
                'label' => __('Inner Text'),
                'type' => Controls::TEXT,
                'placeholder' => __('e.g. Web Designer'),
                'default' => __('Web Designer'),
                'label_block' => true,
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws BuilderException
     */
    public static function registerProcessStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-progress-bar'
    ) {
        $widget->addControl(
            $prefix . 'bar_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'bar_bg_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'bar_height',
            [
                'label' => __('Height'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'bar_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'inner_text_heading',
            [
                'label' => __('Inner Text'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'bar_inline_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => 'bar_inner_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'exclude' => [
                    'line_height',
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
            'section_progress',
            [
                'label' => __('Progress Bar'),
            ]
        );

        self::registerProcessInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_progress_style',
            [
                'label' => __('Progress Bar'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerProcessStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title Style'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_', '.gmt-title');

        $this->endControlsSection();
    }
}
