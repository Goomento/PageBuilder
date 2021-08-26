<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Revision;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Goomento\PageBuilder\Model\ResourceModel\Revison
 */
class Collection extends AbstractCollection
{

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'pagebuilder_content_revision_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'pagebuilder_content_revision_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Goomento\PageBuilder\Model\Revision::class,
            \Goomento\PageBuilder\Model\ResourceModel\Revision::class);
    }
}
