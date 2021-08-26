<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Revision;

use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Content\Relation\Revision
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var RevisionInterface[]
     */
    private $revisions = [];

    /**
     * @var ContentManagementInterface
     */
    protected $contentManagement;
    /**
     * @var RevisionRepositoryInterface
     */
    private $revisionRepository;

    /**
     * SaveHandler constructor.
     * @param ContentManagementInterface $contentManagement
     * @param RevisionRepositoryInterface $revisionRepository
     */
    public function __construct(
        ContentManagementInterface $contentManagement,
        RevisionRepositoryInterface $revisionRepository
    )
    {
        $this->revisionRepository = $revisionRepository;
        $this->contentManagement = $contentManagement;
    }

    /**
     * @param ContentInterface $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        /** @var \Goomento\PageBuilder\Model\Content $entity */
        if ($entity->getId() && $entity->hasDataChanges()) {
            /** @var \Goomento\PageBuilder\Model\Revision|null $revision */
            $revision = $this->revisions[$entity->getId()] ?? null;

            if (!$revision && $revision = $entity->getData('revision')) {
                $this->revisions[$entity->getId()] = $revision;
            }

            if (!$revision) {
                $revision = $this->contentManagement->createRevision($entity);
                $this->revisions[$entity->getId()] = $revision;
            } else {
                $revision->setSettings($entity->getSettings());
                $revision->setElements($entity->getElements());
                $this->revisionRepository->save($revision);
            }
        }
        return $entity;
    }
}
