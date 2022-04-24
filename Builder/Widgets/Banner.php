<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\CssFilterGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\DataHelper;

class Banner extends AbstractWidget
{
    const NAME = 'banner';

    protected $template = 'Goomento_PageBuilder::widgets/banner.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Banner');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-images';
    }

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return ['basic'];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerBannerInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'image',
            $args + [
                'label' => __('Choose Image'),
                'type' => Controls::MEDIA,
                'default' => [
                    'url' => DataHelper::getPlaceholderImageSrc(),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'caption',
            $args + [
                'label' => __('Caption'),
                'type' => Controls::TEXTAREA,
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your caption'),
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            $args + [
                'label' => __('Link'),
                'type' => Controls::URL,
                'placeholder' => __('https://your-link.com'),
                'separator' => 'before',
            ]
        );
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerImageStyle(AbstractElement $widget, string $prefix = self::NAME . '_',
                                              string          $cssTarget = '.gmt-banner-img', array $args = [])
    {
        $widget->addControl(
            $prefix . 'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $widget->startControlsTabs($prefix . 'image_effects');

        $widget->startControlsTab(
            $prefix . 'normal',
            [
                'label' => __('Normal'),
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters',
                'selector' => '{{WRAPPER}} ' . $cssTarget . ' img',
            ]
        );

        $widget->addControl(
            $prefix . 'image_opacity',
            $args + [
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
                    '{{WRAPPER}} ' . $cssTarget . ' img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'background_hover_transition',
            $args + [
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
                    '{{WRAPPER}} ' . $cssTarget . ' img' => 'transition-duration: {{SIZE}}s',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->startControlsTab(
            $prefix . 'hover',
            [
                'label' => __('Hover'),
            ]
        );

        $widget->addGroupControl(
            CssFilterGroup::NAME,
            [
                'name' => $prefix . 'css_filters_hover',
                'selector' => '{{WRAPPER}}:hover ' . $cssTarget . ' img',
            ]
        );

        $widget->addControl(
            $prefix . 'image_opacity_hover',
            $args + [
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
                    '{{WRAPPER}}:hover ' . $cssTarget . ' img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $widget->endControlsTab();

        $widget->endControlsTabs();
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerCaptionStyle(
        AbstractElement $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-banner-content',
        array $args = []
    )
    {
        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'caption_typography',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'caption_position',
            $args + [
                'label' => __('Position'),
                'type' => Controls::SELECT,
                'default' => 'bottom-right',
                'options' => [
                    'top-left' => __('Top Left'),
                    'top-center' => __('Top Center'),
                    'top-right' => __('Top Right'),
                    'middle-left' => __('Middle Left'),
                    'middle-center' => __('Middle Center'),
                    'middle-right' => __('Middle Right'),
                    'bottom-left' => __('Bottom Left'),
                    'bottom-center' => __('Bottom Center'),
                    'bottom-right' => __('Bottom Right')
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'caption_padding',
            $args + [
                'label' => __('Padding'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'caption_margin',
            $args + [
                'label' => __('Margin'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%', 'rem' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ]
            ]
        );

        $widget->addControl(
            $prefix . 'caption_color',
            $args + [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ]
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'caption_width',
            $args + [
                'label' => __('Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'tablet_default' => [
                    'unit' => '%',
                    'size' => 80,
                ],
                'size_units' => [ '%' ],
                'range' => [
                    '%' => [
                        'min' => 5,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'caption_background',
            $args + [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function registerControls()
    {
        $this->startControlsSection(
            'section_image',
            [
                'label' => __('Banner'),
            ]
        );

        self::registerBannerInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerImageStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Caption'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        self::registerCaptionStyle($this);

        $this->endControlsSection();
    }
}
