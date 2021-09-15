<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Cache;

use Goomento\PageBuilder\Model\Content;
use Goomento\PageBuilder\Model\ContentRegistry;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Cache
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ContentRegistry
     */
    private $contentRegistry;

    /**
     * @param ContentRegistry $contentRegistry
     */
    public function __construct(
        ContentRegistry $contentRegistry
    )
    {
        $this->contentRegistry = $contentRegistry;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        /** @var $entity Content */
        if ($entity->getId() && ($entity->hasDataChanges() || $entity->isDeleted())) {
            $this->contentRegistry->cleanContentCache($entity->getId());
        }
        return $entity;
    }
}
