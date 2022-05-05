<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\DataHelper;

class ImageBox extends AbstractWidget
{

    const NAME = 'image-box';

    /**
     * @var string
     */
    protected $template = 'Goomento_PageBuilder::widgets/image_box.phtml';

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-image';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'image', 'photo', 'visual', 'box' ];
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerBoxStyle(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        string         $cssTarget = '.gmt-image-box-wrapper',
        array          $args = []
    )
    {
        $widget->addControl(
            $prefix . 'background_color',
            $args + [
                'label'     => __('Background'),
                'type'      => Controls::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'padding',
            $args + [
                'label'      => __('Padding'),
                'type'       => Controls::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'margin',
            $args + [
                'label'      => __('Margin'),
                'type'       => Controls::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} ' . $cssTarget => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name'     => $prefix . 'border',
                'label'    => __('Border'),
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addControl(
            $prefix . 'border_radius',
            [
                'label'     => __('Border Radius'),
                'type'      => Controls::SLIDER,
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $widget->addGroupControl(
            BoxShadowGroup::NAME,
            [
                'name'      => $prefix . 'shadow',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget,
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
                    'url' => DataHelper::getPlaceholderImageSrc(),
                ],
            ]
        );

        $this->addGroupControl(
            ImageSizeGroup::NAME,
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

        $this->endControlsSection();


        $this->startControlsSection(
            'section_wrapper_style',
            [
                'label' => __('Wrapper'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerBoxStyle($this);

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
            CssFilterGroup::NAME,
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
            CssFilterGroup::NAME,
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
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addGroupControl(
            TypographyGroup::NAME,
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
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => 'description_typography',
                'selector' => '{{WRAPPER}} .gmt-image-box-content .gmt-image-box-description',
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
