<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Revision extends AbstractDb
{
    use TraitResourceModel;

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
}
