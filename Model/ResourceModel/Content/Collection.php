<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\Content as ContentModel;
use Goomento\PageBuilder\Model\ResourceModel\AbstractCollection;
use Goomento\PageBuilder\Model\ResourceModel\Content;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 * @package Goomento\PageBuilder\Model\ResourceModel\Content
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'content_id';

    /**
     * Load data for preview flag
     *
     * @var bool
     */
    protected $_previewFlag;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'pagebuilder_content_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'pagebuilder_content_collection';

    /**
     * @var string
     */
    protected $type;
    /**
     * @var Json|mixed
     */
    private $serializer;

    /**
     * Collection constructor.
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param MetadataPool $metadataPool
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     * @param string $type
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null,
        string $type = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $metadataPool, $connection, $resource);
        $this->type = $type;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ContentModel::class, Content::class);
        $this->_map['fields']['content_id'] = 'main_table.content_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Perform operations after collection load
     *
     */
    protected function _afterLoad()
    {
        $entityMetadata = $this->metadataPool->getMetadata(ContentInterface::class);
        $this->performAfterLoad('pagebuilder_content_store', $entityMetadata->getLinkField());
        $this->unserializeFields();
        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Get serializer
     */
    protected function getSerializer()
    {
        if (null === $this->serializer) {
            $this->serializer = ObjectManager::getInstance()->get(Json::class);
        }
        return $this->serializer;
    }

    /**
     * Unserialize Fields
     */
    protected function unserializeFields()
    {
        /** @var \Goomento\PageBuilder\Model\Content $item */
        foreach ($this->_items as $item) {
            foreach (ContentInterface::SERIALIZABLE_FIELDS as $field => $parameters) {
                list($serializeDefault, $unserializeDefault) = $parameters;
                $value = $item->getData($field);
                if ($value) {
                    $data = $this->getSerializer()->unserialize($value);
                    if (empty($data)) {
                        $item->setData($field, $unserializeDefault);
                    } else {
                        $item->setData($field, $data);
                    }
                } else {
                    $item->setData($field, $unserializeDefault);
                }
            }
        }
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
            $this->setFlag('store_filter_added', true);
        }
        return $this;
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     * @throws Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(ContentInterface::class);
        $this->joinStoreRelationTable('pagebuilder_content_store', $entityMetadata->getLinkField());
        if (!empty($this->type)) {
            $this->addFilter('type', $this->type , 'public');
        }
    }
}
