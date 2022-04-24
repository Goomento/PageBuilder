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
        return ['banner-slider'];
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

        Banner::registerBannerInterface($repeater);

        $this->addControl(
            'banner_list',
            [
                'label' => '',
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'banner_image' => DataHelper::getPlaceholderImageSrc(),
                        'banner_caption' => __('Lorem ipsum dolor sit amet.'),
                    ],
                    [
                        'banner_image' => DataHelper::getPlaceholderImageSrc(),
                        'banner_caption' => __('Lorem ipsum dolor sit amet.'),
                    ],
                ],
                'title_field' => '{{{ banner_caption }}}',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_slider',
            [
                'label' => __('Slider'),
            ]
        );

        ImageCarousel::registerCarouselImagesControl($this, self::NAME . '_');

        $this->removeControl(self::NAME . '_' . 'carousel');

        $this->removeControl(self::NAME . '_' . 'link_to');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_additional_options',
            [
                'label' => __('Additional Options'),
            ]
        );

        ImageCarousel::registerCarouselControl($this);

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

        ImageCarousel::registerNavigationStyle($this, self::NAME . '_', '.gmt-banner-slider-wrapper');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_image',
            [
                'label' => __('Image'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageCarousel::registerImageStyle($this, self::NAME . '_', '.gmt-banner-carousel');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Caption'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        Banner::registerCaptionStyle($this, self::NAME . '_');

        $this->endControlsSection();
    }
}
