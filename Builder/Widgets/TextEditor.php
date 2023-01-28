<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Exception\BuilderException;

class TextEditor extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'text-editor';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/text_editor.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Text Editor');
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return ['goomento-widget-text-editor'];
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
        return 'fas fa-paragraph';
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
        return [ 'text', 'editor' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerTextEditorInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'editor',
            [
                'label' => '',
                'type' => Controls::WYSIWYG,
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap',
            [
                'label' => __('Drop Cap'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'prefix_class' => 'gmt-drop-cap-',
                'frontend_available' => true,
                'frontend_name' => 'drop_cap',
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
    public static function registerTextEditorStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-text-editor'
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
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' =>  $prefix . 'typography',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $textColumns = range(1, 10);
        $textColumns = array_combine($textColumns, $textColumns);
        $textColumns[''] = __('Default');

        $widget->addResponsiveControl(
            $prefix . 'columns',
            [
                'label' => __('Columns'),
                'type' => Controls::SELECT,
                'separator' => 'before',
                'options' => $textColumns,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'columns: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'column_gap',
            [
                'label' => __('Columns Gap'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px', '%', 'em', 'vw' ],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    '%' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'vw' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                    'em' => [
                        'max' => 10,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerDropCapStyles(
        AbstractWidget $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-drop-cap'
    ) {
        $widget->addControl(
            $prefix . 'drop_cap_view',
            [
                'label' => __('View'),
                'type' => Controls::SELECT,
                'options' => [
                    'default' => __('Default'),
                    'stacked' => __('Stacked'),
                    'framed' => __('Framed'),
                ],
                'default' => 'default',
                'prefix_class' => 'gmt-drop-cap-view-',
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-framed ' . $cssTarget . ', {{WRAPPER}}.gmt-drop-cap-view-default ' . $cssTarget => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-framed ' . $cssTarget => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked ' . $cssTarget => 'color: {{VALUE}};',
                ],
                'condition' => [
                    $prefix . 'drop_cap_view!' => 'default',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 5,
                ],
                'range' => [
                    'px' => [
                        'max' => 30,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'drop_cap_view!' => 'default',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_space',
            [
                'label' => __('Space'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    'body:not(.rtl) {{WRAPPER}} ' . $cssTarget => 'margin-right: {{SIZE}}{{UNIT}};',
                    'body.rtl {{WRAPPER}} ' . $cssTarget => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::SLIDER,
                'size_units' => [ '%', 'px' ],
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'drop_cap_border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    $prefix . 'drop_cap_view' => 'framed',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => $prefix . 'drop_cap_typography',
                'selector' => '{{WRAPPER}} .gmt-drop-cap-letter',
                'exclude' => [
                    'letter_spacing',
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
            'section_editor',
            [
                'label' => __('Text Editor'),
            ]
        );

        self::registerTextEditorInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Text Editor'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTextEditorStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_drop_cap',
            [
                'label' => __('Drop Cap'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_drop_cap' => 'yes',
                ],
            ]
        );

        self::registerDropCapStyles($this);

        $this->endControlsSection();
    }


    /**
     * @inheritDoc
     */
    public function renderPlainContent()
    {
        // In plain mode, render without shortcode
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $this->getSettings('editor');
    }
}
