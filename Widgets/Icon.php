<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Schemes\Color;

/**
 * Class Icon
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Icon extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve icon widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'icon';
    }

    /**
     * Get widget title.
     *
     * Retrieve icon widget title.
     *
     *
     * @return string Widget title.
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
     * Get widget icon.
     *
     * Retrieve icon widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'far fa-star';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the icon widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     *
     * @return array Widget categories.
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     *
     * @return array Widget keywords.
     */
    public function getKeywords()
    {
        return [ 'icon' ];
    }

    /**
     * Register icon widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_icon',
            [
                'label' => __('Icon'),
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-star',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->addControl(
            'view',
            [
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

        $this->addControl(
            'shape',
            [
                'label' => __('Shape'),
                'type' => Controls::SELECT,
                'options' => [
                    'circle' => __('Circle'),
                    'square' => __('Square'),
                ],
                'default' => 'circle',
                'condition' => [
                    'view!' => 'default',
                ],
                'prefix_class' => 'gmt-shape-',
            ]
        );

        $this->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );

        $this->addResponsiveControl(
            'align',
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
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->startControlsTabs('icon_colors');

        $this->startControlsTab(
            'icon_colors_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon, {{WRAPPER}}.gmt-view-default .gmt-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon, {{WRAPPER}}.gmt-view-default .gmt-icon svg' => 'fill: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addControl(
            'secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'icon_colors_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'hover_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon:hover, {{WRAPPER}}.gmt-view-default .gmt-icon:hover' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon:hover, {{WRAPPER}}.gmt-view-default .gmt-icon:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'view!' => 'default',
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon:hover' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->addResponsiveControl(
            'size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'icon_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->addResponsiveControl(
            'rotate',
            [
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
                    '{{WRAPPER}} .gmt-icon i, {{WRAPPER}} .gmt-icon svg' => 'transform: rotate({{SIZE}}{{UNIT}});',
                ],
            ]
        );

        $this->addControl(
            'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'view' => 'framed',
                ],
            ]
        );

        $this->addControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'view!' => 'default',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render icon widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('wrapper', 'class', 'gmt-icon-wrapper');

        $this->addRenderAttribute('icon-wrapper', 'class', 'gmt-icon');

        if (! empty($settings['hover_animation'])) {
            $this->addRenderAttribute('icon-wrapper', 'class', 'gmt-animation-' . $settings['hover_animation']);
        }

        $icon_tag = 'div';

        if (! empty($settings['link']['url'])) {
            $this->addRenderAttribute('icon-wrapper', 'href', $settings['link']['url']);
            $icon_tag = 'a';

            if (! empty($settings['link']['is_external'])) {
                $this->addRenderAttribute('icon-wrapper', 'target', '_blank');
            }

            if ($settings['link']['nofollow']) {
                $this->addRenderAttribute('icon-wrapper', 'rel', 'nofollow');
            }
        }

        ?>
		<div <?= $this->getRenderAttributeString('wrapper'); ?>>
			<<?= $icon_tag . ' ' . $this->getRenderAttributeString('icon-wrapper'); ?>>
			<?php Icons::renderIcon($settings['selected_icon'], [ 'aria-hidden' => 'true' ]) ?>
			</<?= $icon_tag; ?>>
		</div>
		<?php
    }

    /**
     * Render icon widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<# var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
				iconHTML = goomento.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' ),
				iconTag = link ? 'a' : 'div';
		#>
		<div class="gmt-icon-wrapper">
			<{{{ iconTag }}} class="gmt-icon gmt-animation-{{ settings.hover_animation }}" {{{ link }}}>
				<# if ( iconHTML && iconHTML.rendered && ! settings.icon ) { #>
					{{{ iconHTML.value }}}
				<# } else { #>
					<i class="{{ settings.icon }}" aria-hidden="true"></i>
				<# } #>
			</{{{ iconTag }}}>
		</div>
		<?php
    }
}
