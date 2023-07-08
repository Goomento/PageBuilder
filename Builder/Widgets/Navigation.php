<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Widgets;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

// phpcs:disable Magento2.PHP.LiteralNamespaces.LiteralClassUsage
class Navigation extends AbstractWidget
{
    /**
     * @var array
     */
    private $menuOptions = [];

    /**
     * @inheirtDoc
     */
    const NAME = 'navigation';

    /**
     * @inheirtDoc
     */
    protected $renderer = \Goomento\PageBuilder\Block\Widgets\Navigation::class;

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return __('Navigation');
    }

    /**
     * @inheritDoc
     */
    public function getIcon()
    {
        return 'fas fa-bars';
    }

    /**
     * @inheritDoc
     */
    public function getKeywords()
    {
        return [ 'menu', 'navigation', 'SnowDog Menu'];
    }

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
    protected function registerControls()
    {
        $this->startControlsSection(
            'section_snowdog_menu',
            [
                'label' => __('SnowDog Menu'),
            ]
        );

        $this->addControl(
            'navigation_menu_warning',
            [
                'raw' => __('Create new menu at Admin Panel > Content > Elements > Menus'),
                'type' => Controls::RAW_HTML,
                'content_classes' => 'gmt-descriptor',
            ]
        );

        $this->addControl(
            'navigation_menu_id',
            [
                'label' => __('Menu Id'),
                'type' => Controls::SELECT,
                'default' => '',
                'options' => $this->getSnowDogMenuList()
            ]
        );

        $this->addControl(
            'navigation_menu_type',
            [
                'label' => __('Type'),
                'type' => Controls::SELECT,
                'default' => 'horizontal',
                'options' => [
                    'horizontal' => __('Horizontal'),
                    'vertical' => __('Vertical'),
                ],
                'condition' => [
                    'navigation_menu_id!' => ''
                ]
            ]
        );

        $this->endControlsSection();

        $this->startControlsSection(
            'navigation_menu_style',
            [
                'label' => __('Menu'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_menu_', '[data-action="navigation"] > ul');

        $this->endControlsSection();

        $this->startControlsSection(
            'navigation_submenu_style',
            [
                'label' => __('Submenu'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_submenu_', '[data-action="navigation"] > ul ul');

        $this->endControlsSection();

        $this->startControlsSection(
            'navigation_menu_item_lv0_style',
            [
                'label' => __('Menu Item Level Top'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_menuitemlv0_', '[data-action="navigation"] > ul > li');

        $this->addControl(
            'navigation_menuitemlv0_typography',
            [
                'label' => __('Typography'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_menuitemlv0_text_', '[data-action="navigation"] > ul > li > a');

        $this->endControlsSection();

        $this->startControlsSection(
            'navigation_menuitem_lv_style',
            [
                'label' => __('Menu Item'),
                'tab'   => Controls::TAB_STYLE,
            ]
        );

        ImageBox::registerBoxStyle($this, self::NAME . '_menuitem_', '[data-action="navigation"] > ul > li  li');

        $this->addControl(
            'navigation_menuitem_lv_typography',
            [
                'label' => __('Typography'),
                'type' => Controls::HEADING,
                'separator' => 'before',
            ]
        );

        Text::registerTextStyle($this, self::NAME . '_menuitem_text_', '[data-action="navigation"] > ul > li  li a');

        $this->endControlsSection();
    }

    /**
     * @return array
     */
    private function getSnowDogMenuList() : array
    {
        if (DataHelper::isModuleOutputEnabled('Snowdog_Menu') && empty($this->menuOptions)) {
            /** @var \Snowdog\Menu\Model\MenuRepository $menuRepo */
            $menuRepo = ObjectManagerHelper::get('Snowdog\Menu\Model\MenuRepository');
            /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = ObjectManagerHelper::get(\Magento\Framework\Api\SearchCriteriaBuilder::class);

            $menus = $menuRepo->getList(
                $searchCriteriaBuilder
                    ->addFilter('is_active', 1)
                    ->create()
            );

            $this->menuOptions = [
                '' => __('Empty')
            ];

            foreach ($menus->getItems() as $menu) {
                $this->menuOptions[$menu->getIdentifier()] = $menu->getTitle();
            }
        }

        return (array) $this->menuOptions;
    }
}
