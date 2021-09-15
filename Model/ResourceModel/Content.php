<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Content
 * @package Goomento\PageBuilder\Model\ResourceModel
 */
class Content extends AbstractDb
{
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
        $linkField = $this->getIdFieldName();

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

        $linkField = $this->getIdFieldName();

        $select = $connection->select()
            ->from(['stores' => $this->getTable('pagebuilder_content_store')], 'store_id')
            ->join(
                ['content' => $this->getMainTable()],
                'stores.' . $linkField . ' = content.' . $linkField,
                []
            )
            ->where('content.' . $linkField . ' = :content_id');

        return $connection->fetchCol($select, ['content_id' => (int)$contentId]);
    }
}
