<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;

/**
 * SagoTheme heading widget.
 *
 * SagoTheme widget that displays an eye-catching headlines.
 *
 */
class Heading extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve heading widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'heading';
    }

    /**
     * Get widget title.
     *
     * Retrieve heading widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Heading');
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
     * Retrieve heading widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-heading';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the heading widget belongs to.
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
        return [ 'heading', 'title', 'text' ];
    }

    /**
     * Register heading widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your title'),
                'default' => __('Add Your Heading Text Here'),
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
                'default' => [
                    'url' => '',
                ],
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'size',
            [
                'label' => __('Size'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'small' => __('Small'),
                    'medium' => __('Medium'),
                    'large' => __('Large'),
                    'xl' => __('XL'),
                    'xxl' => __('XXL'),
                ],
            ]
        );

        $this->addControl(
            'header_size',
            [
                'label' => __('HTML Tag'),
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
                'default' => 'h2',
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
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
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

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => \Goomento\PageBuilder\Builder\Schemes\Color::getType(),
                    'value' => \Goomento\PageBuilder\Builder\Schemes\Color::COLOR_1,
                ],
                'selectors' => [
                    // Stronger selector to avoid section style from overwriting
                    '{{WRAPPER}}.gmt-widget-heading .gmt-heading-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'typography',
                'scheme' => \Goomento\PageBuilder\Builder\Schemes\Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .gmt-heading-title',
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\TextShadow::getType(),
            [
                'name' => 'text_shadow',
                'selector' => '{{WRAPPER}} .gmt-heading-title',
            ]
        );

        $this->addControl(
            'blend_mode',
            [
                'label' => __('Blend Mode'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Normal'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-heading-title' => 'mix-blend-mode: {{VALUE}}',
                ],
                'separator' => 'none',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render heading widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if ('' === $settings['title']) {
            return;
        }

        $this->addRenderAttribute('title', 'class', 'gmt-heading-title');

        if (! empty($settings['size'])) {
            $this->addRenderAttribute('title', 'class', 'gmt-size-' . $settings['size']);
        }

        $this->addInlineEditingAttributes('title');

        $title = $settings['title'];

        if (! empty($settings['link']['url'])) {
            $this->addRenderAttribute('url', 'href', $settings['link']['url']);

            if ($settings['link']['is_external']) {
                $this->addRenderAttribute('url', 'target', '_blank');
            }

            if (! empty($settings['link']['nofollow'])) {
                $this->addRenderAttribute('url', 'rel', 'nofollow');
            }

            $title = sprintf('<a %1$s>%2$s</a>', $this->getRenderAttributeString('url'), $title);
        }

        $title_html = sprintf('<%1$s %2$s>%3$s</%1$s>', $settings['header_size'], $this->getRenderAttributeString('title'), $title);

        echo $title_html;
    }

    /**
     * Render heading widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		var title = settings.title;

		if ( '' !== settings.link.url ) {
			title = '<a href="' + settings.link.url + '">' + title + '</a>';
		}

		view.addRenderAttribute( 'title', 'class', [ 'gmt-heading-title', 'gmt-size-' + settings.size ] );

		view.addInlineEditingAttributes( 'title' );

		var title_html = '<' + settings.header_size  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + settings.header_size + '>';

		print( title_html );
		#>
		<?php
    }
}
