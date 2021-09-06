<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\Border;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadow;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\StaticUtils;

/**
 * Class Button
 * @package Goomento\BuilderWidgets\Widgets
 */
class Button extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve button widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'button';
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * Get widget title.
     *
     * Retrieve button widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Button');
    }

    /**
     * Get widget icon.
     *
     * Retrieve button widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-mouse-pointer';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the button widget belongs to.
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
     * Get button sizes.
     *
     * Retrieve an array of button sizes for the button widget.
     *
     *
     * @return array An array containing button sizes.
     */
    public static function getButtonSizes()
    {
        return [
            'xs' => __('Extra Small'),
            'sm' => __('Small'),
            'md' => __('Medium'),
            'lg' => __('Large'),
            'xl' => __('Extra Large'),
        ];
    }

    /**
     * Register button widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_button',
            [
                'label' => __('Button'),
            ]
        );

        $this->addControl(
            'button_type',
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

        $this->addControl(
            'text',
            [
                'label' => __('Text'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Click here'),
                'placeholder' => __('Click here'),
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
                'default' => [
                    'url' => '#',
                ],
            ]
        );

        $this->addResponsiveControl(
            'align',
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

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => Controls::SELECT,
                'default' => 'sm',
                'options' => self::getButtonSizes(),
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'label_block' => true,
                'fa4compatibility' => 'icon',
            ]
        );

        $this->addControl(
            'icon_align',
            [
                'label' => __('Icon Position'),
                'type' => Controls::SELECT,
                'default' => 'left',
                'options' => [
                    'left' => __('Before'),
                    'right' => __('After'),
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'icon_indent',
            [
                'label' => __('Icon Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-button .gmt-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-button .gmt-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => Controls::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->addControl(
            'button_css_id',
            [
                'label' => __('Button ID'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'title' => __('Add your custom id WITHOUT the Pound key. e.g: my-id'),
                'label_block' => false,
                'description' => __('Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.'),
                'separator' => 'before',

            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Button'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'typography',
                'scheme' => Typography::TYPOGRAPHY_4,
                'selector' => '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button',
            ]
        );

        $this->addGroupControl(
            TextShadow::getType(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button',
            ]
        );

        $this->startControlsTabs('tabs_button_style');

        $this->startControlsTab(
            'tab_button_normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'button_text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button' => 'fill: {{VALUE}}; color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_4,
                ],
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_button_hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'hover_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button:hover, {{WRAPPER}} .gmt-button:hover, {{WRAPPER}} a.gmt-button:focus, {{WRAPPER}} .gmt-button:focus' => 'color: {{VALUE}};',
                    '{{WRAPPER}} a.gmt-button:hover svg, {{WRAPPER}} .gmt-button:hover svg, {{WRAPPER}} a.gmt-button:focus svg, {{WRAPPER}} .gmt-button:focus svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_background_hover_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button:hover, {{WRAPPER}} .gmt-button:hover, {{WRAPPER}} a.gmt-button:focus, {{WRAPPER}} .gmt-button:focus' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'button_hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button:hover, {{WRAPPER}} .gmt-button:hover, {{WRAPPER}} a.gmt-button:focus, {{WRAPPER}} .gmt-button:focus' => 'border-color: {{VALUE}};',
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

        $this->addGroupControl(
            Border::getType(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .gmt-button',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadow::getType(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .gmt-button',
            ]
        );

        $this->addResponsiveControl(
            'text_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} a.gmt-button, {{WRAPPER}} .gmt-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render button widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('wrapper', 'class', 'gmt-button-wrapper');

        if (! empty($settings['link']['url'])) {
            $this->addRenderAttribute('button', 'href', $settings['link']['url']);
            $this->addRenderAttribute('button', 'class', 'gmt-button-link');

            if ($settings['link']['is_external']) {
                $this->addRenderAttribute('button', 'target', '_blank');
            }

            if ($settings['link']['nofollow']) {
                $this->addRenderAttribute('button', 'rel', 'nofollow');
            }
        }

        $this->addRenderAttribute('button', 'class', 'gmt-button');
        $this->addRenderAttribute('button', 'role', 'button');

        if (! empty($settings['button_css_id'])) {
            $this->addRenderAttribute('button', 'id', $settings['button_css_id']);
        }

        if (! empty($settings['size'])) {
            $this->addRenderAttribute('button', 'class', 'gmt-size-' . $settings['size']);
        }

        if ($settings['hover_animation']) {
            $this->addRenderAttribute('button', 'class', 'gmt-animation-' . $settings['hover_animation']);
        } ?>
		<div <?= $this->getRenderAttributeString('wrapper'); ?>>
			<a <?= $this->getRenderAttributeString('button'); ?>>
				<?php $this->renderText(); ?>
			</a>
		</div>
		<?php
    }

    /**
     * Render button widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		view.addRenderAttribute( 'text', 'class', 'gmt-button-text' );
		view.addInlineEditingAttributes( 'text', 'none' );
		var iconHTML = goomento.helpers.renderIcon( view, settings.selected_icon, { 'aria-hidden': true }, 'i' , 'object' );
        settings.size = settings.size ? settings.size : 'sm';
		#>
		<div class="gmt-button-wrapper">
			<a id="{{ settings.button_css_id }}" class="gmt-button gmt-size-{{ settings.size }} gmt-animation-{{ settings.hover_animation }}" href="{{ settings.link.url }}" role="button">
				<span class="gmt-button-content-wrapper">
					<# if ( settings.icon || settings.selected_icon ) { #>
					<span class="gmt-button-icon gmt-align-icon-{{ settings.icon_align }}">
						<# if ( ! settings.icon && iconHTML.rendered ) { #>
							{{{ iconHTML.value }}}
						<# } else { #>
							<i class="{{ settings.icon }}" aria-hidden="true"></i>
						<# } #>
					</span>
					<# } #>
					<span {{{ view.getRenderAttributeString( 'text' ) }}}>{{{ settings.text }}}</span>
				</span>
			</a>
		</div>
		<?php
    }

    /**
     * Render button text.
     *
     * Render button widget text.
     *
     */
    protected function renderText()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute([
            'content-wrapper' => [
                'class' => 'gmt-button-content-wrapper',
            ],
            'icon-align' => [
                'class' => [
                    'gmt-button-icon',
                    'gmt-align-icon-' . $settings['icon_align'],
                ],
            ],
            'text' => [
                'class' => 'gmt-button-text',
            ],
        ]);

        $this->addInlineEditingAttributes('text', 'none'); ?>
		<span <?= $this->getRenderAttributeString('content-wrapper'); ?>>
			<?php if (! empty($settings['icon']) || ! empty($settings['selected_icon']['value'])) : ?>
			<span <?= $this->getRenderAttributeString('icon-align'); ?>>
				<?php Icons::renderIcon($settings['selected_icon'], [ 'aria-hidden' => 'true' ]); ?>
			</span>
			<?php endif; ?>
			<span <?= $this->getRenderAttributeString('text'); ?>><?= $settings['text']; ?></span>
		</span>
		<?php
    }
}
