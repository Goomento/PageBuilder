<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;

class Text extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    const NAME = 'text';

    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/text.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Text');
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
        return 'fas fa-font';
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
        return [ 'heading', 'title', 'text' ];
    }


    /**
     * Share Text interface
     *
     * @param ControlsStack $widget
     * @param string $prefix
     * @throws BuilderException
     */
    public static function registerTextInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXTAREA,
                'placeholder' => __('Enter your text'),
                'default' => __('Add Your Text Here'),
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,

                'default' => [
                    'url' => '',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'size',
            [
                'label' => __('Size'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Default'),
                    'small' => __('Small'),
                    'medium' => __('Medium'),
                    'large' => __('Large'),
                    'xl' => __('XL'),
                    'xxl' => __('XXL'),
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'tag',
            [
                'label' => __('HTML Tag'),
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
                    'code' => 'code',
                    'pre' => 'pre',
                ],
                'default' => 'h2',
            ]
        );
    }

    /**
     * Share Text style
     *
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws BuilderException
     */
    public static function registerSimpleTextStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-text-title'
    ) {
        $widget->addControl(
            $prefix . 'color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * Share Text style
     *
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @throws BuilderException
     */
    public static function registerTextStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-text-title'
    ) {
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

        self::registerSimpleTextStyle($widget, $prefix, $cssTarget);

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            [
                'name' => $prefix . 'shadow',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addControl(
            $prefix . 'blend_mode',
            [
                'label' => __('Blend Mode'),
                'type' => Controls::SELECT,
                'options' => [
                    '' => __('Normal'),
                    'multiply' => 'Multiply',
                    'screen' => 'Screen',
                    'overlay' => 'Overlay',
                    'darken' => 'Darken',
                    'lighten' => 'Lighten',
                    'color-dodge' => 'Color Dodge',
                    'saturation' => 'Saturation',
                    'color' => 'Color',
                    'difference' => 'Difference',
                    'exclusion' => 'Exclusion',
                    'hue' => 'Hue',
                    'luminosity' => 'Luminosity',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'mix-blend-mode: {{VALUE}}',
                ]
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
            ]
        );

        self::registerTextInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title_style',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTextStyle($this);

        $this->endControlsSection();
    }
}
