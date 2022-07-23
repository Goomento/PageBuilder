<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

trait TraitBuildableRepository
{
    /**
     * @param int|BuildableContentInterface|BuildableContentInterface[] $object
     * @return BuildableContentInterface|BuildableContentInterface[]|null
     */
    private function checkObjectInstance($object, bool $force = false)
    {
        if ($object instanceof BuildableContentInterface) {
            if (!isset($this->objectInstances[$object->getId()]) || $force === true) {
                $this->objectInstances[$object->getId()] = $object;
            } else {
                $object = $this->objectInstances[$object->getId()];
            }
            return $object;
        } elseif (is_array($object)) {
            foreach ($object as $key => $item) {
                $newItem = $this->checkObjectInstance($item);
                if ($newItem !== $item) {
                    $object[$key] = $newItem;
                }
            }
            return $object;
        } elseif (is_scalar($object)) {
            return $this->objectInstances[$object] ?? null;
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $items = $collection->getItems() ?: [];
        if (!empty($items) && $instances = $this->checkObjectInstance($items)) {
            $items = $instances;
        }
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
