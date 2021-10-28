<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface RevisionSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get contents list.
     *
     * @return RevisionInterface[]
     */
    public function getItems();

    /**
     * Set contents list.
     *
     * @param RevisionInterface[] $items
     * @return RevisionSearchResultsInterface
     */
    public function setItems(array $items);
}
