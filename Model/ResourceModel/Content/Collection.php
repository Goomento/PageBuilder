<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content;

use Goomento\PageBuilder\Model\Content as ContentModel;
use Goomento\PageBuilder\Model\ResourceModel\AbstractCollection;
use Goomento\PageBuilder\Model\ResourceModel\Content;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'content_id';

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
     * @inheirtDoc
     */
    protected function _construct()
    {
        $this->_init(ContentModel::class, Content::class);
        $this->_map['fields']['content_id'] = 'main_table.content_id';
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * @inheriDoc
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        $this->addStoreData();
        $resource = $this->getResource();
        foreach ($this as $item) {
            $resource->unserializeData($item);
        }
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withGlobalStore
     * @return $this
     */
    public function addStoreFilter($store, $withGlobalStore = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withGlobalStore);
            $this->setFlag('store_filter_added', true);
        }
        return $this;
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable();
        parent::_renderFiltersBefore();
    }
}
