<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Utils;

/**
 * Class Progress
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Progress extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve progress widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'progress';
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * Get widget title.
     *
     * Retrieve progress widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Progress Bar');
    }

    /**
     * Get widget icon.
     *
     * Retrieve progress widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-percentage';
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
        return [ 'progress', 'bar' ];
    }

    /**
     * Register progress widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_progress',
            [
                'label' => __('Progress Bar'),
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('Enter your title'),
                'default' => __('My Skill'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'progress_type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => [
                    '' => __('Default'),
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
            ]
        );

        $this->addControl(
            'percent',
            [
                'label' => __('Percentage'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 50,
                    'unit' => '%',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $this->addControl('display_percentage', [
            'label' => __('Display Percentage'),
            'type' => Controls::SELECT,
            'default' => 'show',
            'options' => [
                'show' => __('Show'),
                'hide' => __('Hide'),
            ],
        ]);

        $this->addControl(
            'inner_text',
            [
                'label' => __('Inner Text'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('e.g. Web Designer'),
                'default' => __('Web Designer'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'view',
            [
                'label' => __('View'),
                'type' => Controls::HIDDEN,
                'default' => 'traditional',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_progress_style',
            [
                'label' => __('Progress Bar'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'bar_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-wrapper .gmt-progress-bar' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'bar_bg_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'bar_height',
            [
                'label' => __('Height'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-bar' => 'height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'bar_border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::SLIDER,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-wrapper' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->addControl(
            'inner_text_heading',
            [
                'label' => __('Inner Text'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'bar_inline_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-progress-bar' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'bar_inner_typography',
                'selector' => '{{WRAPPER}} .gmt-progress-bar',
                'exclude' => [
                    'line_height',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title Style'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'typography',
                'selector' => '{{WRAPPER}} .gmt-title',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render progress widget output on the frontend.
     * Make sure value does no exceed 100%.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $progress_percentage = is_numeric($settings['percent']['size']) ? $settings['percent']['size'] : '0';
        if (100 < $progress_percentage) {
            $progress_percentage = 100;
        }

        $this->addRenderAttribute('wrapper', [
            'class' => 'gmt-progress-wrapper',
            'role' => 'progressbar',
            'aria-valuemin' => '0',
            'aria-valuemax' => '100',
            'aria-valuenow' => $progress_percentage,
            'aria-valuetext' => $settings['inner_text'],
        ]);

        if (! empty($settings['progress_type'])) {
            $this->addRenderAttribute('wrapper', 'class', 'progress-' . $settings['progress_type']);
        }

        $this->addRenderAttribute('progress-bar', [
            'class' => 'gmt-progress-bar',
            'data-max' => $progress_percentage,
        ]);

        $this->addRenderAttribute('inner_text', [
            'class' => 'gmt-progress-text',
        ]);

        $this->addInlineEditingAttributes('inner_text');

        if (! Utils::isEmpty($settings['title'])) { ?>
			<span class="gmt-title"><?= $settings['title']; ?></span>
		<?php } ?>

		<div <?= $this->getRenderAttributeString('wrapper'); ?>>
			<div <?= $this->getRenderAttributeString('progress-bar'); ?>>
				<span <?= $this->getRenderAttributeString('inner_text'); ?>><?= $settings['inner_text']; ?></span>
				<?php if ('hide' !== $settings['display_percentage']) { ?>
					<span class="gmt-progress-percentage"><?= $progress_percentage; ?>%</span>
				<?php } ?>
			</div>
		</div>
		<?php
    }

    /**
     * Render progress widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<#
		let progress_percentage = 0;
		if ( ! isNaN( settings.percent.size ) ) {
			progress_percentage = 100 < settings.percent.size ? 100 : settings.percent.size;
		}

		view.addRenderAttribute( 'progressWrapper', {
			'class': [ 'gmt-progress-wrapper', 'progress-' + settings.progress_type ],
			'role': 'progressbar',
			'aria-valuemin': '0',
			'aria-valuemax': '100',
			'aria-valuenow': progress_percentage,
			'aria-valuetext': settings.inner_text
		} );

		view.addRenderAttribute( 'inner_text', {
			'class': 'gmt-progress-text'
		} );

		view.addInlineEditingAttributes( 'inner_text' );
		#>
		<# if ( settings.title ) { #>
			<span class="gmt-title">{{{ settings.title }}}</span><#
		} #>
		<div {{{ view.getRenderAttributeString( 'progressWrapper' ) }}}>
			<div class="gmt-progress-bar" data-max="{{ progress_percentage }}">
				<span {{{ view.getRenderAttributeString( 'inner_text' ) }}}>{{{ settings.inner_text }}}</span>
				<# if ( 'hide' !== settings.display_percentage ) { #>
					<span class="gmt-progress-percentage">{{{ progress_percentage }}}%</span>
				<# } #>
			</div>
		</div>
		<?php
    }
}
