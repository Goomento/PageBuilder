<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\TypographyGroup;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Tabs extends AbstractWidget
{

    const NAME = 'tabs';

    /**
     * @var string
     */
    protected $template = 'Goomento_PageBuilder::widgets/tabs.phtml';

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Tabs');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'far fa-caret-square-down';
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
    public function getKeywords()
    {
        return [ 'tabs', 'accordion', 'toggle' ];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_tabs',
            [
                'label' => __('Tabs'),
            ]
        );

        /** @var Repeater $repeater */
        $repeater = ObjectManagerHelper::create(Repeater::class);

        $repeater->addControl(
            'tab_title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('Tab Title'),
                'placeholder' => __('Tab Title'),
                'label_block' => true,
            ]
        );

        $repeater->addControl(
            'tab_content',
            [
                'label' => __('Content'),
                'default' => __('Tab Content'),
                'placeholder' => __('Tab Content'),
                'type' => Controls::WYSIWYG,
                'show_label' => false,
            ]
        );

        $this->addControl(
            'tabs',
            [
                'label' => __('Tabs Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'tab_title' => __('Tab #1'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        'tab_title' => __('Tab #2'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                ],
                'title_field' => '{{{ tab_title }}}',
            ]
        );

        $this->addControl(
            'type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
                'prefix_class' => 'gmt-tabs-view-',
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_tabs_style',
            [
                'label' => __('Tabs'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'navigation_width',
            [
                'label' => __('Navigation Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'unit' => '%',
                ],
                'range' => [
                    '%' => [
                        'min' => 10,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-tabs-wrapper' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'type' => 'vertical',
                ],
            ]
        );

        $this->addControl(
            'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title, {{WRAPPER}} .gmt-tab-title:before, {{WRAPPER}} .gmt-tab-title:after, {{WRAPPER}} .gmt-tab-content, {{WRAPPER}} .gmt-tabs-content-wrapper' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-mobile-title, {{WRAPPER}} .gmt-tab-desktop-title.gmt-active, {{WRAPPER}} .gmt-tab-title:before, {{WRAPPER}} .gmt-tab-title:after, {{WRAPPER}} .gmt-tab-content, {{WRAPPER}} .gmt-tabs-content-wrapper' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'background_color',
            [
                'label' => __('Background Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-desktop-title.gmt-active' => 'background-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-tabs-content-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'heading_title',
            [
                'label' => __('Title'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'tab_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_1,
                ],
            ]
        );

        $this->addControl(
            'tab_active_color',
            [
                'label' => __('Active Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-title.gmt-active' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_4,
                ],
            ]
        );

        $this->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => 'tab_typography',
                'selector' => '{{WRAPPER}} .gmt-tab-title',
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $this->addControl(
            'heading_content',
            [
                'label' => __('Content'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'content_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-tab-content' => 'color: {{VALUE}};',
                ],
                'scheme' => [
                    'type' => Color::NAME,
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            TypographyGroup::NAME,
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .gmt-tab-content',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * @inheritDoc
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-tabs" role="tablist">
			<#
			if ( settings.tabs ) {
				var tabindex = view.getIDInt().toString().substr( 0, 3 );
				#>
				<div class="gmt-tabs-wrapper">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1;
						#>
						<div id="gmt-tab-title-{{ tabindex + tabCount }}" class="gmt-tab-title gmt-tab-desktop-title" data-tab="{{ tabCount }}" role="tab" aria-controls="gmt-tab-content-{{ tabindex + tabCount }}"><a href="">{{{ item.tab_title }}}</a></div>
					<# } ); #>
				</div>
				<div class="gmt-tabs-content-wrapper">
					<#
					_.each( settings.tabs, function( item, index ) {
						var tabCount = index + 1,
							tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs',index );

						view.addRenderAttribute( tabContentKey, {
							'id': 'gmt-tab-content-' + tabindex + tabCount,
							'class': [ 'gmt-tab-content', 'gmt-clearfix', 'gmt-repeater-item-' + item._id ],
							'data-tab': tabCount,
							'role' : 'tabpanel',
							'aria-labelledby' : 'gmt-tab-title-' + tabindex + tabCount
						} );

						view.addInlineEditingAttributes( tabContentKey, 'advanced' );
						#>
						<div class="gmt-tab-title gmt-tab-mobile-title" data-tab="{{ tabCount }}" role="tab">{{{ item.tab_title }}}</div>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
					<# } ); #>
				</div>
			<# } #>
		</div>
		<?php
    }
}
