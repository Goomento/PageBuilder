<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;

class Counter extends AbstractWidget
{

    const NAME = 'counter';

    protected $template = 'Goomento_PageBuilder::widgets/counter.phtml';

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-sort-numeric-up-alt';
    }

    /**
     * @inheritDoc
     */
    public function getScriptDepends()
    {
        return [ 'jquery-numerator' ];
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'counter' ];
    }

    /**
     * @param AbstractElement $widget
     * @param string $prefix
     * @param array $args
     * @return void
     */
    public static function registerCounterInterface(AbstractElement $widget, string $prefix = self::NAME . '_', array $args = [])
    {
        $widget->addControl(
            $prefix . 'starting_number',
            $args + [
                'label' => __('Starting Number'),
                'type' => Controls::NUMBER,
                'default' => 0,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'ending_number',
            $args + [
                'label' => __('Ending Number'),
                'type' => Controls::NUMBER,
                'default' => 100,
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'prefix',
            $args + [
                'label' => __('Number Prefix'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => 1,
            ]
        );

        $widget->addControl(
            $prefix . 'suffix',
            $args + [
                'label' => __('Number Suffix'),
                'type' => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'default' => '',
                'placeholder' => __('Plus'),
            ]
        );

        $widget->addControl(
            $prefix . 'duration',
            $args + [
                'label' => __('Animation Duration'),
                'type' => Controls::NUMBER,
                'default' => 2000,
                'min' => 100,
                'step' => 100,
            ]
        );

        $widget->addControl(
            $prefix . 'thousand_separator',
            $args + [
                'label' => __('Thousand Separator'),
                'type' => Controls::SWITCHER,
                'default' => 'yes',
                'label_on' => __('Show'),
                'label_off' => __('Hide'),
            ]
        );

        $widget->addControl(
            $prefix . 'thousand_separator_char',
            $args + [
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

        $widget->addControl(
            $prefix . 'title',
            $args + [
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
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerNumberStyles(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        string         $cssTarget = '.gmt-counter-number-wrapper',
        array          $args = []
    )
    {
        $widget->addControl(
            $prefix . 'number_color',
            $args + [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            $args + [
                'name' => $prefix . 'typography_number',
                'scheme' => Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * @param AbstractWidget $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param array $args
     * @return void
     */
    public static function registerTitleStyles(
        AbstractWidget $widget,
        string         $prefix = self::NAME . '_',
        string         $cssTarget = '.gmt-counter-title',
        array          $args = []
    )
    {
        $widget->addControl(
            $prefix . 'title_color',
            $args + [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_2,
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'color: {{VALUE}};',
                ],
            ]
        );

        $widget->addGroupControl(
            TypographyGroup::NAME,
            $args + [
                'name' => $prefix . 'typography_title',
                'scheme' => Typography::TYPOGRAPHY_2,
                'selector' => '{{WRAPPER}} ' . $cssTarget,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_counter',
            [
                'label' => __('Counter'),
            ]
        );

        self::registerCounterInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_number',
            [
                'label' => __('Number'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerNumberStyles($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerTitleStyles($this);

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-counter">
			<div class="gmt-counter-number-wrapper">
				<span class="gmt-counter-number-prefix">{{{ settings.counter_prefix }}}</span>
				<span class="gmt-counter-number" data-duration="{{ settings.counter_duration }}" data-to-value="{{ settings.counter_ending_number }}" data-delimiter="{{ settings.counter_thousand_separator ? settings.counter_thousand_separator_char || ',' : '' }}">{{{ settings.counter_starting_number }}}</span>
				<span class="gmt-counter-number-suffix">{{{ settings.counter_suffix }}}</span>
			</div>
			<# if ( settings.counter_title ) {
				#><div class="gmt-counter-title">{{{ settings.counter_title }}}</div><#
			} #>
		</div>
		<?php
    }
}
