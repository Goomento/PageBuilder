<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Cache;

use Goomento\PageBuilder\Model\ContentRegistry;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class DeleteHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Cache
 */
class DeleteHandler implements ExtensionInterface
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
     */
    public function execute($entity, $arguments = [])
    {
        $this->contentRegistry->cleanContentCache($entity->getId());
        return $entity;
    }
}
