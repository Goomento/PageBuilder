<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\BackgroundGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Helper\DataHelper;

class Common extends AbstractWidget
{
    const NAME = 'common';

    /**
     * @inheritDoc
     */
    public function showInPanel()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {

        // Section Identify
        $this->startControlsSection(
            'section_identify',
            [
                'label' => __('Identify'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addControl(
            '_element_id',
            [
                'label' => __('CSS ID'),
                'type' => Controls::TEXT,
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this element is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.'),
                'label_block' => false,
                'style_transfer' => false,
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        $this->addControl(
            '_css_classes',
            [
                'label' => __('CSS Classes'),
                'type' => Controls::TEXT,
                'prefix_class' => '',
                'title' => __('Add your custom class WITHOUT the dot. e.g: my-class'),
                'classes' => 'gmt-control-direction-ltr',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            '_section_style',
            [
                'label' => __('Advanced'),
                'tab' => Controls::TAB_ADVANCED,
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

        $this->addResponsiveControl(
            '_margin',
            [
                'label' => __('Margin'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-widget-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-widget-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            '_z_index',
            [
                'label' => __('Z-Index'),
                'type' => Controls::NUMBER,
                'min' => 0,
                'selectors' => [
                    '{{WRAPPER}}' => 'z-index: {{VALUE}};',
                ],
                'label_block' => false,
                'separator' => 'before',
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

        $this->addControl(
            '_hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $this->addResponsiveControl(
            '_animation',
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
                    '_animation!' => '',
                ],
            ]
        );

        $this->addControl(
            '_animation_delay',
            [
                'label' => __('Animation Delay') . ' (ms)',
                'type' => Controls::NUMBER,
                'default' => '',
                'min' => 0,
                'step' => 100,
                'condition' => [
                    '_animation!' => '',
                ],
                'render_type' => 'none',
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            '_section_background',
            [
                'label' => __('Background'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->startControlsTabs('_tabs_background');

        $this->startControlsTab(
            '_tab_background_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            BackgroundGroup::NAME,
            [
                'name' => '_background',
                'selector' => '{{WRAPPER}} > .gmt-widget-container',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            '_tab_background_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            BackgroundGroup::NAME,
            [
                'name' => '_background_hover',
                'selector' => '{{WRAPPER}}:hover .gmt-widget-container',
            ]
        );

        $this->addControl(
            '_background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
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

        $this->startControlsSection(
            '_section_border',
            [
                'label' => __('Border'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->startControlsTabs('_tabs_border');

        $this->startControlsTab(
            '_tab_border_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => '_border',
                'selector' => '{{WRAPPER}} > .gmt-widget-container',
            ]
        );

        $this->addResponsiveControl(
            '_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} > .gmt-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadowGroup::NAME,
            [
                'name' => '_box_shadow',
                'selector' => '{{WRAPPER}} > .gmt-widget-container',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            '_tab_border_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => '_border_hover',
                'selector' => '{{WRAPPER}}:hover .gmt-widget-container',
            ]
        );

        $this->addResponsiveControl(
            '_border_radius_hover',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}}:hover > .gmt-widget-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadowGroup::NAME,
            [
                'name' => '_box_shadow_hover',
                'selector' => '{{WRAPPER}}:hover .gmt-widget-container',
            ]
        );

        $this->addControl(
            '_border_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'separator' => 'before',
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-widget-container' => 'transition: background {{_background_hover_transition.SIZE}}s, border {{SIZE}}s, border-radius {{SIZE}}s, box-shadow {{SIZE}}s',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->endControlsSection();

        $this->startControlsSection(
            '_section_position',
            [
                'label' => __('Custom Positioning'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addResponsiveControl(
            '_element_width',
            [
                'label' => __('Width'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'inherit' => __('Full Width') . ' (100%)',
                    'auto' => __('Inline') . ' (auto)',
                    'initial' => __('Custom'),
                ],
                'selectors_dictionary' => [
                    'inherit' => '100%',
                ],
                'prefix_class' => 'gmt-widget%s__width-',
                'selectors' => [
                    '{{WRAPPER}}' => 'width: {{VALUE}}; max-width: {{VALUE}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_element_custom_width',
            [
                'label' => __('Custom Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    '_element_width' => 'initial',
                ],
                'device_args' => [
                    ControlsStack::RESPONSIVE_TABLET => [
                        'condition' => [
                            '_element_width_tablet' => [ 'initial' ],
                        ],
                    ],
                    ControlsStack::RESPONSIVE_MOBILE => [
                        'condition' => [
                            '_element_width_mobile' => [ 'initial' ],
                        ],
                    ],
                ],
                'size_units' => [ 'px', '%', 'vw' ],
                'selectors' => [
                    '{{WRAPPER}}' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_element_vertical_align',
            [
                'label' => __('Vertical Align'),
                'type' => Controls::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Start'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('End'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'condition' => [
                    '_element_width!' => '',
                    '_position' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'align-self: {{VALUE}}',
                ],
            ]
        );

        $this->addControl(
            '_position',
            [
                'label' => __('Position'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'absolute' => __('Absolute'),
                    'fixed' => __('Fixed'),
                ],
                'prefix_class' => 'gmt-',
                'frontend_available' => true,
            ]
        );

        $start = DataHelper::isRtl() ? __('Right') : __('Left');
        $end = ! DataHelper::isRtl() ? __('Right') : __('Left');

        $this->addControl(
            '_offset_orientation_h',
            [
                'label' => __('Horizontal Orientation'),
                'type' => Controls::CHOOSE,
                'label_block' => false,
                'toggle' => false,
                'default' => 'start',
                'options' => [
                    'start' => [
                        'title' => $start,
                        'icon' => 'fas fa-chevron-left',
                    ],
                    'end' => [
                        'title' => $end,
                        'icon' => 'fas fa-chevron-right',
                    ],
                ],
                'classes' => 'gmt-control-start-end',
                'render_type' => 'ui',
                'condition' => [
                    '_position!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_offset_x',
            [
                'label' => __('Offset'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => '0',
                ],
                'size_units' => [ 'px', '%', 'vw', 'vh' ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_h!' => 'end',
                    '_position!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_offset_x_end',
            [
                'label' => __('Offset'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 0.1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'default' => [
                    'size' => '0',
                ],
                'size_units' => [ 'px', '%', 'vw', 'vh' ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}}' => 'right: {{SIZE}}{{UNIT}}',
                    'body.rtl {{WRAPPER}}' => 'left: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_h' => 'end',
                    '_position!' => '',
                ],
            ]
        );

        $this->addControl(
            '_offset_orientation_v',
            [
                'label' => __('Vertical Orientation'),
                'type' => Controls::CHOOSE,
                'label_block' => false,
                'toggle' => false,
                'default' => 'start',
                'options' => [
                    'start' => [
                        'title' => __('Top'),
                        'icon' => 'fas fa-chevron-up',
                    ],
                    'end' => [
                        'title' => __('Bottom'),
                        'icon' => 'fas fa-chevron-down',
                    ],
                ],
                'render_type' => 'ui',
                'condition' => [
                    '_position!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_offset_y',
            [
                'label' => __('Offset'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'size_units' => [ 'px', '%', 'vh', 'vw' ],
                'default' => [
                    'size' => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'top: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_v!' => 'end',
                    '_position!' => '',
                ],
            ]
        );

        $this->addResponsiveControl(
            '_offset_y_end',
            [
                'label' => __('Offset'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => -1000,
                        'max' => 1000,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vh' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                    'vw' => [
                        'min' => -200,
                        'max' => 200,
                    ],
                ],
                'size_units' => [ 'px', '%', 'vh', 'vw' ],
                'default' => [
                    'size' => '0',
                ],
                'selectors' => [
                    '{{WRAPPER}}' => 'bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    '_offset_orientation_v' => 'end',
                    '_position!' => '',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            '_section_responsive',
            [
                'label' => __('Responsive'),
                'tab' => Controls::TAB_ADVANCED,
            ]
        );

        $this->addControl(
            'responsive_description',
            [
                'raw' => __('Responsive visibility will take effect only on preview or live page, and not while editing in Goomento.'),
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
                'label_on' => 'Hide',
                'label_off' => 'Show',
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
                'label_on' => 'Hide',
                'label_off' => 'Show',
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
                'label_on' => 'Hide',
                'label_off' => 'Show',
                'return_value' => 'hidden-phone',
            ]
        );

        $this->endControlsSection();

        Controls::addExtendControls($this);
    }
}
