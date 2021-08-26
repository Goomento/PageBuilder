<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Builder\Utils;

/**
 * Class Alert
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Alert extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve alert widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'alert';
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
     * Retrieve alert widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Alert');
    }

    /**
     * Get widget icon.
     *
     * Retrieve alert widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-bell-o far fa-bell';
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
        return [ 'alert', 'notice', 'message' ];
    }

    /**
     * Register alert widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_alert',
            [
                'label' => __('Alert'),
            ]
        );

        $this->addControl(
            'alert_type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'info',
                'options' => [
                    'info' => __('Info'),
                    'success' => __('Success'),
                    'warning' => __('Warning'),
                    'danger' => __('Danger'),
                ],
                'style_transfer' => true,
            ]
        );

        $this->addControl(
            'alert_title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'placeholder' => __('Enter your title'),
                'default' => __('This is an Alert'),
                'label_block' => true,
            ]
        );

        $this->addControl(
            'alert_description',
            [
                'label' => __('Content'),
                'type' => Controls::TEXTAREA,
                'placeholder' => __('Enter your description'),
                'default' => __('I am a description. Click the edit button to change this text.'),
                'separator' => 'none',
                'show_label' => false,
            ]
        );

        $this->addControl(
            'show_dismiss',
            [
                'label' => __('Dismiss Button'),
                'type' => Controls::SELECT,
                'default' => 'show',
                'options' => [
                    'show' => __('Show'),
                    'hide' => __('Hide'),
                ],
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
            'section_type',
            [
                'label' => __('Alert'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'background',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-alert' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-alert' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'border_left-width',
            [
                'label' => __('Left Border Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-alert' => 'border-left-width: {{SIZE}}{{UNIT}};',
                ],
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
                'selectors' => [
                    '{{WRAPPER}} .gmt-alert-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'alert_title',
                'selector' => '{{WRAPPER}} .gmt-alert-title',
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_description',
            [
                'label' => __('Description'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'description_color',
            [
                'label' => __('Text Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-alert-description' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'alert_description',
                'selector' => '{{WRAPPER}} .gmt-alert-description',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render alert widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        if (Utils::isEmpty($settings['alert_title'])) {
            return;
        }

        if (! empty($settings['alert_type'])) {
            $this->addRenderAttribute('wrapper', 'class', 'gmt-alert gmt-alert-' . $settings['alert_type']);
        }

        $this->addRenderAttribute('wrapper', 'role', 'alert');

        $this->addRenderAttribute('alert_title', 'class', 'gmt-alert-title');

        $this->addInlineEditingAttributes('alert_title', 'none'); ?>
		<div <?= $this->getRenderAttributeString('wrapper'); ?>>
			<span <?= $this->getRenderAttributeString('alert_title'); ?>><?= $settings['alert_title']; ?></span>
			<?php
            if (! Utils::isEmpty($settings['alert_description'])) :
                $this->addRenderAttribute('alert_description', 'class', 'gmt-alert-description');

        $this->addInlineEditingAttributes('alert_description'); ?>
				<span <?= $this->getRenderAttributeString('alert_description'); ?>><?= $settings['alert_description']; ?></span>
			<?php endif; ?>
			<?php if ('show' === $settings['show_dismiss']) : ?>
				<button type="button" class="gmt-alert-dismiss">
					<span aria-hidden="true">&times;</span>
					<span class="gmt-screen-only"><?= __('Dismiss alert'); ?></span>
				</button>
			<?php endif; ?>
		</div>
		<?php
    }

    /**
     * Render alert widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<# if ( settings.alert_title ) {
			view.addRenderAttribute( {
				alert_title: { class: 'gmt-alert-title' },
				alert_description: { class: 'gmt-alert-description' }
			} );

			view.addInlineEditingAttributes( 'alert_title', 'none' );
			view.addInlineEditingAttributes( 'alert_description' );
			#>
			<div class="gmt-alert gmt-alert-{{ settings.alert_type }}" role="alert">
				<span {{{ view.getRenderAttributeString( 'alert_title' ) }}}>{{{ settings.alert_title }}}</span>
				<span {{{ view.getRenderAttributeString( 'alert_description' ) }}}>{{{ settings.alert_description }}}</span>
				<# if ( 'show' === settings.show_dismiss ) { #>
					<button type="button" class="gmt-alert-dismiss">
						<span aria-hidden="true">&times;</span>
						<span class="gmt-screen-only"><?= __('Dismiss alert'); ?></span>
					</button>
				<# } #>
			</div>
		<# } #>
		<?php
    }
}
