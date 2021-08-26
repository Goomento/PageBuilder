<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Tabs
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Tabs extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve tabs widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'tabs';
    }

    /**
     * Get widget title.
     *
     * Retrieve tabs widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Tabs');
    }

    /**
     * Get widget icon.
     *
     * Retrieve tabs widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-folder-o far fa-folder';
    }

    /**
     * @inheirtDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
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
        return [ 'tabs', 'accordion', 'toggle' ];
    }

    /**
     * Register tabs widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_tabs',
            [
                'label' => __('Tabs'),
            ]
        );

        $repeater = StaticObjectManager::get(Repeater::class);

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
            'view',
            [
                'label' => __('View'),
                'type' => Controls::HIDDEN,
                'default' => 'traditional',
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
                    'type' => Color::getType(),
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
                    'type' => Color::getType(),
                    'value' => Color::COLOR_4,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
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
                    'type' => Color::getType(),
                    'value' => Color::COLOR_3,
                ],
            ]
        );

        $this->addGroupControl(
            \Goomento\PageBuilder\Builder\Controls\Groups\Typography::getType(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .gmt-tab-content',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render tabs widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $tabs = $this->getSettingsForDisplay('tabs');

        $id_int = substr((string) $this->getIdInt(), 0, 3); ?>
		<div class="gmt-tabs" role="tablist">
			<div class="gmt-tabs-wrapper">
				<?php
                foreach ($tabs as $index => $item) :
                    $tab_count = $index + 1;

        $tab_title_setting_key = $this->getRepeaterSettingKey('tab_title', 'tabs', $index);

        $this->addRenderAttribute($tab_title_setting_key, [
                        'id' => 'gmt-tab-title-' . $id_int . $tab_count,
                        'class' => [ 'gmt-tab-title', 'gmt-tab-desktop-title' ],
                        'data-tab' => $tab_count,
                        'role' => 'tab',
                        'aria-controls' => 'gmt-tab-content-' . $id_int . $tab_count,
                    ]); ?>
					<div <?= $this->getRenderAttributeString($tab_title_setting_key); ?>><a href=""><?= $item['tab_title']; ?></a></div>
				<?php endforeach; ?>
			</div>
			<div class="gmt-tabs-content-wrapper">
				<?php
                foreach ($tabs as $index => $item) :
                    $tab_count = $index + 1;

        $tab_content_setting_key = $this->getRepeaterSettingKey('tab_content', 'tabs', $index);

        $tab_title_mobile_setting_key = $this->getRepeaterSettingKey('tab_title_mobile', 'tabs', $tab_count);

        $this->addRenderAttribute($tab_content_setting_key, [
                        'id' => 'gmt-tab-content-' . $id_int . $tab_count,
                        'class' => [ 'gmt-tab-content', 'gmt-clearfix' ],
                        'data-tab' => $tab_count,
                        'role' => 'tabpanel',
                        'aria-labelledby' => 'gmt-tab-title-' . $id_int . $tab_count,
                    ]);

        $this->addRenderAttribute($tab_title_mobile_setting_key, [
                        'class' => [ 'gmt-tab-title', 'gmt-tab-mobile-title' ],
                        'data-tab' => $tab_count,
                        'role' => 'tab',
                    ]);

        $this->addInlineEditingAttributes($tab_content_setting_key, 'advanced'); ?>
					<div <?= $this->getRenderAttributeString($tab_title_mobile_setting_key); ?>><?= $item['tab_title']; ?></div>
					<div <?= $this->getRenderAttributeString($tab_content_setting_key); ?>><?= $this->parseTextEditor($item['tab_content']); ?></div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
    }

    /**
     * Render tabs widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
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
