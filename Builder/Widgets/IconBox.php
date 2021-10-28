<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;

class IconBox extends AbstractWidget
{

    const NAME = 'icon-box';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/icon_box.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Icon Box');
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
        return 'fas fa-star';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'icon box', 'icon' ];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_icon',
            [
                'label' => __('Icon Box'),
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
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
                    'selected_icon[value]!' => '',
                ],
                'prefix_class' => 'gmt-shape-',
            ]
        );

        $this->addControl(
            'title_text',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('This is the heading'),
                'placeholder' => __('Enter your title'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'description_text',
            [
                'label' => '',
                'type' => Controls::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your description'),
                'rows' => 10,
                'separator' => 'none',
                'show_label' => false,
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
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'position',
            [
                'label' => __('Icon Position'),
                'type' => Controls::CHOOSE,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'fas fa-level-up-alt',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'prefix_class' => 'gmt-position-',
                'toggle' => false,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'selected_icon[value]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'title_size',
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
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'h3',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_icon',
            [
                'label' => __('Icon'),
                'tab'   => Controls::TAB_STYLE,
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'name' => 'selected_icon[value]',
                            'operator' => '!=',
                            'value' => '',
                        ],
                    ],
                ],
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
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon, {{WRAPPER}}.gmt-view-default .gmt-icon' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
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
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
                    '{{WRAPPER}}.gmt-view-framed .gmt-icon:hover, {{WRAPPER}}.gmt-view-default .gmt-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}}; border-color: {{VALUE}};',
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
                    '{{WRAPPER}}.gmt-view-stacked .gmt-icon:hover' => 'fill: {{VALUE}}; color: {{VALUE}};',
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
            'icon_space',
            [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 15,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}.gmt-position-right .gmt-icon-box-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-left .gmt-icon-box-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-top .gmt-icon-box-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} .gmt-icon-box-icon' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_size',
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

        $this->addControl(
            'rotate',
            [
                'label' => __('Rotate'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0,
                    'unit' => 'deg',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
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

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Content'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'text_align',
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
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-box-wrapper' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'content_vertical_alignment',
            [
                'label' => __('Vertical Alignment'),
                'type' => Controls::SELECT,
                'options' => [
                    'top' => __('Top'),
                    'middle' => __('Middle'),
                    'bottom' => __('Bottom'),
                ],
                'default' => 'top',
                'prefix_class' => 'gmt-vertical-align-',
            ]
        );

        $this->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'title_bottom_space',
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
                    '{{WRAPPER}} .gmt-icon-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-box-content .gmt-icon-box-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .gmt-icon-box-content .gmt-icon-box-title',
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $this->addControl(
            'heading_description',
            [
                'label' => __('Description'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'description_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon-box-content .gmt-icon-box-description' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup::NAME,
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .gmt-icon-box-content .gmt-icon-box-description',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<#
		var link = settings.link.url ? 'href="' + settings.link.url + '"' : '',
			iconTag = link ? 'a' : 'span',
			iconHTML = goomento.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );

		view.addRenderAttribute( 'description_text', 'class', 'gmt-icon-box-description' );

		view.addInlineEditingAttributes( 'title_text', 'none' );
		view.addInlineEditingAttributes( 'description_text' );
		#>
		<div class="gmt-icon-box-wrapper">
			<# if ( settings.icon || settings.selected_icon ) { #>
			<div class="gmt-icon-box-icon">
				<{{{ iconTag + ' ' + link }}} class="gmt-icon gmt-animation-{{ settings.hover_animation }}">
					<# if ( iconHTML && iconHTML.rendered && ! settings.icon ) { #>
						{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{ settings.icon }}" aria-hidden="true"></i>
						<# } #>
				</{{{ iconTag }}}>
			</div>
			<# } #>
			<div class="gmt-icon-box-content">
				<{{{ settings.title_size }}} class="gmt-icon-box-title">
					<{{{ iconTag + ' ' + link }}} {{{ view.getRenderAttributeString( 'title_text' ) }}}>{{{ settings.title_text }}}</{{{ iconTag }}}>
				</{{{ settings.title_size }}}>
				<# if ( settings.description_text ) { #>
				<p {{{ view.getRenderAttributeString( 'description_text' ) }}}>{{{ settings.description_text }}}</p>
				<# } #>
			</div>
		</div>
		<?php
    }
}
