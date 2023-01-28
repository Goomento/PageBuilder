<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;

class Counter extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'counter';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/counter.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Counter');
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
        return 'fas fa-sort-numeric-up-alt';
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return [ 'goomento-widget-counter' ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'counter' ];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerCounterInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'starting_number',
            [
                'label' => __('Starting Number'),
                'type' => Controls::NUMBER,
                'default' => 0,
            ]
        );

        $widget->addControl(
            $prefix . 'ending_number',
            [
                'label' => __('Ending Number'),
                'type' => Controls::NUMBER,
                'default' => 100,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'prefix',
            [
                'label' => __('Number Prefix'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => 1,
            ]
        );

        $widget->addControl(
            $prefix . 'suffix',
            [
                'label' => __('Number Suffix'),
                'type' => Controls::TEXT,
                'default' => '',
                'placeholder' => __('Plus'),
            ]
        );

        $widget->addControl(
            $prefix . 'duration',
            [
                'label' => __('Animation Duration'),
                'type' => Controls::NUMBER,
                'default' => 2000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $widget->addControl(
            $prefix . 'thousand_separator',
            [
                'label' => __('Thousand Separator'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $widget->addControl(
            $prefix . 'thousand_separator_char',
            [
                'label' => __('Separator'),
                'type' => Controls::SELECT,
                'condition' => [
                    $prefix . 'separator' => 'yes',
                ],
                'options' => [
                    '' => 'Default',
                    '.' => 'Dot',
                    ' ' => 'Space',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'label_block' => true,
                'default' => __('Cool Number'),
                'placeholder' => __('Cool Number'),
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerNumberStyles(
        AbstractWidget $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-counter-number-wrapper'
    ) {
        $widget->addControl(
            $prefix . 'number_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'typography_number',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerTitleStyles(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-counter-title'
    ) {
        $widget->addControl(
            $prefix . 'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'typography_title',
                'scheme' => Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_counter',
            [
                'label' => __('Counter'),
            ]
        );

        self::registerCounterInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_number',
            [
                'label' => __('Number'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerNumberStyles($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTitleStyles($this);

        $this->endControlsSection();
    }
}
