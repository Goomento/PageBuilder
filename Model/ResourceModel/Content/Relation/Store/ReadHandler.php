<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Store;

use Goomento\PageBuilder\Model\ResourceModel\Content;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Store
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Content
     */
    protected $resourcePage;

    /**
     * @param MetadataPool $metadataPool
     * @param Content $resourcePage
     */
    public function __construct(
        MetadataPool $metadataPool,
        Content $resourcePage
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourcePage = $resourcePage;
    }

    /**
     * @param DataObject $entity
     * @param array $arguments
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $stores = $this->resourcePage->lookupStoreIds((int)$entity->getId());
            $entity->setStoreId($stores);
        }
        $entity->setDataChanges(false);
        return $entity;
    }
}
