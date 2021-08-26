<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Store;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Store
 */
class SaveHandler implements ExtensionInterface
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
     * @param ContentInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        $this->saveStoreId($entity, $arguments);
        return $entity;
    }

    /**
     * @param $entity
     * @param array $arguments
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function saveStoreId($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(ContentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldStores = $this->resourcePage->lookupStoreIds((int)$entity->getId());
        $newStores = (array)$entity->getStoreId();

        $table = $this->resourcePage->getTable('pagebuilder_content_store');

        $delete = array_diff($oldStores, $newStores);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'store_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newStores, $oldStores);
        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'store_id' => (int)$storeId
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }
}
