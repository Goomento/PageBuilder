<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\ContentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ContentRepositoryInterface
 * For loading content from caching, use ContentRegistryInterface instead.
 * @see ContentRegistryInterface
 */
interface ContentRepositoryInterface
{
    /**
     * Save page.
     * Save content alongside with revision, please use BuildableContentManagementInterface::save
     *
     * @param ContentInterface $content
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function save(ContentInterface $content);

    /**
     * Retrieve page by Id from database
     *
     * @see ContentRegistryInterface::getById()
     * @param int $contentId
     * @return ContentInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $contentId) : ContentInterface;

    /**
     * Retrieve page by identifier from database
     *
     *
     * @see ContentRegistryInterface::getByIdentifier()
     * @param string $identifier
     * @return ContentInterface
     * @throws NoSuchEntityException
     */
    public function getByIdentifier(string $identifier) : ContentInterface;

    /**
     * Retrieve contents matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ContentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete content.
     *
     * @param ContentInterface $page
     * @return bool true on success
     * @throws LocalizedException
     * @see ContentRegistryInterface::invalidateContent()
     */
    public function delete(ContentInterface $page);

    /**
     * Delete content by ID.
     *
     * @param int $pageId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @see ContentRegistryInterface::invalidateContent()
     */
    public function deleteById($pageId);
}
