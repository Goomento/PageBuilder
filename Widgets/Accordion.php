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
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Schemes\Color;
use Goomento\PageBuilder\Builder\Schemes\Typography;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticEscaper;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Accordion
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class Accordion extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve accordion widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'accordion';
    }

    /**
     * Get widget title.
     *
     * Retrieve accordion widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Accordion');
    }

    /**
     * Get widget icon.
     *
     * Retrieve accordion widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-arrow-circle-o-right far fa-arrow-alt-circle-right';
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
        return [ 'accordion', 'tabs', 'toggle' ];
    }

    /**
     * Register accordion widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_title',
            [
                'label' => __('Accordion'),
            ]
        );

        $repeater = StaticObjectManager::create(Repeater::class);

        $repeater->addControl(
            'tab_title',
            [
                'label' => __('Title & Description'),
                'type' => Controls::TEXT,
                'default' => __('Accordion Title'),
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
            ]
        );

        $repeater->addControl(
            'tab_content',
            [
                'label' => __('Content'),
                'type' => Controls::WYSIWYG,
                'default' => __('Accordion Content'),
                'show_label' => false,
            ]
        );

        $this->addControl(
            'tabs',
            [
                'label' => __('Accordion Items'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'tab_title' => __('Accordion #1'),
                        'tab_content' => __('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.'),
                    ],
                    [
                        'tab_title' => __('Accordion #2'),
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
                    'value' => 'fas fa-plus',
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
                    'value' => 'fas fa-minus',
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
            'section_title_style',
            [
                'label' => __('Accordion'),
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
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item' => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item .gmt-tab-content' => 'border-width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item .gmt-tab-title.gmt-active' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addControl(
            'border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item' => 'border-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item .gmt-tab-content' => 'border-top-color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-item .gmt-tab-title.gmt-active' => 'border-bottom-color: {{VALUE}};',
                ],
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'title_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title' => 'color: {{VALUE}};',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title.gmt-active' => 'color: {{VALUE}};',
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
                'selector' => '{{WRAPPER}} .gmt-accordion .gmt-tab-title',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title .gmt-accordion-icon i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title .gmt-accordion-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'icon_active_color',
            [
                'label' => __('Active Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title.gmt-active .gmt-accordion-icon i:before' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-title.gmt-active .gmt-accordion-icon svg' => 'fill: {{VALUE}};',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-icon.gmt-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .gmt-accordion .gmt-accordion-icon.gmt-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'content_color',
            [
                'label' => __('Color'),
                'type' => Controls::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-content' => 'color: {{VALUE}};',
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
                'selector' => '{{WRAPPER}} .gmt-accordion .gmt-tab-content',
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
                    '{{WRAPPER}} .gmt-accordion .gmt-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render accordion widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();

        $has_icon = ! empty($settings['selected_icon']['value']);
        $id_int = substr((string)$this->getIdInt(), 0, 3); ?>
        <div class="gmt-accordion" role="tablist">
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
            <div class="gmt-accordion-item">
            <<?= $settings['title_html_tag']; ?> <?= $this->getRenderAttributeString($tab_title_setting_key); ?>>
            <?php if ($has_icon) : ?>
                <span class="gmt-accordion-icon gmt-accordion-icon-<?= StaticEscaper::escapeHtml($settings['icon_align']); ?>" aria-hidden="true">
						<span class="gmt-accordion-icon-closed"><?php Icons::renderIcon($settings['selected_icon']); ?></span>
								<span class="gmt-accordion-icon-opened"><?php Icons::renderIcon($settings['selected_active_icon']); ?></span>
							</span>
            <?php endif; ?>
            <a href=""><?= $item['tab_title']; ?></a>
            </<?= $settings['title_html_tag']; ?>>
            <div <?= $this->getRenderAttributeString($tab_content_setting_key); ?>><?= $this->parseTextEditor($item['tab_content']); ?></div>
            </div>
        <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render accordion widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
        <div class="gmt-accordion" role="tablist">
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
            'tabindex': tabindex + tabCount,
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
            <div class="gmt-accordion-item">
                <{{{ settings.title_html_tag }}} {{{ view.getRenderAttributeString( tabTitleKey ) }}}>
                <# if ( settings.icon || settings.selected_icon ) { #>
                <span class="gmt-accordion-icon gmt-accordion-icon-{{ settings.icon_align }}" aria-hidden="true">
								<# if ( iconHTML && iconHTML.rendered && ( ! settings.icon ) ) { #>
									<span class="gmt-accordion-icon-closed">{{{ iconHTML.value }}}</span>
									<span class="gmt-accordion-icon-opened">{{{ iconActiveHTML.value }}}</span>
								<# } else { #>
									<i class="gmt-accordion-icon-closed {{ settings.icon }}"></i>
									<i class="gmt-accordion-icon-opened {{ settings.icon_active }}"></i>
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
