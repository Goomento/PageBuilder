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

/**
 * Class Counter
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Counter extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve counter widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'counter';
    }

    /**
     * Get widget title.
     *
     * Retrieve counter widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Counter');
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
     * Retrieve counter widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-sort-numeric-up-alt';
    }

    /**
     * Retrieve the list of scripts the counter widget depended on.
     *
     * Used to set scripts dependencies required to run the widget.
     *
     *
     * @return array Widget scripts dependencies.
     */
    public function getScriptDepends()
    {
        return [ 'jquery-numerator' ];
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
        return [ 'counter' ];
    }

    /**
     * Register counter widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_counter',
            [
                'label' => __('Counter'),
            ]
        );

        $this->addControl(
            'starting_number',
            [
                'label' => __('Starting Number'),
                'type' => Controls::NUMBER,
                'default' => 0,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'ending_number',
            [
                'label' => __('Ending Number'),
                'type' => Controls::NUMBER,
                'default' => 100,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $this->addControl(
            'prefix',
            [
                'label' => __('Number Prefix'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => 1,
            ]
        );

        $this->addControl(
            'suffix',
            [
                'label' => __('Number Suffix'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => __('Plus'),
            ]
        );

        $this->addControl(
            'duration',
            [
                'label' => __('Animation Duration'),
                'type' => Controls::NUMBER,
                'default' => 2000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $this->addControl(
            'thousand_separator',
            [
                'label' => __('Thousand Separator'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $this->addControl(
            'thousand_separator_char',
            [
                'label' => __('Separator'),
                'type' => Controls::SELECT,
                'condition' => [
                    'thousand_separator' => 'yes',
                ],
                'options' => [
                    '' => 'Default',
                    '.' => 'Dot',
                    ' ' => 'Space',
                ],
            ]
        );

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'label_block' => true,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => __('Cool Number'),
                'placeholder' => __('Cool Number'),
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
            'section_number',
            [
                'label' => __('Number'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'number_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-counter-number-wrapper' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'typography_number',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .gmt-counter-number-wrapper',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::getType(),
                    'value' => Color::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-counter-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'typography_title',
                'scheme' => Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} .gmt-counter-title',
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render counter widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-counter">
			<div class="gmt-counter-number-wrapper">
				<span class="gmt-counter-number-prefix">{{{ settings.prefix }}}</span>
				<span class="gmt-counter-number" data-duration="{{ settings.duration }}" data-to-value="{{ settings.ending_number }}" data-delimiter="{{ settings.thousand_separator ? settings.thousand_separator_char || ',' : '' }}">{{{ settings.starting_number }}}</span>
				<span class="gmt-counter-number-suffix">{{{ settings.suffix }}}</span>
			</div>
			<# if ( settings.title ) {
				#><div class="gmt-counter-title">{{{ settings.title }}}</div><#
			} #>
		</div>
		<?php
    }

    /**
     * Render counter widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('counter', [
            'class' => 'gmt-counter-number',
            'data-duration' => $settings['duration'],
            'data-to-value' => $settings['ending_number'],
            'data-from-value' => $settings['starting_number'],
        ]);

        if (! empty($settings['thousand_separator'])) {
            $delimiter = empty($settings['thousand_separator_char']) ? ',' : $settings['thousand_separator_char'];
            $this->addRenderAttribute('counter', 'data-delimiter', $delimiter);
        } ?>
		<div class="gmt-counter">
			<div class="gmt-counter-number-wrapper">
				<span class="gmt-counter-number-prefix"><?= $settings['prefix']; ?></span>
				<span <?= $this->getRenderAttributeString('counter'); ?>><?= $settings['starting_number']; ?></span>
				<span class="gmt-counter-number-suffix"><?= $settings['suffix']; ?></span>
			</div>
			<?php if ($settings['title']) : ?>
				<div class="gmt-counter-title"><?= $settings['title']; ?></div>
			<?php endif; ?>
		</div>
		<?php
    }
}
