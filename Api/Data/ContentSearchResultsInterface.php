<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface ContentSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get contents list.
     *
     * @return ContentInterface[]|RevisionInterface[]
     */
    public function getItems();

    /**
     * Set contents list.
     *
     * @param ContentInterface[]||RevisionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
