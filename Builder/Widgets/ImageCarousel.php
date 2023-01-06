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
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\EscaperHelper;

class ImageCarousel extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'image-carousel';

    /**
     * @inheirtDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/image_carousel.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Slider');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-photo-video';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'image', 'photo', 'visual', 'carousel', 'slider' ];
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-image-carousel'];
    }

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerCarouselImagesInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'carousel',
            [
                'label' => __('Add Images'),
                'type' => Controls::GALLERY,
                'default' => [],
                'show_label' => false,
            ]
        );

        $widget->addGroupControl(
            ImageSizeGroup::NAME,
            [
                'name' => $prefix . 'thumbnail',
                'separator' => 'none',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerCarouselImagesControl(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $slidesToShow = range(1, 10);
        $slidesToShow = array_combine($slidesToShow, $slidesToShow);

        $widget->addResponsiveControl(
            $prefix . 'slides_to_show',
            [
                'label' => __('Slides to Show'),
                'type' => Controls::SELECT,
                'options' => [
                        '' => __('Default'),
                    ] + $slidesToShow,
                'frontend_available' => true,
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'slides_to_scroll',
            [
                'label' => __('Slides to Scroll'),
                'type' => Controls::SELECT,
                'description' => __('Set how many slides are scrolled per swipe.'),
                'options' => [
                        '' => __('Default'),
                    ] + $slidesToShow,
                'condition' => [
                    $prefix . 'slides_to_show!' => '1',
                ],
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'image_stretch',
            [
                'label' => __('Image Stretch'),
                'type' => Controls::SELECT,
                'default' => 'no',
                'options' => [
                    'no' => __('No'),
                    'yes' => __('Yes'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'navigation',
            [
                'label' => __('Navigation'),
                'type' => Controls::SELECT,
                'default' => 'both',
                'options' => [
                    'both' => __('Arrows and Dots'),
                    'arrows' => __('Arrows'),
                    'dots' => __('Dots'),
                    'none' => __('None'),
                ],
                'frontend_available' => true,
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
                    $prefix  . 'link_to' => 'custom',
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
                    $prefix  . 'link_to' => 'file',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerCarouselControl(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'pause_on_hover',
            [
                'label' => __('Pause on Hover'),
                'type' => Controls::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'autoplay',
            [
                'label' => __('Autoplay'),
                'type' => Controls::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'autoplay_speed',
            [
                'label' => __('Autoplay Speed'),
                'type' => Controls::NUMBER,
                'default' => 5000,
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'infinite',
            [
                'label' => __('Infinite Loop'),
                'type' => Controls::SELECT,
                'default' => 'yes',
                'options' => [
                    'yes' => __('Yes'),
                    'no' => __('No'),
                ],
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'effect',
            [
                'label' => __('Effect'),
                'type' => Controls::SELECT,
                'default' => 'slide',
                'options' => [
                    'slide' => __('Slide'),
                    'fade' => __('Fade'),
                ],
                'condition' => [
                    $prefix . 'slides_to_show' => '1',
                ],
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'speed',
            [
                'label' => __('Animation Speed'),
                'type' => Controls::NUMBER,
                'default' => 500,
                'frontend_available' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'direction',
            [
                'label' => __('Direction'),
                'type' => Controls::SELECT,
                'default' => 'ltr',
                'options' => [
                    'ltr' => __('Left'),
                    'rtl' => __('Right'),
                ],
                'frontend_available' => true,
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerNavigationStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-image-carousel-wrapper'
    ) {
        $widget->addControl(
            $prefix . 'heading_style_arrows',
            [
                'label' => __('Arrows'),
                'type' => Controls::HEADING,
                'separator' => 'before',
                'condition' => [
                    $prefix . 'navigation' => [ 'arrows', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'arrows_position',
            [
                'label' => __('Position'),
                'type' => Controls::SELECT,
                'default' => 'inside',
                'options' => [
                    'inside' => __('Inside'),
                    'outside' => __('Outside'),
                ],
                'prefix_class' => 'gmt-arrows-position-',
                'condition' => [
                    $prefix . 'navigation' => [ 'arrows', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'arrows_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 60,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-swiper-button.gmt-swiper-button-prev i, {{WRAPPER}} ' . $cssTarget . ' .gmt-swiper-button.gmt-swiper-button-next i' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'navigation' => [ 'arrows', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'arrows_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .gmt-swiper-button.gmt-swiper-button-prev, {{WRAPPER}} ' . $cssTarget . ' .gmt-swiper-button.gmt-swiper-button-next' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    $prefix . 'navigation' => [ 'arrows', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'heading_style_dots',
            [
                'label' => __('Dots'),
                'type' => Controls::HEADING,
                'separator' => 'before',
                'condition' => [
                    $prefix . 'navigation' => [ 'dots', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'dots_position',
            [
                'label' => __('Position'),
                'type' => Controls::SELECT,
                'default' => 'outside',
                'options' => [
                    'outside' => __('Outside'),
                    'inside' => __('Inside'),
                ],
                'prefix_class' => 'gmt-pagination-position-',
                'condition' => [
                    $prefix . 'navigation' => [ 'dots', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'dots_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 5,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .swiper-pagination-bullet' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'navigation' => [ 'dots', 'both' ],
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'dots_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .swiper-pagination-bullet' => 'background: {{VALUE}};',
                ],
                'condition' => [
                    $prefix . 'navigation' => [ 'dots', 'both' ],
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerImageStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-image-carousel'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'gallery_vertical_align',
            [
                'label' => __('Vertical Align'),
                'type' => Controls::CHOOSE,
                'label_block' => false,
                'options' => [
                    'flex-start' => [
                        'title' => __('Start'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fas fa-align-center',
                    ],
                    'flex-end' => [
                        'title' => __('End'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'condition' => [
                    $prefix . 'slides_to_show!' => '1',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'display: flex; align-items: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'image_spacing',
            [
                'label' => __('Spacing'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Default'),
                    'custom' => __('Custom'),
                ],
                'default' => '',
                'condition' => [
                    $prefix . 'slides_to_show!' => '1',
                ],
            ]
        );

        $widget->addControl(
            'image_spacing_custom', // avoid using prefix since this use for JS on FE
            [
                'label' => __('Image Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 20,
                ],
                'show_label' => false,
                'condition' => [
                    $prefix . 'image_spacing' => 'custom',
                    $prefix . 'slides_to_show!' => '1',
                ],
                'frontend_available' => true
            ]
        );

        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => $prefix . 'image_border',
                'selector' => '{{WRAPPER}} ' . $cssTarget . ' .swiper-slide-image',
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'image_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' .swiper-slide-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param bool $pagination
     * @param bool $navigation
     * @return void
     */
    public static function printCarouselControls(bool $pagination, bool $navigation)
    {
        ?>
            <?php if ($pagination): ?>
                <div class="swiper-pagination"></div>
            <?php endif; ?>

            <?php if ($navigation): ?>
            <div class="gmt-swiper-button gmt-swiper-button-prev">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
                <span class="gmt-screen-only">
                    <?= /** @noEscape */ EscaperHelper::escapeHtml(__('Previous')); ?>
                </span>
            </div>
            <div class="gmt-swiper-button gmt-swiper-button-next">
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
                <span class="gmt-screen-only">
                    <?= /** @noEscape */ EscaperHelper::escapeHtml(__('Next')); ?>
                </span>
            </div>
            <?php endif; ?>
        <?php
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_image_carousel',
            [
                'label' => __('Image Carousel'),
            ]
        );

        self::registerCarouselImagesInterface($this);
        self::registerCarouselImagesControl($this, '');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        self::registerCarouselControl($this, '');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_navigation',
            [
                'label' => __('Navigation'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'navigation' => [ 'arrows', 'dots', 'both' ],
                ],
            ]
        );

        self::registerNavigationStyle($this, '');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerImageStyle($this, '');

        $this->endControlsSection();
    }
}
