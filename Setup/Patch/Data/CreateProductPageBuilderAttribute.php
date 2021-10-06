<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Setup\Patch\Data;

use Goomento\PageBuilder\Model\Config\Source\PageIdList;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Goomento\PageBuilder\Model\Config\Source\Boolean;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Goomento\PageBuilder\Model\ContentRelation;
use Zend_Validate_Exception;

/**
 * Class CreateProductPageBuilderAttribute
 * @package Goomento\PageBuilder\Setup\Patch\Data
 */
class CreateProductPageBuilderAttribute implements DataPatchInterface
{
    const PAGEBUILDER_GROUP = 'Page Builder';

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var CategorySetup
     */
    private $categorySetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * Create Page Builder Group
     */
    public function createPageBuilderGroup()
    {
        $attributeSetIds = $this->categorySetup->getAllAttributeSetIds(Product::ENTITY);
        foreach ($attributeSetIds as $attributeSetId) {
            $this->categorySetup->addAttributeGroup(
                Product::ENTITY,
                $attributeSetId,
                self::PAGEBUILDER_GROUP,
                10
            );
        }
    }

    /**
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function createPageBuilderAttribute()
    {
        $this->categorySetup->addAttribute(
            Product::ENTITY,
            ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID,
            [
                'type' => 'int',
                'label' => 'Content',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'source' => PageIdList::class,
                'visible_in_advanced_search' => false,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'used_for_sort_by' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
            ]
        );

        $this->categorySetup->addAttribute(
            Product::ENTITY,
            ContentRelation::FIELD_PAGEBUILDER_IS_ACTIVE,
            [
                'type' => 'int',
                'label' => 'Active',
                'input' => 'select',
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'source' => Boolean::class,
                'visible_in_advanced_search' => false,
                'default' => '0',
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'used_in_product_listing' => true,
                'visible_on_front' => false,
                'used_for_sort_by' => false,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
            ]
        );
        $attributeSetIds = $this->categorySetup->getAllAttributeSetIds(Product::ENTITY);
        foreach ($attributeSetIds as $attributeSetId) {
            $groupId = $this->categorySetup->getAttributeGroupId(Product::ENTITY, $attributeSetId, self::PAGEBUILDER_GROUP);
            $this->categorySetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                $groupId,
                ContentRelation::FIELD_PAGEBUILDER_IS_ACTIVE,
                10
            );
            $this->categorySetup->addAttributeToGroup(
                Product::ENTITY,
                $attributeSetId,
                $groupId,
                ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID,
                20
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
        $this->createPageBuilderGroup();
        $this->createPageBuilderAttribute();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
