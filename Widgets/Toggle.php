<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadow;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticUtils;

/**
 * Class Toggle
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Toggle extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve toggle widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'toggle';
    }

    /**
     * Get widget title.
     *
     * Retrieve toggle widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Toggle');
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
     * Retrieve toggle widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-arrow-circle-right';
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
     * Register toggle widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_toggle',
            [
                'label' => __('Toggle'),
            ]
        );

        $repeater = StaticObjectManager::create(Repeater::class);

        $repeater->addControl(
            'tab_title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('Toggle Title'),
                'label_block' => true,
            ]
        );

        $repeater->addControl(
            'tab_content',
            [
                'label' => __('Content'),
                'type' => Controls::WYSIWYG,
                'default' => __('Toggle Content'),
                'show_label' => false,
            ]
        );

        $this->addControl(
            'tabs',
            [
                'label' => __('Toggle Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'tab_title' => __('Toggle #1'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        'tab_title' => __('Toggle #2'),
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
            'selected_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'separator' => 'before',
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-caret' . (StaticData::isRtl() ? '-left' : '-right'),
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->addControl(
            'selected_active_icon',
            [
                'label' => __('Active Icon'),
                'type' => Controls::ICONS,
                'fa4compatibility' => 'icon_active',
                'default' => [
                    'value' => 'fas fa-caret-up',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'title_html_tag',
            [
                'label' => __('Title HTML Tag'),
                'type' => Controls::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                ],
                'default' => 'div',
                'separator' => 'before',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style',
            [
                'label' => __('Toggle'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'border_width',
            [
                'label' => __('Border Width'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'border-bottom-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'space_between',
            [
                'label' => __('Space Between'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-toggle-item:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->addGroupControl(
            BoxShadow::getType(),
            [
                'name' => 'box_shadow',
                'selector' => '{{WRAPPER}} .gmt-toggle .gmt-toggle-item',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_title',
            [
                'label' => __('Title'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'title_background',
            [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title.gmt-active' => 'color: {{VALUE}};',
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
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .gmt-toggle .gmt-tab-title',
                'scheme' => Typography::TYPOGRAPHY_1,
            ]
        );

        $this->addResponsiveControl(
            'title_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_icon',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
                'condition' => [
                    'selected_icon[value]!' => '',
                ],
            ]
        );

        $this->addControl(
            'icon_align',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Start'),
                        'icon' => 'fas fa-align-left',
                    ],
                    'right' => [
                        'title' => __('End'),
                        'icon' => 'fas fa-align-right',
                    ],
                ],
                'default' => StaticData::isRtl() ? 'right' : 'left',
                'toggle' => false,
                'label_block' => false,
            ]
        );

        $this->addControl(
            'icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title .gmt-toggle-icon i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title .gmt-toggle-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'icon_active_color',
            [
                'label' => __('Active Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title.gmt-active .gmt-toggle-icon i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-title.gmt-active .gmt-toggle-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_space',
            [
                'label' => __('Spacing'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-toggle-icon.gmt-toggle-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-toggle .gmt-toggle-icon.gmt-toggle-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_toggle_style_content',
            [
                'label' => __('Content'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'content_background_color',
            [
                'label' => __('Background'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'content_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'color: {{VALUE}};',
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
                'selector' => '{{WRAPPER}} .gmt-toggle .gmt-tab-content',
                'scheme' => Typography::TYPOGRAPHY_3,
            ]
        );

        $this->addResponsiveControl(
            'content_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', 'em', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-toggle .gmt-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render toggle widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $id_int = substr((string) $this->getIdInt(), 0, 3);

        $has_icon = !empty($settings['selected_icon']['value']); ?>
		<div class="gmt-toggle" role="tablist">
			<?php
            foreach ($settings['tabs'] as $index => $item) :
                $tab_count = $index + 1;

        $tab_title_setting_key = $this->getRepeaterSettingKey('tab_title', 'tabs', $index);

        $tab_content_setting_key = $this->getRepeaterSettingKey('tab_content', 'tabs', $index);

        $this->addRenderAttribute($tab_title_setting_key, [
                    'id' => 'gmt-tab-title-' . $id_int . $tab_count,
                    'class' => [ 'gmt-tab-title' ],
                    'data-tab' => $tab_count,
                    'role' => 'tab',
                    'aria-controls' => 'gmt-tab-content-' . $id_int . $tab_count,
                ]);

        $this->addRenderAttribute($tab_content_setting_key, [
                    'id' => 'gmt-tab-content-' . $id_int . $tab_count,
                    'class' => [ 'gmt-tab-content', 'gmt-clearfix' ],
                    'data-tab' => $tab_count,
                    'role' => 'tabpanel',
                    'aria-labelledby' => 'gmt-tab-title-' . $id_int . $tab_count,
                ]);

        $this->addInlineEditingAttributes($tab_content_setting_key, 'advanced'); ?>
				<div class="gmt-toggle-item">
					<<?= StaticUtils::escapeHtml($settings['title_html_tag']); ?> <?= $this->getRenderAttributeString($tab_title_setting_key); ?>>
						<?php if ($has_icon) : ?>
						<span class="gmt-toggle-icon gmt-toggle-icon-<?= StaticUtils::escapeHtml($settings['icon_align']); ?>" aria-hidden="true">
								<span class="gmt-toggle-icon-closed"><?php Icons::renderIcon($settings['selected_icon']); ?></span>
								<span class="gmt-toggle-icon-opened"><?php Icons::renderIcon($settings['selected_active_icon'], [ 'class' => 'gmt-toggle-icon-opened' ]); ?></span>
						</span>
						<?php endif; ?>
						<a href=""><?= $item['tab_title']; ?></a>
					</<?= StaticUtils::escapeHtml($settings['title_html_tag']); ?>>
					<div <?= $this->getRenderAttributeString($tab_content_setting_key); ?>><?= $this->parseTextEditor($item['tab_content']); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
    }

    /**
     * Render toggle widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<div class="gmt-toggle" role="tablist">
			<#
			if ( settings.tabs ) {
				var tabindex = view.getIDInt().toString().substr( 0, 3 ),
					iconHTML = goomento.helpers.renderIcon( view, settings.selected_icon, {}, 'i' , 'object' ),
					iconActiveHTML = goomento.helpers.renderIcon( view, settings.selected_active_icon, {}, 'i' , 'object' );

				_.each( settings.tabs, function( item, index ) {
					var tabCount = index + 1,
						tabTitleKey = view.getRepeaterSettingKey( 'tab_title', 'tabs', index ),
						tabContentKey = view.getRepeaterSettingKey( 'tab_content', 'tabs', index );

					view.addRenderAttribute( tabTitleKey, {
						'id': 'gmt-tab-title-' + tabindex + tabCount,
						'class': [ 'gmt-tab-title' ],
						'data-tab': tabCount,
						'role': 'tab',
						'aria-controls': 'gmt-tab-content-' + tabindex + tabCount
					} );

					view.addRenderAttribute( tabContentKey, {
						'id': 'gmt-tab-content-' + tabindex + tabCount,
						'class': [ 'gmt-tab-content', 'gmt-clearfix' ],
						'data-tab': tabCount,
						'role': 'tabpanel',
						'aria-labelledby': 'gmt-tab-title-' + tabindex + tabCount
					} );

					view.addInlineEditingAttributes( tabContentKey, 'advanced' );
					#>
					<div class="gmt-toggle-item">
						<{{{ settings.title_html_tag }}} {{{ view.getRenderAttributeString( tabTitleKey ) }}}>
							<# if ( settings.icon || settings.selected_icon ) { #>
							<span class="gmt-toggle-icon gmt-toggle-icon-{{ settings.icon_align }}" aria-hidden="true">
								<# if ( iconHTML && iconHTML.rendered && ! settings.icon ) { #>
									<span class="gmt-toggle-icon-closed">{{{ iconHTML.value }}}</span>
									<span class="gmt-toggle-icon-opened">{{{ iconActiveHTML.value }}}</span>
								<# } else { #>
									<i class="gmt-toggle-icon-closed {{ settings.icon }}"></i>
									<i class="gmt-toggle-icon-opened {{ settings.icon_active }}"></i>
								<# } #>
							</span>
							<# } #>
							<a href="">{{{ item.tab_title }}}</a>
						</{{{ settings.title_html_tag }}}>
						<div {{{ view.getRenderAttributeString( tabContentKey ) }}}>{{{ item.tab_content }}}</div>
					</div>
					<#
				} );
			} #>
		</div>
		<?php
    }
}
