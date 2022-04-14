<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Store\Model\Store;

abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by store
     *
     * @param int|array|Store $store
     * @param bool $withGlobalStore
     * @return $this
     */
    abstract public function addStoreFilter($store, $withGlobalStore = true);

    /**
     * Perform adding filter by store
     *
     * @param int|array|Store $store
     * @param bool $withGlobalStore
     * @return void
     */
    protected function performAddStoreFilter($store, $withGlobalStore = true)
    {
        if ($store instanceof Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withGlobalStore) {
            $store[] = Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function joinStoreRelationTable()
    {
        if ($this->getFilter('store_id')) {
            $this->getSelect()->join(
                ['store_table' => $this->getTable('pagebuilder_content_store')],
                'main_table.' . ContentInterface::CONTENT_ID . ' = store_table.' . ContentInterface::CONTENT_ID,
                []
            )->group(
                'main_table.' . ContentInterface::CONTENT_ID
            );
        }
    }

    /**
     * Add store data to collection
     *
     * @return void
     */
    protected function addStoreData()
    {
        $linkField = ContentInterface::CONTENT_ID;
        $linkedIds = $this->getColumnValues($linkField);
        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['store_table' => $this->getTable('pagebuilder_content_store')])
                ->where('store_table.' . $linkField . ' IN (?)', $linkedIds);

            $result = $connection->fetchAll($select);

            if ($result) {
                $storesData = [];
                foreach ($result as $storeData) {
                    $storesData[$storeData[$linkField]][] = $storeData['store_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($storesData[$linkedId])) {
                        continue;
                    }
                    $data = $storesData[$linkedId] ?? [];
                    $item->setData('store_ids', $storesData[$linkedId]);
                    // Add store for UI component
                    $item->setData('store_id', $data);
                }
            }
        }
    }
}
