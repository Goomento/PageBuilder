<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Cms\Relation;

use Goomento\Core\Helper\State;
use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Block\ContentFactory;
use Goomento\PageBuilder\Helper\Data;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ReadHandler
 * @package \Goomento\PageBuilder\Model\ResourceModel\Cms\Relation
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
     * @param State $state
     * @param Data $dataHelper
     * @param ContentFactory $contentFactory
     */
    public function __construct(
        State $state,
        Data $dataHelper,
        ContentFactory $contentFactory
    )
    {
        $this->state = $state;
        $this->contentFactory = $contentFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param DataObject $entity
     * @param array $arguments
     * @return DataObject
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        if (!$this->state->isAdminhtml()) {
            if (
                $this->dataHelper->isActive() &&
                (int) $entity->getData('pagebuilder_is_active') === 1 &&
                $contentId = (int) $entity->getData('pagebuilder_content_id')
            ) {
                $block = $this->getBlockContent($contentId);
                $block->setOrigin($entity->getData('content'));
                $entity->setData(
                    'content',
                    $block
                );
            }
        }
        return $entity;
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
