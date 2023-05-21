<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Exception;
use Goomento\PageBuilder\Helper\DataHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Config extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pagebuilder_config_data', 'config_id');
    }

    /**
     * Save config value
     *
     * @param string $path
     * @param string|null $value
     * @param int $storeId
     * @return Config
     * @throws LocalizedException
     */
    public function saveConfig($path, $value, int $storeId = \Goomento\PageBuilder\Model\Config::DEFAULT_STORE_ID)
    {
        if (is_array($value)) {
            try {
                $value = DataHelper::encode($value);
            } catch (Exception $e) {

            }
        }
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(
                $this->getMainTable()
            )->where(
                'path = ?',
                $path
            )->where(
                'store_id = ?',
                $storeId
            );
        $row = $connection->fetchRow($select);

        $newData = ['store_id' => $storeId, 'path' => $path, 'value' => $value];

        if ($row) {
            $whereCondition = [$this->getIdFieldName() . '=?' => $row[$this->getIdFieldName()]];
            $connection->update($this->getMainTable(), $newData, $whereCondition);
        } else {
            $connection->insert($this->getMainTable(), $newData);
        }
        return $this;
    }

    /**
     * @param int|null $storeId
     * @return array
     * @throws LocalizedException
     */
    public function fetchAll(?int $storeId = null)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable()
        );

        if ($storeId) {
            $select->where(
                'store_id = ?',
                $storeId
            );
        }

        $result = $connection->fetchAll($select);
        if (!empty($result)) {
            foreach ($result as &$row) {
                if (is_string($row['value'])) {
                    try {
                        $row['value'] = DataHelper::decode($row['value']);
                    } catch (Exception $e) {

                    }
                }
            }
        }

        return $result;
    }

    /**
     * Delete config value
     *
     * @param string $path
     * @param int $storeId
     * @return $this
     * @throws LocalizedException
     */
    public function deleteConfig($path, $storeId = \Goomento\PageBuilder\Model\Config::DEFAULT_STORE_ID)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getMainTable(),
            [
                $connection->quoteInto('path = ?', $path),
                $connection->quoteInto('store_id = ?', $storeId)
            ]
        );
        return $this;
    }
}
