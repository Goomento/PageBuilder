<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;

class Toggle extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'toggle';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/toggle.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Toggle');
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
        return ['goomento-widget-toggle'];
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-caret-square-right';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'tabs', 'accordion', 'toggle' ];
    }


    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_toggle',
            [
                'label' => __('Toggle'),
            ]
        );

        Accordion::registerAccordionInterface($this, self::buildPrefixKey());

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style',
            [
                'label' => __('Toggle'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'border_width',
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
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'space_between',
            [
                'label' => __('Space Between'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-toggle-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadowGroup::NAME,
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .gmt-toggle .gmt-toggle-item',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Accordion::registerAccordionTitleStyle(
            $this,
            self::buildPrefixKey(),
            '.gmt-toggle .gmt-tab-title'
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_icon',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::buildPrefixKey() . 'selected_icon.value!' => '',
                ],
            ]
        );

        Accordion::registerAccordionIconStyle(
            $this,
            self::buildPrefixKey(),
            '.gmt-toggle .gmt-toggle-icon',
            '.gmt-toggle .gmt-tab-title.gmt-active .gmt-toggle-icon'
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_content',
            [
                'label' => __('Content'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Accordion::registerAccordionContentStyle(
            $this,
            self::buildPrefixKey(),
            '.gmt-toggle .gmt-tab-content'
        );

        $this->endControlsSection();
    }
}
