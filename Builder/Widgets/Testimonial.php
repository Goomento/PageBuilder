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
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class Testimonial extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'testimonial';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/testimonial.phtml';

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
        return __('Testimonial');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fa fa-comments-o far fa-comments';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'testimonial', 'blockquote' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerTestimonialInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'content',
            [
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'rows' => '10',
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
            ]
        );

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
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $widget->addControl(
            $prefix . 'name',
            [
                'label' => __('Name'),
                'type' => Controls::TEXT,
                'default' => 'John Doe',
            ]
        );

        $widget->addControl(
            $prefix . 'job',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'default' => 'Designer',
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'placeholder' => __('https://your-link.com'),
            ]
        );

        $widget->addControl(
            $prefix . 'image_position',
            [
                'label' => __('Image Position'),
                'type' => Controls::SELECT,
                'default' => 'aside',
                'options' => [
                    'aside' => __('Aside'),
                    'top' => __('Top'),
                ],
                'condition' => [
                    $prefix . 'image.url!' => '',
                ],
                'separator' => 'before',
                'style_transfer' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'alignment',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left'    => [
                        'title' => __('Left'),
                        'icon' => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => __('Center'),
                        'icon' => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fa fa-align-right',
                    ],
                ],
                'label_block' => false,
                'style_transfer' => true,
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
    public static function registerTestimonialImageStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-testimonial-wrapper .gmt-testimonial-image img'
    ) {
        $widget->addControl(
            $prefix . '__image_size', // Fix missing tab issue
            [
                'label' => __('Image Size'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => $prefix . 'image_border',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
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
                    '{{WRAPPER}} ' . $cssTarget => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
            'section_testimonial',
            [
                'label' => __('Testimonial'),
            ]
        );

        self::registerTestimonialInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_testimonial_content',
            [
                'label' => __('Content'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerSimpleTextStyle($this, self::buildPrefixKey(Text::NAME, 'content'), '.gmt-testimonial-content');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_testimonial_image',
            [
                'label' => __('Image'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_image.url!' => '',
                ],
            ]
        );

        self::registerTestimonialImageStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_testimonial_name',
            [
                'label' => __('Name'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerSimpleTextStyle($this, self::buildPrefixKey(Text::NAME, 'name'), '.gmt-testimonial-name');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_testimonial_job',
            [
                'label' => __('Job'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        Text::registerSimpleTextStyle($this, self::buildPrefixKey(Text::NAME, 'job'), '.gmt-testimonial-job');

        $this->endControlsSection();
    }
}
