<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

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
     * @param $contentId
     * @param null $statuses
     * @param int|null $limit
     * @return RevisionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getRevisionsByContent($contentId, $statuses = null, ?int $limit = null)
    {
        if ($contentId instanceof ContentInterface) {
            $contentId = $contentId->getId();
        }
        return $this->revisionRepository->getListByContentId(
            (int) $contentId,
            $statuses,
            $limit
        );
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
     * @param $contentId
     * @return ContentInterface|null
     */
    public function get($contentId)
    {
        return $this->contentRegistry->getById((int) $contentId);
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
     * @param ContentInterface $content
     * @param bool $createRevision
     * @return void
     * @throws LocalizedException
     */
    public function save(ContentInterface $content, bool $createRevision = true)
    {
        if (false === $createRevision) {
            $content->setRevisionFlag(false);
        }
        /** @var \Goomento\PageBuilder\Model\Content $content */
        $this->contentRepository->save($content);
        if (false === $createRevision) {
            $content->setRevisionFlag(true);
            $content->setDataChanges(false);
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
