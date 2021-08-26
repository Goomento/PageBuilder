<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\Typography;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;

/**
 * Class TextEditor
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class TextEditor extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve text editor widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'text-editor';
    }

    /**
     * Get widget title.
     *
     * Retrieve text editor widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Text Editor');
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * Get widget icon.
     *
     * Retrieve text editor widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-pencil-alt';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the text editor widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     *
     * @return array Widget categories.
     */
    public function getCategories()
    {
        return [ 'basic' ];
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the list of keywords the widget belongs to.
     *
     *
     * @return array Widget keywords.
     */
    public function getKeywords()
    {
        return [ 'text', 'editor' ];
    }

    /**
     * Register text editor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_editor',
            [
                'label' => __('Text Editor'),
            ]
        );

        $this->addControl(
            'editor',
            [
                'label' => '',
                'type' => Controls::WYSIWYG,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
            ]
        );

        $this->addControl(
            'drop_cap',
            [
                'label' => __('Drop Cap'),
                'type' => Controls::SWITCHER,
                'label_off' => __('Off'),
                'label_on' => __('On'),
                'prefix_class' => 'gmt-drop-cap-',
                'frontend_available' => true,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_style',
            [
                'label' => __('Text Editor'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addResponsiveControl(
            'align',
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
                    '{{WRAPPER}} .gmt-text-editor' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'text_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}}' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            Typography::getType(),
            [
                'name' => 'typography',
                'scheme' => \Goomento\PageBuilder\Builder\Schemes\Typography::TYPOGRAPHY_3,
            ]
        );

        $text_columns = range(1, 10);
        $text_columns = array_combine($text_columns, $text_columns);
        $text_columns[''] = __('Default');

        $this->addResponsiveControl(
            'text_columns',
            [
                'label' => __('Columns'),
                'type' => Controls::SELECT,
                'separator' => 'before',
                'options' => $text_columns,
                'selectors' => [
                    '{{WRAPPER}} .gmt-text-editor' => 'columns: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'column_gap',
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
                    '{{WRAPPER}} .gmt-text-editor' => 'column-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_drop_cap',
            [
                'label' => __('Drop Cap'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'drop_cap' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_view',
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
                'condition' => [
                    'drop_cap' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked .gmt-drop-cap' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-framed .gmt-drop-cap, {{WRAPPER}}.gmt-drop-cap-view-default .gmt-drop-cap' => 'color: {{VALUE}}; border-color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
                'condition' => [
                    'drop_cap' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}}.gmt-drop-cap-view-framed .gmt-drop-cap' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}}.gmt-drop-cap-view-stacked .gmt-drop-cap' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'drop_cap_view!' => 'default',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_size',
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
                    '{{WRAPPER}} .gmt-drop-cap' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'drop_cap_view!' => 'default',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_space',
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
                    'body:not(.rtl) {{WRAPPER}} .gmt-drop-cap' => 'margin-right: {{SIZE}}{{UNIT}};',
                    'body.rtl {{WRAPPER}} .gmt-drop-cap' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_border_radius',
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
                    '{{WRAPPER}} .gmt-drop-cap' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'drop_cap_border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::DIMENSIONS,
                'selectors' => [
                    '{{WRAPPER}} .gmt-drop-cap' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'drop_cap_view' => 'framed',
                ],
            ]
        );

        $this->addGroupControl(
            Typography::getType(),
            [
                'name' => 'drop_cap_typography',
                'selector' => '{{WRAPPER}} .gmt-drop-cap-letter',
                'exclude' => [
                    'letter_spacing',
                ],
                'condition' => [
                    'drop_cap' => 'yes',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render text editor widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $editor_content = $this->getSettingsForDisplay('editor');

        $editor_content = $this->parseTextEditor($editor_content);

        $this->addRenderAttribute('editor', 'class', [ 'gmt-text-editor', 'gmt-clearfix' ]);

        $this->addInlineEditingAttributes('editor', 'advanced'); ?>
		<div <?= $this->getRenderAttributeString('editor'); ?>><?= $editor_content; ?></div>
		<?php
    }

    /**
     * Render text editor widget as plain content.
     *
     * Override the default behavior by printing the content without rendering it.
     *
     */
    public function renderPlainContent()
    {
        // In plain mode, render without shortcode
        echo $this->getSettings('editor');
    }

    /**
     * Render text editor widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		view.addRenderAttribute( 'editor', 'class', [ 'gmt-text-editor', 'gmt-clearfix' ] );

		view.addInlineEditingAttributes( 'editor', 'advanced' );
		#>
		<div {{{ view.getRenderAttributeString( 'editor' ) }}}>{{{ settings.editor }}}</div>
		<?php
    }
}
