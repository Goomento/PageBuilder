<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;

class SocialIcons extends AbstractWidget
{
    /**
     * @inheritDoc
     */
    const NAME = 'social_icons';

    /**
     * @inheritDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/social_icons.phtml';

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fa-twitter fab fa-twitter';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'social', 'icon', 'link' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerSocialIconIconInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'social_icon',
            [
                'label' => __('Icon'),
                'type' => Controls::ICONS,
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

        $widget->addControl(
            $prefix . 'link',
            [
                'label' => __('Link'),
                'type' => Controls::URL,
                'label_block' => true,
                'default' => [
                    'is_external' => 'true',
                ],
                'placeholder' => __('https://your-link.com'),
            ]
        );

        $widget->addControl(
            $prefix . 'item_icon_color',
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

        $widget->addControl(
            $prefix . 'item_icon_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    $prefix . 'item_icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'item_icon_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    $prefix . 'item_icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} {{CURRENT_ITEM}}.gmt-social-icon svg' => 'fill: {{VALUE}};',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerSocialIconsInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $repeater = new Repeater;

        self::registerSocialIconIconInterface($repeater, '');

        $widget->addControl(
            $prefix . 'list',
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
                'title_field' => '<# var social = ( "undefined" === typeof social ) ? false : social; #>',
            ]
        );

        $widget->addControl(
            $prefix . 'shape',
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

        $widget->addResponsiveControl(
            $prefix . 'align',
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
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @param string $cssIconTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerSocialIconsIconStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-social-icon',
        string $cssIconTarget = '.gmt-icon'
    ) {
        $widget->addControl(
            $prefix . 'icon_color',
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

        $widget->addControl(
            $prefix . 'icon_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    $prefix . 'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'icon_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'condition' => [
                    $prefix . 'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ' i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ' svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_size',
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
                    '{{WRAPPER}} ' . $cssTarget => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $widget->addResponsiveControl(
            $prefix . 'icon_padding',
            [
                'label' => __('Padding'),
                'type' => Controls::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget => 'padding: {{SIZE}}{{UNIT}};',
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

        $iconSpacing = DataHelper::isRtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};';

        $widget->addResponsiveControl(
            $prefix . 'icon_spacing',
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
                    '{{WRAPPER}} ' . $cssTarget . ':not(:last-child)' => $iconSpacing,
                ],
            ]
        );

        $widget->addGroupControl(
            BorderGroup::NAME,
            [
                'name' => $prefix . 'image_border',
                'selector' => '{{WRAPPER}} ' . $cssTarget,
                'separator' => 'before',
            ]
        );

        $widget->addControl(
            $prefix . 'border_radius',
            [
                'label' => __('Border Radius'),
                'type' => Controls::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssIconTarget => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @param string $cssTarget
     * @return void
     * @throws BuilderException
     */
    public static function registerSocialIconsIconHoverStyle(
        ControlsStack $widget,
        string $prefix = self::NAME . '_',
        string $cssTarget = '.gmt-social-icon'
    ) {
        $widget->addControl(
            $prefix . 'hover_primary_color',
            [
                'label' => __('Primary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_secondary_color',
            [
                'label' => __('Secondary Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'icon_color' => 'custom',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} ' . $cssTarget . ':hover svg' => 'fill: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_border_color',
            [
                'label' => __('Border Color'),
                'type' => Controls::COLOR,
                'default' => '',
                'condition' => [
                    $prefix . 'image_border_border!' => '',
                ],
                'selectors' => [
                    '{{WRAPPER}} ' . $cssTarget . ':hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'hover_animation',
            [
                'label' => __('Hover Animation'),
                'type' => Controls::HOVER_ANIMATION,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_social_icon',
            [
                'label' => __('Social Icons'),
            ]
        );

        self::registerSocialIconsInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_social_style',
            [
                'label' => __('Icon'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerSocialIconsIconStyle($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_social_hover',
            [
                'label' => __('Icon Hover'),
                'tab' => Controls::TAB_STYLE,
            ]
        );

        self::registerSocialIconsIconHoverStyle($this);

        $this->endControlsSection();
    }
}
