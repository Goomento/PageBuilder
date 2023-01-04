<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class CatalogCategory implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @var int
     */
    private $storeId = 0;

    /**
     * @var []
     */
    private $categories = [];

    public function __construct(
        CollectionFactory $categoryCollectionFactory
    ) {

        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId) : CatalogCategory
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get category base on store
     *
     * @return array
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (!isset($this->categories[$this->storeId])) {
            $collection = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->setStore($this->storeId)
                ->addAttributeToFilter('is_active', '1')
                ->addAttributeToSelect('name');
            foreach ($collection->getItems() as $item) {
                $this->categories[$this->storeId][] = [
                    'value' => $item->getId(),
                    'label' => sprintf('%s (ID: %s)', $item->getName(), $item->getId())
                ];
            }
        }

        return $this->categories[$this->storeId];
    }
}
