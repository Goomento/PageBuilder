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
use Goomento\PageBuilder\Builder\Controls\Groups\ImageSizeGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class ImageBox extends AbstractWidget
{
    /**
     * @inheirtDoc
     */
    const NAME = 'image-box';

    /**
     * @inheirtDoc
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
     * @return void
     * @throws BuilderException
     */
    public static function registerBoxStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-image-box-wrapper'
    ) {
        $widget->addControl(
            $prefix . 'background_color',
            [
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
            [
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
            [
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
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerImageBoxInterface(
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
                'default' => 'full',
                'separator' => 'none',
            ]
        );

        $widget->addControl(
            $prefix . 'title_text',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('This is the heading'),
                'placeholder' => __('Enter your title'),
                'label_block' => true,
            ]
        );

        $widget->addControl(
            $prefix . 'description_text',
            [
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                'placeholder' => __('Enter your description'),
                'separator' => 'none',
                'rows' => 10,
                'show_label' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'placeholder' => __('https://your-link.com'),
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'position',
            [
                'label' => __('Image Position'),
                'type' => Controls::CHOOSE,
                'default' => 'top',
                'options' => [
                    'left' => [
                        'title' => __('Left'),
                        'icon' => 'fas fa-angle-left',
                    ],
                    'top' => [
                        'title' => __('Top'),
                        'icon' => 'fas fa-angle-up',
                    ],
                    'right' => [
                        'title' => __('Right'),
                        'icon' => 'fas fa-angle-right',
                    ],
                ],
                'prefix_class' => 'gmt-position-',
                'toggle' => false,
            ]
        );

        $widget->addControl(
            $prefix . 'title_size',
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
        string $cssTarget = '.gmt-image-box-img'
    ) {
        $widget->addResponsiveControl(
            $prefix . 'image_space',
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
                    '{{WRAPPER}}.gmt-position-right ' . $cssTarget => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-left ' . $cssTarget => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}}.gmt-position-top ' . $cssTarget => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '(mobile){{WRAPPER}} ' . $cssTarget => 'margin-bottom: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'image_size',
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
                    '{{WRAPPER}} .gmt-image-box-wrapper ' . $cssTarget => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        Banner::registerImageStyle($widget, $prefix, $cssTarget);
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

        self::registerImageBoxInterface($this);

        $this->endControlsSection();


        $this->startControlsSection(
            'section_wrapper_style',
            [
                'label' => __('General'),
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

        self::registerImageStyle($this, self::buildPrefixKey('image'));

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style_content',
            [
                'label' => __('Content'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        IconBox::registerIconBoxContentStyle(
            $this,
            self::buildPrefixKey(),
            '.gmt-image-box-wrapper',
            '.gmt-image-box-content .gmt-image-box-title',
            '.gmt-image-box-content .gmt-image-box-description'
        );

        $this->endControlsSection();
    }
}
