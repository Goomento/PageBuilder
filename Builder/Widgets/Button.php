<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;

class Button extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'button';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/button.phtml';

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
    public function getTitle()
    {
        return __('Button');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-mouse-pointer';
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * Share of button interface
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @throws BuilderException
     */
    public static function registerButtonInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
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
                'prefix_class' => 'gmt-button-',
            ]
        );

        $widget->addControl(
            $prefix . 'text',
            [
                'label' => __('Text'),
                'type' => Controls::TEXT,
                'default' => __('Click here'),
                'placeholder' => __('Click here'),
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'placeholder' => __('https://your-link.com'),
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'align',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left'    => [
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
                'prefix_class' => 'gmt%s-align-',
                'default' => '',
            ]
        );

        $widget->addControl(
            $prefix . 'size',
            [
                'label' => __('Size'),
                'type' => Controls::SELECT,
                'default' => 'sm',
                'options' => [
                    'xs' => __('Extra Small'),
                    'sm' => __('Small'),
                    'md' => __('Medium'),
                    'lg' => __('Large'),
                    'xl' => __('Extra Large'),
                ],
                'style_transfer' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix .  'icon_align',
            [
                'label' => __('Icon Position'),
                'type' => Controls::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'condition' => [
                    $prefix . 'selected_icon.value!' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'css_id',
            [
                'label' => __('Button CSS ID'),
                'type' => Controls::TEXT,
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'label_block' => false,
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this element is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.'),
                'separator' => 'before',
            ]
        );
    }

    /**
     * Share button label styling
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerButtonBodyStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-button'
    ) {
        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => $prefix . 'border',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'separator' => 'before',
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
            ]
        );

        $widget->addGroupControl(
            BoxShadowGroup::NAME,
            [
                'name' => $prefix . 'box_shadow',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'text_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );
    }

    /**
     * Share button icon styling
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerButtonIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-button'
    ) {
        $widget->addControl(
            $prefix . 'icon_indent',
            [
                'label' => __('Icon Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * Share button label styling
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerButtonLabelStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-button'
    ) {
        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} ' . $cssTarget ,
            ]
        );

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            [
                'name' => $prefix . 'text_shadow',
                'selector' => '{{WRAPPER}}  ' . $cssTarget,
            ]
        );

        $widget->startControlsTabs($prefix . 'tabs_button_style');

        $widget->startControlsTab(
            $prefix . 'tab_button_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $widget->addControl(
            $prefix . 'text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  ' . $cssTarget => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}}  ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            $prefix . 'tab_button_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $widget->addControl(
            $prefix . 'hover_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  ' . $cssTarget . ':hover, {{WRAPPER}} ' . $cssTarget . ':focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_hover_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover, {{WRAPPER}} ' . $cssTarget . ':focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover, {{WRAPPER}} ' . $cssTarget . ':focus' => 'border-color: {{VALUE}};',
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
    }


    /**
     * Share of button styling
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws BuilderException
     */
    public static function registerButtonStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-button'
    ) {
        self::registerButtonLabelStyle($widget, $prefix, $cssTarget);
        self::registerButtonBodyStyle($widget, $prefix, $cssTarget);
        self::registerButtonIconStyle($widget, $prefix, $cssTarget);
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_button',
            [
                'label' => __('Button'),
            ]
        );

        self::registerButtonInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Button'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerButtonStyle($this, 'button_');

        $this->endControlsSection();
    }
}
