<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilter;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSize;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Utils;

/**
 * Class ImageBox
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class ImageBox extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve image box widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'image-box';
    }

    /**
     * Get widget title.
     *
     * Retrieve image box widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Image Box');
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
     * Retrieve image box widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-images';
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
        return [ 'image', 'photo', 'visual', 'box' ];
    }

    /**
     * Register image box widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_image',
            [
                'label' => __('Image Box'),
            ]
        );

        $this->addControl(
            'image',
            [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => Utils::getPlaceholderImageSrc(),
                ],
            ]
        );

        $this->addGroupControl(
            ImageSize::getType(),
            [
                'name' => 'image',
                'default' => 'full',
                'separator' => 'none',
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
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your description'),
                'separator' => 'none',
                'rows' => 10,
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
                'label' => __('Image Position'),
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
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'image_space',
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
                    '{{WRAPPER}}.gmt-position-right .gmt-image-box-img' => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-left .gmt-image-box-img' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-top .gmt-image-box-img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} .gmt-image-box-img' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'image_size',
            [
                'label' => __('Width') . ' (%)',
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 30,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image-box-wrapper .gmt-image-box-img' => 'width: {{SIZE}}{{UNIT}};',
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

        $this->startControlsTabs('image_effects');

        $this->startControlsTab(
            'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .gmt-image-box-img img',
            ]
        );

        $this->addControl(
            'image_opacity',
            [
                'label' => __('Opacity'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image-box-img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addControl(
            'background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 0.3,
                ],
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image-box-img img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover .gmt-image-box-img img',
            ]
        );

        $this->addControl(
            'image_opacity_hover',
            [
                'label' => __('Opacity'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 1,
                        'min' => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}:hover .gmt-image-box-img img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

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
                    '{{WRAPPER}} .gmt-image-box-wrapper' => 'text-align: {{VALUE}};',
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
                    '{{WRAPPER}} .gmt-image-box-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .gmt-image-box-content .gmt-image-box-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .gmt-image-box-content .gmt-image-box-title',
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
                    '{{WRAPPER}} .gmt-image-box-content .gmt-image-box-description' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .gmt-image-box-content .gmt-image-box-description',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render image box widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $has_content = ! Utils::isEmpty($settings['title_text']) || ! Utils::isEmpty($settings['description_text']);

        $html = '<div class="gmt-image-box-wrapper">';

        if (! empty($settings['link']['url'])) {
            $this->addRenderAttribute('link', 'href', $settings['link']['url']);

            if ($settings['link']['is_external']) {
                $this->addRenderAttribute('link', 'target', '_blank');
            }

            if (! empty($settings['link']['nofollow'])) {
                $this->addRenderAttribute('link', 'rel', 'nofollow');
            }
        }

        if (! empty($settings['image']['url'])) {
            $this->addRenderAttribute('image', 'src', $settings['image']['url']);
            $this->addRenderAttribute('image', 'alt', '');
            $this->addRenderAttribute('image', 'title', '');

            if ($settings['hover_animation']) {
                $this->addRenderAttribute('image', 'class', 'gmt-animation-' . $settings['hover_animation']);
            }

            $image_html = ImageSize::getAttachmentImageHtml($settings, 'image', 'image');

            if (! empty($settings['link']['url'])) {
                $image_html = '<a ' . $this->getRenderAttributeString('link') . '>' . $image_html . '</a>';
            }

            $html .= '<figure class="gmt-image-box-img">' . $image_html . '</figure>';
        }

        if ($has_content) {
            $html .= '<div class="gmt-image-box-content">';

            if (! Utils::isEmpty($settings['title_text'])) {
                $this->addRenderAttribute('title_text', 'class', 'gmt-image-box-title');

                $this->addInlineEditingAttributes('title_text', 'none');

                $title_html = $settings['title_text'];

                if (! empty($settings['link']['url'])) {
                    $title_html = '<a ' . $this->getRenderAttributeString('link') . '>' . $title_html . '</a>';
                }

                $html .= sprintf('<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->getRenderAttributeString('title_text'), $title_html);
            }

            if (! Utils::isEmpty($settings['description_text'])) {
                $this->addRenderAttribute('description_text', 'class', 'gmt-image-box-description');

                $this->addInlineEditingAttributes('description_text');

                $html .= sprintf('<p %1$s>%2$s</p>', $this->getRenderAttributeString('description_text'), $settings['description_text']);
            }

            $html .= '</div>';
        }

        $html .= '</div>';

        echo $html;
    }

    /**
     * Render image box widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		var html = '<div class="gmt-image-box-wrapper">';

		if ( settings.image.url ) {
            var image_url = settings.image.url;
            var image_size = settings.image_size === 'custom' ? settings.image_custom_dimension : {},
                width = image_size.width,
                height = image_size.height;

			var imageHtml = '<img ' + (height ? ' height="' + height + '"' : '') + (width ? ' width="' + width + '"' : '') + ' src="' + image_url + '" class="gmt-animation-' + settings.hover_animation + '" />';

			if ( settings.link.url ) {
				imageHtml = '<a href="' + settings.link.url + '">' + imageHtml + '</a>';
			}

			html += '<figure class="gmt-image-box-img">' + imageHtml + '</figure>';
		}

		var hasContent = !! ( settings.title_text || settings.description_text );

		if ( hasContent ) {
			html += '<div class="gmt-image-box-content">';

			if ( settings.title_text ) {
				var title_html = settings.title_text;

				if ( settings.link.url ) {
					title_html = '<a href="' + settings.link.url + '">' + title_html + '</a>';
				}

				view.addRenderAttribute( 'title_text', 'class', 'gmt-image-box-title' );

				view.addInlineEditingAttributes( 'title_text', 'none' );

				html += '<' + settings.title_size  + ' ' + view.getRenderAttributeString( 'title_text' ) + '>' + title_html + '</' + settings.title_size  + '>';
			}

			if ( settings.description_text ) {
				view.addRenderAttribute( 'description_text', 'class', 'gmt-image-box-description' );

				view.addInlineEditingAttributes( 'description_text' );

				html += '<p ' + view.getRenderAttributeString( 'description_text' ) + '>' + settings.description_text + '</p>';
			}

			html += '</div>';
		}

		html += '</div>';

		print( html );
		#>
		<?php
    }
}
