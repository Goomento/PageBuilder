<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TextShadowGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;

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
     * @param AbstractWidget $widget
     * @param string $prefix
     */
    public static function registerTextInterface(AbstractWidget $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'title',
            $args + [
                'label' => __('Title'),
                'type' => Controls::TEXTAREA,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your text'),
                'default' => __('Add Your Text Here'),
            ]
        );

        $widget->addControl(
            $prefix . 'link',
            $args + [
                'label' => __('Link'),
                'type' => Controls::URL,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => [
                    'url' => '',
                ],
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'size',
            $args + [
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
            $args + [
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
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     */
    public static function registerSimpleTextStyle(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        string         $cssTarget = '.gmt-text-title',
        array          $args = []
    )
    {
        $widget->addControl(
            $prefix . 'color',
            $args + [
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
            $args + [
                'name' => $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_3,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * Share Text style
     *
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     */
    public static function registerTextStyle(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        string         $cssTarget = '.gmt-text-title',
        array          $args = []
    )
    {
        $widget->addResponsiveControl(
            $prefix . 'align',
            $args + [
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

        self::registerSimpleTextStyle($widget, $prefix, $cssTarget, $args);

        $widget->addGroupControl(
            TextShadowGroup::NAME,
            $args + [
                'name' => $prefix . 'shadow',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );

        $widget->addControl(
            $prefix . 'blend_mode',
            $args + [
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

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<#
            var title = settings.text_title;

            if ( '' !== settings.text_link.url ) {
                title = '<a href="' + settings.text_link.url + '">' + title + '</a>';
            }

            view.addRenderAttribute( 'title', 'class', [ 'gmt-text-title', 'gmt-size-' + settings.text_size ] );

            view.addInlineEditingAttributes( 'title' );

            var title_html = '<' + settings.text_tag  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + settings.text_tag + '>';

            print( title_html );
		#>
		<?php
    }
}
