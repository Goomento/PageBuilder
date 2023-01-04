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
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class Image extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'image';

    /**
     * @var string
     */
    protected $template = 'Goomento_PageBuilder::widgets/image.phtml';

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-image';
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
        return [ 'image', 'photo' ];
    }

    /**
     * Share image style
     *
     * @param ControlsStack $widget
     * @param string $prefix
     * @throws BuilderException
     */
    public static function registerImageInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'image',
            [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'default' => [
                    'url' => DataHelper::getPlaceholderImageSrc(),
                ],
            ]
        );

        $widget->addGroupControl(
            ImageSizeGroup::NAME,
            [
                'name' => $prefix . 'image',
                'default' => 'large',
                'separator' => 'none',
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'align',
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

        $widget->addControl(
            $prefix . 'caption_source',
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

        $widget->addControl(
            $prefix . 'caption',
            [
                'label' => __('Custom Caption'),
                'type' => Controls::TEXT,
                'default' => '',
                'placeholder' => __('Enter your image caption'),
                'condition' => [
                    $prefix . 'caption_source' => 'custom',
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'link_to',
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

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'placeholder' => __('https://your-link.com'),
                'condition' => [
                    $prefix . 'link_to' => 'custom',
                ],
                'show_label' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'open_lightbox',
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
                    $prefix . 'link_to' => 'file',
                ],
            ]
        );
    }

    /**
     * Share Image style
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssHoverTarget
     * @throws BuilderException
     */
    public static function registerImageStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-image img',
        string $cssHoverTarget = '.gmt-image:hover img'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'width',
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
                    '{{WRAPPER}} ' . $cssTarget => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'space',
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
                    '{{WRAPPER}} ' . $cssTarget => 'max-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'separator_panel_style',
            [
                'type' => Controls::DIVIDER,
                'style' => 'thick',
            ]
        );

        $widget->startControlsTabs($prefix . 'image_effects');

        $widget->startControlsTab(
            $prefix . 'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $widget->addControl(
            $prefix . 'opacity',
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
                    '{{WRAPPER}} ' . $cssTarget => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            $prefix . 'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $widget->addControl(
            $prefix . 'opacity_hover',
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
                    '{{WRAPPER}} ' . $cssHoverTarget => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters_hover',
                'selector' => '{{WRAPPER}} ' . $cssHoverTarget,
            ]
        );

        $widget->addControl(
            $prefix . 'background_hover_transition',
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
                    '{{WRAPPER}} ' . $cssTarget => 'transition-duration: {{SIZE}}s',
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

        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => $prefix . 'image_border',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'separator' => 'before',
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'image_border_radius',
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
                'name' => $prefix . 'image_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * Share Image style
     *
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws BuilderException
     */
    public static function registerCaptionStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.widget-image-caption'
    ) {
        $widget->addControl(
            $prefix . 'caption_align',
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
                    '{{WRAPPER}} ' . $cssTarget => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'caption_background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'caption_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            [
                'name' => $prefix . 'caption_text_shadow',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'caption_space',
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
                    '{{WRAPPER}} ' . $cssTarget => 'margin-top: {{SIZE}}{{UNIT}};',
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
                'label' => __('Image'),
            ]
        );

        self::registerImageInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerImageStyle($this, self::buildPrefixKey('image'));

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_caption',
            [
                'label' => __('Caption'),
                'tab'   => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_caption_source!' => 'none',
                ],
            ]
        );

        self::registerCaptionStyle($this);

        $this->endControlsSection();
    }
}
