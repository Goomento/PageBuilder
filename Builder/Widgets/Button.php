<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;

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
     * @param array $args
     */
    public static function registerButtonInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'type',
            $args + [
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
            $args + [
                'label' => __('Text'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click here'),
                'placeholder' => __('Click here'),
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
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'align',
            $args + [
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
            $args + [
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
            $args + [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix .  'icon_align',
            $args + [
                'label' => __('Icon Position'),
                'type' => Controls::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'condition' => [
                    $prefix . 'selected_icon[value]!' => '',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'button_css_id',
            $args + [
                'label' => __('Button CSS ID'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'label_block' => false,
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this element is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.'),
                'separator' => 'before',
            ]
        );
    }

    /**
     * Share of button styling
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws \Exception
     */
    public static function registerButtonStyle(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        array          $args = [],
        string         $cssTarget = '.gmt-button'
    )
    {
        $widget->addGroupControl(
            TypographyGroup::NAME,
            $args + [
                'name' => $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} ' . $cssTarget ,
            ]
        );

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            $args + [
                'name' => $prefix . 'text_shadow',
                'selector' => '{{WRAPPER}}  ' . $cssTarget,
            ]
        );

        $widget->startControlsTabs('tabs_button_style');

        $widget->startControlsTab(
            $prefix . 'tab_button_normal',
            $args + [
                'label' => __('Normal'),
            ]
        );

        $widget->addControl(
            $prefix . 'text_color',
            $args + [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}  ' . $cssTarget => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_color',
            $args + [
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
            $args + [
                'label' => __('Hover'),
            ]
        );

        $widget->addControl(
            $prefix . 'hover_color',
            $args + [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}  ' . $cssTarget . ':hover, {{WRAPPER}} ' . $cssTarget . ':focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_hover_color',
            $args + [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover, {{WRAPPER}} ' . $cssTarget . ':focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_border_color',
            $args + [
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
            $args + [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $widget->endControlsTab();

        $widget->endControlsTabs();

        $widget->addGroupControl(
            BorderGroup::NAME,
            $args + [
                'name' => $prefix . 'border',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'separator' => 'before',
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
            ]
        );

        $widget->addGroupControl(
            BoxShadowGroup::NAME,
            $args + [
                'name' => $prefix . 'box_shadow',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'text_padding',
            $args + [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'icon_indent',
            $args + [
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

        self::registerButtonInterface($this, 'button_');

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

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<#
		view.addRenderAttribute( 'text', 'class', 'gmt-button-text' );
		view.addInlineEditingAttributes( 'text', 'none' );
		var iconHTML = goomento.helpers.renderIcon( view, settings.button_selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
        settings.button_size = settings.button_size ? settings.button_size : 'sm';
		#>
		<div class="gmt-button-wrapper">
			<a id="{{ settings.button_css_id }}" class="gmt-button gmt-size-{{ settings.button_size }} gmt-animation-{{ settings.hover_animation }}" href="{{ settings.button_link.url }}" role="button">
				<span class="gmt-button-content-wrapper">
					<# if ( settings.button_icon || settings.button_selected_icon ) { #>
					<span class="gmt-button-icon gmt-align-icon-{{ settings.button_icon_align }}">
						<# if ( ! settings.button_icon && iconHTML.rendered ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
						<# } #>
					</span>
					<# } #>
					<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.button_text }}}</span>
				</span>
			</a>
		</div>
		<?php
    }
}
