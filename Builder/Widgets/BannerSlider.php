<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\DataHelper;

class BannerSlider extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'banner-slider';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/banner_slider.phtml';

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
    public function getTitle()
    {
        return __('Banner Slider');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-images';
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-banner-slider'];
    }

    /**
     * @inheritDoc
     */
    public function registerControls()
    {
        $this->startControlsSection(
            'section_banner',
            [
                'label' => __('Banner List'),
            ]
        );

        $repeater = new Repeater;

        $prefixKey = self::buildPrefixKey(Banner::NAME);

        Banner::registerBannerInterface($repeater, $prefixKey);

        $this->addControl(
            self::NAME . '_list',
            [
                'label' => '',
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        $prefixKey . 'image' => DataHelper::getPlaceholderImageSrc(),
                        $prefixKey . 'title' => __('Lorem ipsum dolor sit amet.'),
                        $prefixKey . 'caption' => '',
                    ],
                    [
                        $prefixKey . 'image' => DataHelper::getPlaceholderImageSrc(),
                        $prefixKey . 'title' => __('Lorem ipsum dolor sit amet.'),
                        $prefixKey . 'caption' => '',
                    ]
                ],
                'title_field' => '{{{ obj["' . $prefixKey . 'title"] }}}',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_slider',
            [
                'label' => __('Slider'),
            ]
        );

        ImageCarousel::registerCarouselImagesControl($this, '');

        $this->removeControl('link_to');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        ImageCarousel::registerCarouselControl($this, '');

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

        ImageCarousel::registerNavigationStyle($this, '', '.gmt-banner-slider-wrapper');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageCarousel::registerImageStyle($this, '', '.gmt-banner-carousel');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_title',
            [
                'label' => __('Title'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        Banner::registerTitleStyle($this, self::buildPrefixKey(Banner::NAME, 'title'));

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Caption'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        Banner::registerCaptionStyle($this, self::buildPrefixKey(Banner::NAME, 'caption'));

        $this->endControlsSection();
    }
}
