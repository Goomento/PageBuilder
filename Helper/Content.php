<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Magento\Framework\App\Helper\Context;
use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\Data\RevisionSearchResultsInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class Content extends AbstractHelper
{
    /**
     * @var ContentRepositoryInterface
     */
    private $contentRepository;
    /**
     * @var ContentManagementInterface
     */
    private $contentManagement;
    /**
     * @var RevisionRepositoryInterface
     */
    private $revisionRepository;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param Context $context
     * @param ContentRepositoryInterface $contentRepository
     * @param ContentManagementInterface $contentManagement
     * @param ContentRegistryInterface $contentRegistry
     * @param RevisionRepositoryInterface $revisionRepository
     */
    public function __construct(
        Context $context,
        ContentRepositoryInterface $contentRepository,
        ContentManagementInterface $contentManagement,
        ContentRegistryInterface $contentRegistry,
        RevisionRepositoryInterface $revisionRepository
    )
    {
        parent::__construct($context);
        $this->contentRepository = $contentRepository;
        $this->contentManagement = $contentManagement;
        $this->revisionRepository = $revisionRepository;
        $this->contentRegistry = $contentRegistry;
    }


    /**
     * @param ContentInterface $content
     * @param array|null $statuses
     * @param int|null $limit
     * @param int|null $currentPage
     * @return RevisionInterface[]
     * @throws LocalizedException
     */
    public function getRevisionsByContent(ContentInterface $content, ?array $statuses = null, ?int $limit = 200, ?int $currentPage = 1)
    {
        return $this->revisionRepository->getListByContentId(
            (int) $content->getId(),
            $statuses,
            $limit,
            $currentPage
        )->getItems();
    }

    /**
     * @param ContentInterface $content
     * @return RevisionInterface|null
     */
    public function getLastRevisionByContent(ContentInterface $content)
    {
        return $this->revisionRepository->getLastRevisionByContentId((int) $content->getId());
    }


    /**
     * @param $revisionId
     * @return RevisionInterface
     * @throws LocalizedException
     */
    public function getRevision($revisionId)
    {
        return $this->revisionRepository->getById(
            (int) $revisionId
        );
    }

    /**
     * @param string|int $contentId
     * @return ContentInterface|null
     */
    public function get($contentId)
    {
        return $this->contentRegistry->getByIdentifier( (string) $contentId );
    }

    /**
     * Save content as revision, then It won't affect to the main version
     *
     * @param ContentInterface $content
     * @param string $status
     * @return null|RevisionInterface
     * @throws LocalizedException
     */
    public function saveAsRevision(ContentInterface $content, string $status = BuildableContentInterface::STATUS_REVISION) : ?RevisionInterface
    {
        return $this->contentManagement->createRevision($content , $status);
    }

    /**
     * Save content
     *
     * @param ContentInterface $content
     * @return null|ContentInterface
     * @throws LocalizedException
     */
    public function saveContent(ContentInterface $content) : ?ContentInterface
    {
        return $this->contentRepository->save($content);
    }

    /**
     * Save revision
     *
     * @param RevisionInterface $revision
     * @return null|RevisionInterface
     * @throws LocalizedException
     */
    public function saveRevision(RevisionInterface $revision) : ?RevisionInterface
    {
        return $this->revisionRepository->save($revision);
    }

    /**
     * @param array $data
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function create(array $data)
    {
        return $this->contentManagement->createContent($data);
    }

    /**
     * @param BuildableContentInterface $content
     * @param bool $createRevision
     * @return BuildableContentInterface|null
     * @throws LocalizedException
     */
    public function save(BuildableContentInterface $content, bool $createRevision = true)
    {
        if ($content instanceof ContentInterface) {
            return $this->saveContent($content);
        } elseif ($content instanceof RevisionInterface) {
            return $this->saveRevision($content);
        }
    }

    /**
     * @param $id
     * @throws Exception
     */
    public function delete($id)
    {
        $this->contentRepository->deleteById((int) $id);
    }
}
