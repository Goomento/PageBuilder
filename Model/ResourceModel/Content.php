<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
/**
 * Class Content
 * @package Goomento\PageBuilder\Model\ResourceModel
 */
class Content extends AbstractDb
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pagebuilder_content', 'content_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(ContentInterface::class)->getEntityConnection();
    }


    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return $this|Content
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $this->entityManager->load($object, $value);
        return $this;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param ContentInterface $object
     * @return Select
     * @throws LocalizedException
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(ContentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [
                Store::DEFAULT_STORE_ID,
                (int)$object->getStoreId(),
            ];
            $select->join(
                ['pagebuilder_content_store' => $this->getTable('pagebuilder_content_store')],
                $this->getMainTable() . '.' . $linkField . ' = pagebuilder_content_store.' . $linkField,
                []
            )
                ->where('is_active = ?', 1)
                ->where('pagebuilder_content_store.store_id IN (?)', $storeIds)
                ->order('pagebuilder_content_store.store_id DESC')
                ->limit(1);
        }

        return $select;
    }


    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $contentId
     * @return array
     * @throws LocalizedException
     */
    public function lookupStoreIds($contentId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(ContentInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['goomento_page_store' => $this->getTable('pagebuilder_content_store')], 'store_id')
            ->join(
                ['goomento_page' => $this->getMainTable()],
                'goomento_page_store.' . $linkField . ' = goomento_page.' . $linkField,
                []
            )
            ->where('goomento_page.' . $entityMetadata->getIdentifierField() . ' = :content_id');

        return $connection->fetchCol($select, ['content_id' => (int)$contentId]);
    }


    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }
}
