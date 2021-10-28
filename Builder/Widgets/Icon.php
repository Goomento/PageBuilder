<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;

class Icon extends AbstractWidget
{

    const NAME = 'icon';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/icon.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Icon');
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
        return 'far fa-star';
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'icon' ];
    }

    /**
     * Share icon interface
     *
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     */
    public static function registerIconInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'selected_icon',
            $args + [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'view',
            $args + [
                'label' => __('View'),
                'type' => Controls::SELECT,
                'options' => [
                    'default' => __('Default'),
                    'stacked' => __('Stacked'),
                    'framed' => __('Framed'),
                ],
                'default' => 'default',
                'prefix_class' => 'gmt-view-',
            ]
        );

        $widget->addControl(
            $prefix .  'shape',
            $args + [
                'label' => __('Shape'),
                'type' => Controls::SELECT,
                'options' => [
                    'circle' => __('Circle'),
                    'square' => __('Square'),
                ],
                'default' => 'circle',
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
                'prefix_class' => 'gmt-shape-',
            ]
        );

        $widget->addControl(
            $prefix . 'link',
           $args + [
                'label' => __('Link'),
                'type' => Controls::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @param string $cssTarget
     */
    public static function registerIconStyle(
        AbstractElement $widget,
        string          $prefix = self::NAME . '_',
        array           $args = [],
        string          $cssTarget = '.gmt-icon'
    )
    {
        $widget->startControlsTabs($prefix . 'colors');

        $widget->startControlsTab(
            $prefix . 'colors_normal',
            $args + [
                'label' => __('Normal'),
            ]
        );

        $widget->addControl(
            $prefix . 'primary_color',
            $args + [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ', {{WRAPPER}}.gmt-view-default .gmt-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'secondary_color',
            $args + [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            $prefix . 'colors_hover',
            $args + [
                'label' => __('Hover'),
            ]
        );

        $widget->addControl(
            $prefix . 'hover_primary_color',
            $args + [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget . ':hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ':hover, {{WRAPPER}}.gmt-view-default ' . $cssTarget . ':hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_secondary_color',
            $args + [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed ' . $cssTarget . ':hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget . ':hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked ' . $cssTarget . ':hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_animation',
            $args + [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $widget->endControlsTab();

        $widget->endControlsTabs();

        $widget->addResponsiveControl(
            $prefix . 'size',
            $args + [
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
            $prefix . 'padding',
            $args + [
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

        $widget->addResponsiveControl(
            $prefix . 'rotate',
            $args + [
                'label' => __('Rotate'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'deg' ],
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'tablet_default' => [
                    'unit' => 'deg',
                ],
                'mobile_default' => [
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'border_width',
            $args + [
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
            $args + [
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

        $widget->addResponsiveControl(
            $prefix . 'align',
            $args + [
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
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-wrapper' => 'text-align: {{VALUE}};',
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
            'section_icon',
            [
                'label' => __('Icon'),
            ]
        );

        self::registerIconInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerIconStyle($this);

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<# var link = settings.icon_link.url ? 'href="' + settings.icon_link.url + '"' : '',
				iconHTML = goomento.helpers.renderIcon( view, settings.icon_selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
				iconTag = link ? 'a' : 'div';
		#>
		<div class="gmt-icon-wrapper">
			<{{{ iconTag }}} class="gmt-icon gmt-animation-{{ settings.icon_hover_animation }}" {{{ link }}}>
				<# if ( iconHTML && iconHTML.rendered && ! settings.icon_icon ) { #>
					{{{ iconHTML.value }}}
				<# } else { #>
					<i class="{{ settings.icon }}" aria-hidden="true"></i>
				<# } #>
			</{{{ iconTag }}}>
		</div>
		<?php
    }
}
