<?php
declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\Data\RevisionSearchResultsInterfaceFactory;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Model\ResourceModel\Revision as ResourceRevision;
use Goomento\PageBuilder\Model\ResourceModel\Revision\CollectionFactory as RevisionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class RevisionRepository
 * @package Goomento\PageBuilder\Model
 */
class RevisionRepository implements RevisionRepositoryInterface
{
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
    private $revisionCollectionFactory;
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
     * RevisionRepository constructor.
     * @param ResourceRevision $resource
     * @param RevisionFactory $revisionFactory
     * @param RevisionCollectionFactory $revisionCollectionFactory
     * @param RevisionSearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceRevision $resource,
        RevisionFactory $revisionFactory,
        RevisionCollectionFactory $revisionCollectionFactory,
        RevisionSearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->revisionFactory = $revisionFactory;
        $this->revisionCollectionFactory = $revisionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RevisionInterface $revision)
    {
        try {
            $this->resource->save($revision);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the revision: %1', $exception->getMessage()),
                $exception
            );
        }
        return $revision;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $revisionId)
    {
        $revision = $this->revisionFactory->create();
        $this->resource->load($revision, $revisionId);
        if (!$revision->getId()) {
            throw new NoSuchEntityException(
                __('The revision with the "%1" ID doesn\'t exist.', $revisionId)
            );
        }
        return $revision;
    }

    /**
     * @inheritDoc
     */
    public function getListByContentId(int $contentId)
    {
        $this->searchCriteriaBuilder->addFilter(RevisionInterface::CONTENT_ID, $contentId);
        $sortOrder = $this->sortOrderBuilder->setField(RevisionInterface::REVISION_ID)->setDirection(
            SortOrder::SORT_DESC
        )->create();
        $this->searchCriteriaBuilder->setSortOrders([$sortOrder]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        return $this->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->revisionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems() ?: []);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
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
        foreach ($this->getListByContentId($contentId)->getItems() as $revision) {
            $this->delete($revision);
        }
    }
}
