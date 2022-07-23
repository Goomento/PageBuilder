<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\Data\RevisionSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RevisionRepositoryInterface
{
    /**
     * Save revision.
     *
     * @param RevisionInterface $revision
     * @return RevisionInterface
     * @throws LocalizedException
     */
    public function save(RevisionInterface $revision);

    /**
     * Retrieve revision.
     *
     * @param int $revisionId
     * @return RevisionInterface
     * @throws LocalizedException
     */
    public function getById(int $revisionId) : RevisionInterface;

    /**
     * Retrieve revision.
     *
     * @param string $revisionHash
     * @return RevisionInterface
     * @throws LocalizedException
     */
    public function getByRevisionHash(string $revisionHash);

    /**
     * Retrieve revisions of content.
     *
     * @param int $contentId
     * @param array|null $statuses
     * @param int|null $limit
     * @param int|null $currentPage
     * @return RevisionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getListByContentId(int $contentId, ?array $statuses, ?int $limit, ?int $currentPage);

    /**
     * @param int $contentId
     * @return mixed
     */
    public function getLastRevisionByContentId(int $contentId) : ?RevisionInterface;

    /**
     * Retrieve contents matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return RevisionSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete revision.
     *
     * @param RevisionInterface $revision
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(RevisionInterface $revision);

    /**
     * Delete by revision id.
     *
     * @param int $revisionId
     * @return bool true on success
     * @throws LocalizedException
     */
    public function deleteById(int $revisionId);

    /**
     * Delete revision by content Id.
     *
     * @param int $contentId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteByContentId($contentId);
}
