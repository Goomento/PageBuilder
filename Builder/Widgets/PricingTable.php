<?php
/**
 * @package Goomento_DocBuilder
 * @link https://github.com/Goomento/DocBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Controls\Groups\BorderGroup;
use Goomento\PageBuilder\Builder\Controls\Groups\BoxShadowGroup;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;

class PricingTable extends AbstractWidget
{
    /**
     * @inheriDoc
     */
    protected $template = 'Goomento_PageBuilder::widgets/pricing_table.phtml';

    /**
     * @inheriDoc
     */
    const NAME = 'pricing_table';

    /**
     * @inheritDoc
     */
    public function getStyleDepends()
    {
        return ['goomento-widgets'];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Pricing Table');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-dollar-sign';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'product', ' pricing', 'table'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'products' ];
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_pricing_table_general',
            [
                'label' => __('General'),
            ]
        );

        $this->addControl(
            'pricing_table_title',
            [
                'label'       => __('Title'),
                'type'        => Controls::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => false,
                'default'     => __('General Package'),
            ]
        );

        $this->addControl(
            'pricing_table_title_size',
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
                    'span' => 'span',
                    'p' => 'p',
                ],
                'default' => 'span',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_price',
            [
                'label' => __('Price'),
            ]
        );

        $this->addControl(
            'pricing_table_price',
            [
                'label'       => __('Price'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '100',
            ]
        );
        $this->addControl(
            'pricing_table_onsale',
            [
                'label'        => __('On Sale?'),
                'type'         => Controls::SWITCHER,
                'return_value' => 'yes',
            ]
        );

        $this->addControl(
            'pricing_table_onsale_price',
            [
                'label'       => __('Sale Price'),
                'type'        => Controls::TEXT,
                'default'     => 95,
                'condition'   => [
                    'pricing_table_onsale' => 'yes',
                ],
            ]
        );

        $this->addControl(
            'pricing_table_price_cur',
            [
                'label'       => __('Price Currency'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '$',
            ]
        );

        $this->addControl(
            'pricing_table_price_cur_placement',
            [
                'label'       => __('Currency Placement'),
                'type'        => Controls::SELECT,
                'default'     => 'left',
                'label_block' => false,
                'options'     => [
                    'left'  => __('Left'),
                    'right' => __('Right'),
                ],
            ]
        );

        $this->addControl(
            'pricing_table_price_period',
            [
                'label'       => __('Price Period (per)'),
                'type'        => Controls::TEXT,
                'dynamic' => ['active' => true],
                'label_block' => false,
                'default'     => __('Month'),
            ]
        );

        $this->addControl(
            'pricing_table_period_separator',
            [
                'label'       => __('Period Separator'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '/ ',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_feature',
            [
                'label' => __('Feature'),
            ]
        );

        $repeater = new Repeater();

        $repeater->addControl(
            'pricing_table_item',
            [
                'label'       => __( 'List Item' ),
                'type'        => Controls::TEXT,
                'label_block' => true,
                'default'     => __( 'Pricing table list item' ),
            ]
        );

        $repeater->addControl(
            'pricing_table_item_selected_icon',
            [
                'label'            => __( 'List Icon' ),
                'type'             => Controls::ICONS,
                'default'          => [
                    'value'   => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $repeater->addControl(
            'pricing_table_item_active',
            [
                'label'        => __( 'Item Active?' ),
                'type'         => Controls::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $repeater->addControl(
            'pricing_table_item_icon_color',
            [
                'label'   => __( 'Icon Color' ),
                'type'    => Controls::COLOR,
                'default' => '#23a455',
            ]
        );

        $this->addControl(
            'pricing_table_icon_enabled',
            [
                'label'        => __('Show Icon'),
                'type'         => Controls::SWITCHER,
                'default'      => 'yes',
            ]
        );

        $this->addControl(
            'pricing_table_items',
            [
                'type'        => Controls::REPEATER,
                'seperator'   => 'before',
                'default'     => [
                    ['pricing_table_item' => 'Unlimited calls'],
                    ['pricing_table_item' => 'Free hosting'],
                    ['pricing_table_item' => '500 MB of storage space'],
                    ['pricing_table_item' => '500 MB Bandwidth'],
                    ['pricing_table_item' => '24/7 support'],
                ],
                'fields'      => $repeater->getControls(),
                'title_field' => '{{pricing_table_item}}',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_footer',
            [
                'label' => __('Button'),
            ]
        );

        $this->addControl(
            'pricing_table_button_show',
            [
                'label'        => __('Display Button'),
                'type'         => Controls::SWITCHER,
                'default'      => 'yes',
            ]
        );

        Button::registerButtonInterface($this, self::NAME . '_button_', [
            'condition' => [
                'pricing_table_button_show' => 'yes'
            ]
        ]);

        $this->removeControl(self::NAME . '_button_align');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_style',
            [
                'label' => __('Pricing Table'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_wrapper_', '.gmt-pricing');

        $this->addResponsiveControl(
            'pricing_table_align',
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
                    'justify' => [
                        'title' => __('Justified'),
                        'icon' => 'fas fa-align-justify',
                    ],
                ],
                'prefix_class' => 'gmt%s-align-',
                'default' => 'center',
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_header_style',
            [
                'label' => __('Header'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_title_', '.gmt-pricing-head');
        ImageBox::registerBoxStyle($this, self::NAME . '_head_wrapper_', '.gmt-pricing-head');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_title_style',
            [
                'label' => __('Pricing'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'pricing_table_original_price_style',
            [
                'label'     => __('Original Price'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_op_', '.gmt-pricing-body .gmt-original-price');

        $this->addControl(
            'pricing_table_original_price_currency_style',
            [
                'label'     => __('Original Price Currency'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_opc_', '.gmt-pricing-body .gmt-original-price .gmt-price-currency');

        $this->addControl(
            'pricing_table_onsale_price_style',
            [
                'label'     => __('Sale Price'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
                'condition' => [
                    'pricing_table_onsale' => 'yes'
                ]
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_onp_', '.gmt-pricing-body .gmt-sale-price', [
            'condition' => [
                'pricing_table_onsale' => 'yes'
            ]
        ]);

        $this->addControl(
            'pricing_table_onsale_price_currency_style',
            [
                'label'     => __('Sale Price Currency'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
                'condition' => [
                    'pricing_table_onsale' => 'yes'
                ]
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_onpc_', '.gmt-pricing-body .gmt-sale-price .gmt-price-currency');

        $this->addControl(
            'pricing_table_pricing_period_style',
            [
                'label'     => __('Pricing Period'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_period_', '.gmt-pricing-body .gmt-price-period');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_style_body',
            [
                'label' => __('Body & Features'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        $this->addControl(
            'pricing_table_list_item_color',
            [
                'label'     => __('Color'),
                'type'      => Controls::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-pricing-body .pricing-feature' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'pricing_table_list_disable_item_color',
            [
                'label'     => __('Disable Item Color'),
                'type'      => Controls::COLOR,
                'default'   => '',
                'selectors' => [
                    '{{WRAPPER}} .gmt-pricing-body .pricing-feature-disabled.pricing-feature' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->addControl(
            'pricing_table_list_item_icon_size',
            [
                'label'     => __('Icon Size'),
                'type'      => Controls::SLIDER,
                'default'   => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'range'     => [
                    'px' => [
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .gmt-pricing-body .pricing-feature i'   => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_feature_', '.gmt-pricing-body .pricing-feature');

        ImageBox::registerBoxStyle($this, self::NAME . '_body_wrapper_', '.gmt-pricing-body');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_footer_style',
            [
                'label' => __('Footer & Button'),
                'tab'   => Controls::TAB_STYLE,
                'condition' => [
                    'pricing_table_button_show' => 'yes'
                ]
            ]
        );

        Button::registerButtonStyle($this, self::NAME . '_button_', '.gmt-button');
        ImageBox::registerBoxStyle($this, self::NAME . '_footer_wrapper_', '.gmt-pricing-footer');

        $this->endControlsSection();
    }
}
