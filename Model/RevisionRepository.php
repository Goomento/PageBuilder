<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\Data\RevisionSearchResultsInterfaceFactory;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Helper\AdminUser;
use Goomento\PageBuilder\Model\ResourceModel\Revision as ResourceRevision;
use Goomento\PageBuilder\Model\ResourceModel\Revision\CollectionFactory as RevisionCollectionFactory;
use Goomento\PageBuilder\Traits\TraitBuildableRepository;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

class RevisionRepository implements RevisionRepositoryInterface
{
    use TraitBuildableRepository;
    /**
     * @var CollectionProcessorInterface|mixed
     */
    private $collectionProcessor;
    /**
     * @var RevisionSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var RevisionCollectionFactory
     */
    private $collectionFactory;
    /**
     * @var RevisionFactory
     */
    private $revisionFactory;
    /**
     * @var ResourceRevision
     */
    private $resource;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var AdminUser
     */
    private $adminUser;

    /**
     * @var RevisionInterface[]|[]
     */
    private $objectInstances = [];

    /**
     * RevisionRepository constructor.
     * @param ResourceRevision $resource
     * @param RevisionFactory $revisionFactory
     * @param RevisionCollectionFactory $revisionCollectionFactory
     * @param RevisionSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionProcessorInterface|null $collectionProcessor
     * @param AdminUser $adminUser
     */
    public function __construct(
        ResourceRevision $resource,
        RevisionFactory $revisionFactory,
        RevisionCollectionFactory $revisionCollectionFactory,
        RevisionSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionProcessorInterface $collectionProcessor,
        AdminUser $adminUser
    ) {
        $this->resource = $resource;
        $this->revisionFactory = $revisionFactory;
        $this->collectionFactory = $revisionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->adminUser = $adminUser;
    }

    /**
     * @inheritDoc
     */
    public function save(RevisionInterface $revision) : RevisionInterface
    {
        try {
            $this->validateStatus($revision);
            $this->validateLabel($revision);
            $currentAdminUser = $this->adminUser->getCurrentAdminUser();
            $revision->setAuthorId($currentAdminUser ? $currentAdminUser->getId() : 0);
            $this->resource->save($revision);
            $this->checkObjectInstance($revision, true);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the revision: %1', $exception->getMessage()),
                $exception
            );
        }
        return $revision;
    }

    /**
     * @param RevisionInterface $revision
     * @throws LocalizedException
     */
    private function validateStatus(RevisionInterface $revision)
    {
        if (!isset(Revision::getAvailableStatuses()[$revision->getStatus()])) {
            throw new LocalizedException(
                __('Invalid revision status: %1', $revision->getStatus())
            );
        }
    }

    /**
     * @param RevisionInterface $revision
     * @throws LocalizedException
     */
    private function validateLabel(RevisionInterface $revision)
    {
        if (!$revision->getLabel()) {
            throw new LocalizedException(
                __('Save message must be specified.')
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getById(int $revisionId) : RevisionInterface
    {
        if ($instance = $this->checkObjectInstance($revisionId)) {
            return $instance;
        }
        $revision = $this->revisionFactory->create();
        $this->resource->load($revision, $revisionId);
        if (!$revision->getId()) {
            throw new NoSuchEntityException(
                __('The revision with the "%1" ID doesn\'t exist.', $revisionId)
            );
        }
        if ($instance = $this->checkObjectInstance($revision)) {
            return $instance;
        }
        return $revision;
    }


    /**
     * @inheritDoc
     */
    public function getByRevisionHash(string $revisionHash)
    {
        $revision = $this->revisionFactory->create();
        $this->resource->load($revision, $revisionHash, BuildableContentInterface::REVISION_HASH);
        if (!$revision->getId()) {
            throw new NoSuchEntityException(
                __('The revision with the "%1" hash doesn\'t exist.', $revisionHash)
            );
        }
        if ($instance = $this->checkObjectInstance($revision)) {
            return $instance;
        }
        return $revision;
    }

    /**
     * @inheritDoc
     */
    public function getListByContentId(int $contentId, ?array $statuses, ?int $limit, ?int $currentPage)
    {
        $this->searchCriteriaBuilder->addFilter(RevisionInterface::CONTENT_ID, $contentId);
        $statuses = (array) $statuses;
        if (!empty($statuses)) {
            $this->searchCriteriaBuilder->addFilter(BuildableContentInterface::STATUS, $statuses, 'in');
        }
        $sortOrder = $this->sortOrderBuilder->setField(RevisionInterface::REVISION_ID)->setDirection(
            SortOrder::SORT_DESC
        )->create();
        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        if ($limit !== null) {
            $searchCriteria->setPageSize($limit);
        }
        if ($currentPage !== null) {
            $searchCriteria->setCurrentPage($currentPage);
        }
        return $this->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function delete(RevisionInterface $revision)
    {
        try {
            $this->resource->delete($revision);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the revision: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $revisionId)
    {
        return $this->delete(
            $this->getById($revisionId)
        );
    }

    /**
     * @inheritDoc
     */
    public function deleteByContentId($contentId)
    {
        foreach ($this->getListByContentId((int) $contentId, null, null, null)->getItems() as $revision) {
            $this->delete($revision);
        }
    }

    /**
     * @inheritDoc
     */
    public function getLastRevisionByContentId(int $contentId) : ?RevisionInterface
    {
        $statuses = Revision::getAvailableStatuses();
        unset($statuses[BuildableContentInterface::STATUS_AUTOSAVE]);
        $items = $this->getListByContentId($contentId, array_keys($statuses), 1, 1)->getItems();
        if (!empty($items)) {
            return array_values($items)[0];
        }

        return null;
    }
}
