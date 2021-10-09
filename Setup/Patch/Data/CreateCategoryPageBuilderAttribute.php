<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Setup\Patch\Data;

use Goomento\PageBuilder\Model\Config\Source\BuildableContent;
use Magento\Catalog\Model\Category;
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
 * Class CreateCategoryPageBuilderAttribute
 * @package Goomento\PageBuilder\Setup\Patch\Data
 */
class CreateCategoryPageBuilderAttribute implements DataPatchInterface
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
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     */
    public function createPageBuilderAttribute()
    {
        $this->categorySetup->addAttribute(
            Category::ENTITY,
            ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID,
            [
                'type' => 'int',
                'label' => 'Content',
                'input' => 'select',
                'source' => BuildableContent::class,
                'required' => false,
                'sort_order' => 10,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => self::PAGEBUILDER_GROUP,
            ]
        );

        $this->categorySetup->addAttribute(
            Category::ENTITY,
            ContentRelation::FIELD_PAGEBUILDER_IS_ACTIVE,
            [
                'type' => 'int',
                'label' => 'Is Active',
                'input' => 'select',
                'source' => Boolean::class,
                'sort_order' => 2,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'group' => self::PAGEBUILDER_GROUP,
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);
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
