<?php
/**
 * @package Goomento_DocBuilder
 * @link https://github.com/Goomento/DocBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Exception\BuilderException;

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
        return [ 'product', ' pricing', 'table', 'price'];
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [ 'products' ];
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerPricingTableFeature(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'item',
            [
                'label'       => __('List Item'),
                'type'        => Controls::TEXT,
                'label_block' => true,
                'default'     => __('Pricing table list item'),
            ]
        );

        $widget->addControl(
            $prefix . 'item_selected_icon',
            [
                'label'            => __('List Icon'),
                'type'             => Controls::ICONS,
                'default'          => [
                    'value'   => 'fas fa-check',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'item_active',
            [
                'label'        => __('Item Active?'),
                'type'         => Controls::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'item_icon_color',
            [
                'label'   => __('Icon Color'),
                'type'    => Controls::COLOR,
                'default' => '#23a455',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerPricingTableTitleInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'title',
            [
                'label'       => __('Title'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => __('General Package'),
            ]
        );

        $widget->addControl(
            $prefix . 'title_size',
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
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerPricingTablePriceInterface(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $widget->addControl(
            $prefix . 'price',
            [
                'label'       => __('Price'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '100',
            ]
        );
        $widget->addControl(
            $prefix . 'onsale',
            [
                'label'        => __('On Sale?'),
                'type'         => Controls::SWITCHER,
                'return_value' => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'onsale_price',
            [
                'label'       => __('Sale Price'),
                'type'        => Controls::TEXT,
                'default'     => 95,
                'condition'   => [
                    $prefix . 'onsale' => 'yes',
                ],
            ]
        );

        $widget->addControl(
            $prefix . 'price_cur',
            [
                'label'       => __('Price Currency'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '$',
            ]
        );

        $widget->addControl(
            $prefix . 'price_cur_placement',
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

        $widget->addControl(
            $prefix . 'price_period',
            [
                'label'       => __('Price Period (per)'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => __('Month'),
            ]
        );

        $widget->addControl(
            $prefix . 'period_separator',
            [
                'label'       => __('Period Separator'),
                'type'        => Controls::TEXT,
                'label_block' => false,
                'default'     => '/ ',
            ]
        );
    }

    /**
     * @param ControlsStack $widget
     * @param string $prefix
     * @return void
     * @throws BuilderException
     */
    public static function registerPricingTableFeatures(
        ControlsStack $widget,
        string $prefix = self::NAME . '_'
    ) {
        $repeater = new Repeater();

        self::registerPricingTableFeature($repeater, $prefix);

        $widget->addControl(
            $prefix . 'icon_enabled',
            [
                'label'        => __('Show Icon'),
                'type'         => Controls::SWITCHER,
                'default'      => 'yes',
            ]
        );

        $widget->addControl(
            $prefix . 'items',
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
                'title_field' => '{{ ' . $prefix . 'item }}',
            ]
        );
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

        self::registerPricingTableTitleInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_price',
            [
                'label' => __('Price'),
            ]
        );

        self::registerPricingTablePriceInterface($this);

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_feature',
            [
                'label' => __('Features'),
            ]
        );

        self::registerPricingTableFeatures($this);

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

        $prefixKey = self::buildPrefixKey(Button::NAME);

        Button::registerButtonInterface($this, $prefixKey);

        $this->removeControl($prefixKey . 'align');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_style',
            [
                'label' => __('General'),
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

        Text::registerSimpleTextStyle($this, self::NAME . '_op_', '.gmt-pricing-body .gmt-original-price');

        $this->addControl(
            'pricing_table_original_price_currency_style',
            [
                'label'     => __('Currency'),
                'type'      => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_opc_', '.gmt-pricing-body .gmt-original-price .gmt-price-currency');

        $this->endControlsSection();

        $this->startControlsSection(
            'section_pricing_table_sale_price_style',
            [
                'label' => __('Sale Price'),
                'tab'   => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_onsale' => 'yes'
                ]
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_onp_', '.gmt-pricing-body .gmt-sale-price');

        $this->addControl(
            'pricing_table_onsale_price_currency_style',
            [
                'label'     => __('Currency'),
                'type'      => Controls::HEADING,
                'separator' => 'before'
            ]
        );

        Text::registerSimpleTextStyle($this, self::NAME . '_onpc_', '.gmt-pricing-body .gmt-sale-price .gmt-price-currency');

        $this->endControlsSection();

        $this->startControlsSection(
            'pricing_table_pricing_period_style',
            [
                'label'     => __('Pricing Period'),
                'tab'       => Controls::TAB_STYLE,
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
            'section_pricing_table_footer_button_style',
            [
                'label' => __('Footer & Button'),
                'tab'   => Controls::TAB_STYLE,
                'condition' => [
                    self::NAME . '_button_show' => 'yes'
                ]
            ]
        );

        $this->addControl(
            'section_pricing_table_button_style',
            [
                'label' => __('Button'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Button::registerButtonStyle($this, self::NAME . '_button_', '.gmt-button');

        $this->addControl(
            'section_pricing_table_footer_style',
            [
                'label' => __('Footer'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_footer_wrapper_', '.gmt-pricing-footer');

        $this->endControlsSection();
    }
}
