<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Revision extends AbstractDb
{
    use TraitResourceModel;

    const MAX_REVISION_ITEMS = 500;

    /**
     * @inheriDoc
     */
    protected $_serializableFields = [
        BuildableContentInterface::SETTINGS => [null, []],
        BuildableContentInterface::ELEMENTS => [null, []],
    ];

    /**
     * @inheriDoc
     */
    protected function _construct()
    {
        $this->_init('pagebuilder_content_revision', 'revision_id');
    }

    /**
     * @inheriDoc
     */
    protected function _afterSave(AbstractModel $object)
    {
        parent::_afterSave($object);
        $this->cleanRevisions($object);
        return $this;
    }

    /**
     * Remove revisions
     */
    private function cleanRevisions(AbstractModel $object)
    {
        $contentId = (int) $object->getData(RevisionInterface::CONTENT_ID);
        if ($contentId) {
            $connection = $this->getConnection();
            $selectCount = $connection->select()->from(
                $this->getMainTable(),
                'COUNT(*)'
            )->where('content_id = ?', $contentId);
            $count = (int) $connection->fetchOne($selectCount);

            if ($count && $count > self::MAX_REVISION_ITEMS) {
                $removeNumber = $count - self::MAX_REVISION_ITEMS;
                $connection->deleteFromSelect(
                    $connection->select()->from(
                        $this->getMainTable(),
                        'revision_id'
                    )
                        ->where('content_id = ?', $contentId)
                        ->limit($removeNumber)
                        ->order('revision_id desc'),
                    $this->getMainTable()
                );
            }
        }
    }
}
