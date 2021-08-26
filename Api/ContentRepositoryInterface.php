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
 * For loading content from cached, use ContentRegistryInterface instead.
 *
 * @package Goomento\PageBuilder\Api
 */
interface ContentRepositoryInterface
{
    /**
     * Save page.
     *
     * @param ContentInterface $content
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function save(ContentInterface $content);

    /**
     * Retrieve page.
     *
     * @param int $contentId
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function getById($contentId);

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
     */
    public function delete(ContentInterface $page);

    /**
     * Delete content by ID.
     *
     * @param int $pageId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($pageId);
}
