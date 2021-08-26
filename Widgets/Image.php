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
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilter;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSize;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadow;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use ReflectionException;

/**
 * Class Image
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Image extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve image widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'image';
    }

    /**
     * Get widget title.
     *
     * Retrieve image widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Image');
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
     * Retrieve image widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'far fa-image';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the image widget belongs to.
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
        return [ 'image', 'photo', 'visual' ];
    }

    /**
     * Register image widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @throws ReflectionException
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_image',
            [
                'label' => __('Image'),
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
                'name' => 'image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `image_size` and `image_custom_dimension`.
                'default' => 'large',
                'separator' => 'none',
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
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'caption_source',
            [
                'label' => __('Caption'),
                'type' => Controls::SELECT,
                'options' => [
                    'none' => __('None'),
                    'custom' => __('Custom Caption'),
                ],
                'default' => 'none',
            ]
        );

        $this->addControl(
            'caption',
            [
                'label' => __('Custom Caption'),
                'type' => Controls::TEXT,
                'default' => '',
                'placeholder' => __('Enter your image caption'),
                'condition' => [
                    'caption_source' => 'custom',
                ],
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'link_to',
            [
                'label' => __('Link'),
                'type' => Controls::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None'),
                    'file' => __('Media File'),
                    'custom' => __('Custom URL'),
                ],
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
                'condition' => [
                    'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $this->addControl(
            'open_lightbox',
            [
                'label' => __('Lightbox'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
                'condition' => [
                    'link_to' => 'file',
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
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'width',
            [
                'label' => __('Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                ],
                'mobile_default' => [
                    'unit' => '%',
                ],
                'size_units' => [ '%', 'px', 'vw' ],
                'range' => [
                    '%' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 1,
                        'max' => 1000,
                    ],
                    'vw' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'space',
            [
                'label' => __('Max Width') . ' (%)',
                'type' => Controls::SLIDER,
                'default' => [
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
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image img' => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'separator_panel_style',
            [
                'type' => Controls::DIVIDER,
                'style' => 'thick',
            ]
        );

        $this->startControlsTabs('image_effects');

        $this->startControlsTab(
            'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $this->addControl(
            'opacity',
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
                    '{{WRAPPER}} .gmt-image img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters',
                'selector' => '{{WRAPPER}} .gmt-image img',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $this->addControl(
            'opacity_hover',
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
                    '{{WRAPPER}} .gmt-image:hover img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->addGroupControl(
            CssFilter::getType(),
            [
                'name' => 'css_filters_hover',
                'selector' => '{{WRAPPER}} .gmt-image:hover img',
            ]
        );

        $this->addControl(
            'background_hover_transition',
            [
                'label' => __('Transition Duration'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image img' => 'transition-duration: {{SIZE}}s',
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
                'name' => 'image_border',
                'selector' => '{{WRAPPER}} .gmt-image img',
                'separator' => 'before',
            ]
        );

        $this->addResponsiveControl(
            'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadow::getType(),
            [
                'name' => 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .gmt-image img',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_caption',
            [
                'label' => __('Caption'),
                'tab'   => Controls::TAB_STYLE,
                'condition' => [
                    'caption_source!' => 'none',
                ],
            ]
        );

        $this->addControl(
            'caption_align',
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
                    '{{WRAPPER}} .widget-image-caption' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addControl(
            'caption_background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'caption_typography',
                'selector' => '{{WRAPPER}} .widget-image-caption',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->addGroupControl(
            TextShadow::getType(),
            [
                'name' => 'caption_text_shadow',
                'selector' => '{{WRAPPER}} .widget-image-caption',
            ]
        );

        $this->addResponsiveControl(
            'caption_space',
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
                    '{{WRAPPER}} .widget-image-caption' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Check if the current widget has caption
     *
     *
     * @param array $settings
     *
     * @return boolean
     */
    private function hasCaption($settings)
    {
        return (! empty($settings['caption_source']) && 'none' !== $settings['caption_source']);
    }

    /**
     * Get the caption for current widget.
     *
     * @param $settings
     *
     * @return string
     */
    private function getCaption($settings)
    {
        $caption = '';
        if (! empty($settings['caption_source'])) {
            switch ($settings['caption_source']) {
                case 'custom':
                    $caption = ! Utils::isEmpty($settings['caption']) ? $settings['caption'] : '';
            }
        }
        return $caption;
    }

    /**
     * Render image widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if (empty($settings['image']['url'])) {
            return;
        }

        $has_caption = $this->hasCaption($settings);

        $this->addRenderAttribute('wrapper', 'class', 'gmt-image');

        if (! empty($settings['shape'])) {
            $this->addRenderAttribute('wrapper', 'class', 'gmt-image-shape-' . $settings['shape']);
        }

        $link = $this->getLinkUrl($settings);

        if ($link) {
            $this->addRenderAttribute('link', [
                'href' => $link['url'],
                'data-gmt-open-lightbox' => $settings['open_lightbox'],
            ]);

            if (StaticObjectManager::get(Editor::class)->isEditMode()) {
                $this->addRenderAttribute('link', [
                    'class' => 'gmt-clickable',
                ]);
            }

            if (! empty($link['is_external'])) {
                $this->addRenderAttribute('link', 'target', '_blank');
            }

            if (! empty($link['nofollow'])) {
                $this->addRenderAttribute('link', 'rel', 'nofollow');
            }
        } ?>
		<div <?= $this->getRenderAttributeString('wrapper'); ?>>
			<?php if ($has_caption) : ?>
				<figure class="gmt-caption">
			<?php endif; ?>
			<?php if ($link) : ?>
					<a <?= $this->getRenderAttributeString('link'); ?>>
			<?php endif; ?>
				<?= ImageSize::getAttachmentImageHtml($settings); ?>
			<?php if ($link) : ?>
					</a>
			<?php endif; ?>
			<?php if ($has_caption) : ?>
					<figcaption class="widget-image-caption image-caption-text"><?= $this->getCaption($settings); ?></figcaption>
			<?php endif; ?>
			<?php if ($has_caption) : ?>
				</figure>
			<?php endif; ?>
		</div>
		<?php
    }

    /**
     * Render image widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<# if ( settings.image.url ) {

			var image_url = settings.image.url;
            var image_size = settings.image_size === 'custom' ? settings.image_custom_dimension : {},
                width = image_size.width,
                height = image_size.height;

			if ( ! image_url ) {
				return;
			}

			var hasCaption = function() {
				if( ! settings.caption_source || 'none' === settings.caption_source ) {
					return false;
				}
				return true;
			}

			var getCaption = function() {
				if ( ! hasCaption() ) {
					return '';
				}
				return 'custom' === settings.caption_source ? settings.caption : '';
			}

			var link_url;

			if ( 'custom' === settings.link_to ) {
				link_url = settings.link.url;
			}

			if ( 'file' === settings.link_to ) {
				link_url = settings.image.url;
			}

			#><div class="gmt-image{{ settings.shape ? ' gmt-image-shape-' + settings.shape : '' }}"><#
			var imgClass = '';

			if ( '' !== settings.hover_animation ) {
				imgClass = 'gmt-animation-' + settings.hover_animation;
			}

			if ( hasCaption() ) {
				#><figure class="gmt-caption"><#
			}

			if ( link_url ) {
					#><a class="gmt-clickable" data-gmt-open-lightbox="{{ settings.open_lightbox }}" href="{{ link_url }}"><#
			}
						#><img
                    <# if ( width ) { #> width="{{ width }}" <# } #>
                    <# if ( height ) { #> height="{{ height }}" <# } #>
                        src="{{ image_url }}"
                        class="{{ imgClass }}" /><#

			if ( link_url ) {
					#></a><#
			}

			if ( hasCaption() ) {
					#><figcaption class="widget-image-caption gmt-caption-text">{{{ getCaption() }}}</figcaption><#
			}

			if ( hasCaption() ) {
				#></figure><#
			}

			#></div><#
		} #>
		<?php
    }

    /**
     * Retrieve image widget link URL.
     *
     *
     * @param array $settings
     *
     * @return array|string|false An array/string containing the link URL, or false if no link.
     */
    private function getLinkUrl($settings)
    {
        if ('none' === $settings['link_to']) {
            return false;
        }

        if ('custom' === $settings['link_to']) {
            if (empty($settings['link']['url'])) {
                return false;
            }
            return $settings['link'];
        }

        return [
            'url' => $settings['image']['url'],
        ];
    }
}
