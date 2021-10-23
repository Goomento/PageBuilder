<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Widgets;

use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Controls\Groups\Border;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticUtils;

/**
 * Class SocialIcons
 * @package Goomento\PageBuilder\Builder\Widgets
 */
class SocialIcons extends Widget
{

    /**
     * Get widget name.
     *
     * Retrieve social icons widget name.
     *
     *
     * @return string Widget name.
     */
    public function getName()
    {
        return 'social-icons';
    }

    /**
     * Get widget title.
     *
     * Retrieve social icons widget title.
     *
     *
     * @return string Widget title.
     */
    public function getTitle()
    {
        return __('Social Icons');
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
     * Retrieve social icons widget icon.
     *
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fa-twitter fab fa-twitter';
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
        return [ 'social', 'icon', 'link' ];
    }

    /**
     * Register social icons widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_social_icon',
            [
                'label' => __('Social Icons'),
            ]
        );
        /** @var Repeater $repeater */
        $repeater = StaticObjectManager::create(Repeater::class);

        $repeater->addControl(
            'social_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
                'fa4compatibility' => 'social',
                'label_block' => true,
                'default' => [
                    'value' => 'fab fa-wordpress',
                    'library' => 'fa-brands',
                ],
                'recommended' => [
                    'fa-brands' => [
                        'android',
                        'apple',
                        'behance',
                        'bitbucket',
                        'codepen',
                        'delicious',
                        'deviantart',
                        'digg',
                        'dribbble',
                        'facebook',
                        'flickr',
                        'foursquare',
                        'free-code-camp',
                        'github',
                        'gitlab',
                        'globe',
                        'google-plus',
                        'houzz',
                        'instagram',
                        'jsfiddle',
                        'linkedin',
                        'medium',
                        'meetup',
                        'mixcloud',
                        'odnoklassniki',
                        'pinterest',
                        'product-hunt',
                        'reddit',
                        'shopping-cart',
                        'skype',
                        'slideshare',
                        'snapchat',
                        'soundcloud',
                        'spotify',
                        'stack-overflow',
                        'steam',
                        'stumbleupon',
                        'telegram',
                        'thumb-tack',
                        'tripadvisor',
                        'tumblr',
                        'twitch',
                        'twitter',
                        'viber',
                        'vimeo',
                        'vk',
                        'weibo',
                        'weixin',
                        'whatsapp',
                        'wordpress',
                        'xing',
                        'yelp',
                        'youtube',
                        '500px',
                    ],
                    'fa-solid' => [
                        'envelope',
                        'link',
                        'rss',
                    ],
                ],
            ]
        );

        $repeater->addControl(
            'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'label_block' => true,
                'default' => [
                    'is_external' => 'true',
                ],
                'dynamic' => [
                    'active' => true,
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );

        $repeater->addControl(
            'item_icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Official Color'),
                    'custom' => __('Custom'),
                ],
            ]
        );

        $repeater->addControl(
            'item_icon_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'item_icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $repeater->addControl(
            'item_icon_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'item_icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'social_icon_list',
            [
                'label' => __('Social Icons'),
                'type' => Controls::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'social_icon' => [
                            'value' => 'fab fa-facebook',
                            'library' => 'fa-brands',
                        ],
                    ],
                    [
                        'social_icon' => [
                            'value' => 'fab fa-twitter',
                            'library' => 'fa-brands',
                        ],
                    ],
                    [
                        'social_icon' => [
                            'value' => 'fab fa-google-plus',
                            'library' => 'fa-brands',
                        ],
                    ],
                ],
                'title_field' => '<# var migrated = "undefined" !== typeof __fa4_migrated, social = ( "undefined" === typeof social ) ? false : social; #>',
            ]
        );

        $this->addControl(
            'shape',
            [
                'label' => __('Shape'),
                'type' => Controls::SELECT,
                'default' => 'rounded',
                'options' => [
                    'rounded' => __('Rounded'),
                    'square' => __('Square'),
                    'circle' => __('Circle'),
                ],
                'prefix_class' => 'gmt-shape-',
            ]
        );

        $this->addResponsiveControl(
            'align',
            [
                'label' => __('Alignment'),
                'type' => Controls::CHOOSE,
                'options' => [
                    'left'    => [
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
                ],
                'default' => 'center',
                'selectors' => [
                    '{{WRAPPER}}' => 'text-align: {{VALUE}};',
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
            'section_social_style',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'icon_color',
            [
                'label' => __('Color'),
                'type' => Controls::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => __('Official Color'),
                    'custom' => __('Custom'),
                ],
            ]
        );

        $this->addControl(
            'icon_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'icon_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-social-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_size',
            [
                'label' => __('Size'),
                'type' => Controls::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 6,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->addResponsiveControl(
            'icon_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon' => 'padding: {{SIZE}}{{UNIT}};',
                ],
                'default' => [
                    'unit' => 'em',
                ],
                'tablet_default' => [
                    'unit' => 'em',
                ],
                'mobile_default' => [
                    'unit' => 'em',
                ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                    ],
                ],
            ]
        );

        $icon_spacing = StaticData::isRtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};';

        $this->addResponsiveControl(
            'icon_spacing',
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
                    '{{WRAPPER}} .gmt-social-icon:not(:last-child)' => $icon_spacing,
                ],
            ]
        );

        $this->addGroupControl(
            Border::getType(),
            [
                'name' => 'image_border', // We know this mistake - TODO: 'icon_border' (for hover control condition also)
                'selector' => '{{WRAPPER}} .gmt-social-icon',
                'separator' => 'before',
            ]
        );

        $this->addControl(
            'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_social_hover',
            [
                'label' => __('Icon Hover'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'hover_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon:hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .gmt-social-icon:hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    'image_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-social-icon:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Render social icons widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     */
    protected function render()
    {
        $settings = $this->getSettingsForDisplay();
        $fallback_defaults = [
            'fab fa-facebook',
            'fab fa-twitter',
            'fab fa-google-plus',
        ];

        $class_animation = '';

        if (! empty($settings['hover_animation'])) {
            $class_animation = ' gmt-animation-' . $settings['hover_animation'];
        }
        ?>
		<div class="gmt-social-icons-wrapper">
			<?php
            foreach ($settings['social_icon_list'] as $index => $item) {
                $social = '';

                // add old default
                if (empty($item['social'])) {
                    $item['social'] = $fallback_defaults[$index] ?? 'fab fa-magento';
                }

                if (! empty($item['social'])) {
                    $social = str_replace('fas fa-', '', $item['social']);
                }

                $link_key = 'link_' . $index;

                $this->addRenderAttribute($link_key, 'href', $item['link']['url']);

                $className = str_replace('fab fa-', '', $social);
                $className = str_replace('fas fa-', '', $className);

                $this->addRenderAttribute($link_key, 'class', [
                    'gmt-icon',
                    'gmt-social-icon',
                    'gmt-social-icon-' . $className . $class_animation,
                    'gmt-repeater-item-' . $item['_id'],
                ]);

                if ($item['link']['is_external']) {
                    $this->addRenderAttribute($link_key, 'target', '_blank');
                }

                if ($item['link']['nofollow']) {
                    $this->addRenderAttribute($link_key, 'rel', 'nofollow');
                } ?>
				<a <?= $this->getRenderAttributeString($link_key); ?>>
					<span class="gmt-screen-only"><?= ucwords($social); ?></span>
					<?php Icons::renderIcon($item['social_icon']); ?>
				</a>
			<?php
            } ?>
		</div>
		<?php
    }

    /**
     * Render social icons widget output in the editor.
     *
     * Written as a Backbone JavaScript template and used to generate the live preview.
     *
     */
    protected function contentTemplate()
    {
        ?>
		<# var iconsHTML = {}; #>
		<div class="gmt-social-icons-wrapper">
			<# _.each( settings.social_icon_list, function( item, index ) {
				var link = item.link ? item.link.url : '',
					social = goomento.helpers.getSocialNetworkNameFromIcon( item.social_icon, item.social, false );
                    className = (item.social_icon.value + '').replace('fab fa-', '').replace('fas fa-', '');
				#>
				<a class="gmt-icon gmt-social-icon gmt-social-icon-{{ className }} gmt-animation-{{ settings.hover_animation }} gmt-repeater-item-{{item._id}}" href="{{ link }}">
					<span class="gmt-screen-only">{{{ social }}}</span>
					<#
						iconsHTML[ index ] = goomento.helpers.renderIcon( view, item.social_icon, {}, 'i', 'object' );
						if ( ( ! item.social ) && iconsHTML[ index ] && iconsHTML[ index ].rendered ) { #>
							{{{ iconsHTML[ index ].value }}}
						<# } else { #>
							<i class="{{ item.social }}"></i>
						<# }
					#>
				</a>
			<# } ); #>
		</div>
		<?php
    }
}
