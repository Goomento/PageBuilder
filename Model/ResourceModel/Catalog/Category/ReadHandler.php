<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Catalog\Category;

use Goomento\Core\Helper\State;
use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Block\ContentFactory;
use Goomento\PageBuilder\Model\ContentRelation;
use Goomento\PageBuilder\Helper\Data;
use Magento\Catalog\Model\Category;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Catalog\Category
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var State
     */
    private $state;
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var ContentFactory
     */
    private $contentFactory;

    /**
     * @var Content[]
     */
    private $blockContent = [];
    /**
     * @var ContentRelation
     */
    private $contentRelation;

    /**
     * @param State $state
     * @param Data $dataHelper
     * @param ContentFactory $contentFactory
     * @param ContentRelation $contentRelation
     */
    public function __construct(
        State $state,
        Data $dataHelper,
        ContentFactory $contentFactory,
        ContentRelation $contentRelation
    )
    {
        $this->state = $state;
        $this->contentFactory = $contentFactory;
        $this->dataHelper = $dataHelper;
        $this->contentRelation = $contentRelation;
    }

    /**
     * @inheirtDoc
     */
    public function execute($entity, $arguments = [])
    {
        if (!$this->state->isAdminhtml()) {
            /** @var Category $entity */
            $relation = $this->contentRelation->getRelationByEntity(ContentRelation::TYPE_CATALOG_CATEGORY, $entity);
            if (
                $this->dataHelper->isActive() &&
                self::isValidRelation($relation)
            ) {
                $block = $this->getBlockContent($relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID]);
                $block->setOrigin($entity->getData('description'));
                $entity->setData(
                    'description',
                    $block
                );
            }
        }
        return $entity;
    }

    /**
     * @param array $relation
     * @return bool
     */
    private function isValidRelation(array $relation)
    {
        return !empty($relation) &&
            isset($relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID]) &&
            $relation[ContentRelation::FIELD_PAGEBUILDER_CONTENT_ID] &&
            isset($relation[ContentRelation::FIELD_PAGEBUILDER_IS_ACTIVE]) &&
            true === $relation[ContentRelation::FIELD_PAGEBUILDER_IS_ACTIVE];
    }

    /**
     * @param int $contentId
     * @return Content
     */
    private function getBlockContent(int $contentId)
    {
        if (!isset($this->blockContent[$contentId])) {
            $this->blockContent[$contentId] = $this->contentFactory->create()
                ->setContentId($contentId);
        }

        return $this->blockContent[$contentId];
    }
}
