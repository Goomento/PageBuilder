<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Widgets\Product;

use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Traits\TraitWidgetBlock;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @method setProductsPerPage(int $number)
 * @method null|array getCategoryIds()
 * @method null|array getProductSkus()
 * @method null|int getProductsPerRow()
 * @method null|int getProductsPerRowTablet()
 * @method null|int getProductsPerRowMobile()
 * @method null|string getMode()
 * @method setCategoryIds(array $ids)
 * @method setProductSkus(array $skus)
 * @method setProductsPerRow($num)
 * @method setProductsPerRowTablet($num)
 * @method setProductsPerRowMobile($num)
 * @method setMode($mode)
 */
class ProductList extends ProductsList
{
    use TraitWidgetBlock;

    /**
     * Phase product collection
     *
     * @return void
     */
    private function parseWidgetData()
    {
        $widget = $this->getWidget();

        if ($widget) {
            $name = $widget->getName();
            if ($cats = $this->getSettingsForDisplay("{$name}_category")) {
                $this->setCategoryIds($cats);
            }
            if ($skus = $this->getSettingsForDisplay("{$name}_product")) {
                $this->setProductSkus($skus);
            }
            if ($productsPerPage = $this->getSettingsForDisplay("{$name}_products_per_page")) {
                $this->setProductsPerPage((int) $productsPerPage);
            }
            if ($mode = $this->getSettingsForDisplay("{$name}_mode")) {
                $this->setMode($mode);
            }
            if ($showPager = $this->getSettingsForDisplay("{$name}_show_pager")) {
                $this->setData('show_pager', $showPager);
            }

            $this->setData('page_var_name', 'gp');
        }
    }

    /**
     * @return Collection|AbstractDb
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function createCollection()
    {
        $this->parseWidgetData();

        if (!$this->getCategoryIds() && !$this->getProductSkus()) {
            return null;
        }

        $collection = $this->productCollectionFactory->create();

        $collection->setPageSize(
            $this->getProductsPerPage()
        )->setCurPage(1);

        if ($this->getCategoryIds()) {
            $collection->addCategoriesFilter(['in' => $this->getCategoryIds()]);
            $this->setCollectionGeneralData($collection);
        }

        if ($this->getProductSkus()) {
            if ($this->getCategoryIds()) {
                $productsCollection = $this->productCollectionFactory->create();
                $productsCollection->addAttributeToFilter('sku', ['in' => $this->getProductSkus()]);
                $this->setCollectionGeneralData($productsCollection);

                if ($productsCollection->count()) {
                    $collection->load();
                    foreach ($productsCollection->getItems() as $item) {
                        if (!$collection->getItemById($item->getId())) {
                            $collection->addItem($item);
                        }
                    }
                }
            } else {
                $collection->addAttributeToFilter('sku', ['in' => $this->getProductSkus()]);
            }
        }

        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * @param Collection $collection
     * @return void
     * @throws NoSuchEntityException
     */
    private function setCollectionGeneralData(Collection $collection)
    {
        $this->_addProductAttributesAndPrices($collection);
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        if ($this->getData('store_id') !== null) {
            $currentStoreId = (int) $this->getData('store_id');
        } else {
            $currentStoreId = $this->_storeManager->getStore()->getId();
        }
        $collection
            ->setStoreId($currentStoreId)
            ->addStoreFilter($currentStoreId)
            ->addAttributeToSort('created_at', 'desc');
    }

    /**
     * @inheritDoc
     */
    public function getCacheKeyInfo()
    {
        $keys = parent::getCacheKeyInfo();
        if ($this->getWidget()) {
            $settings = $this->getWidget()->getSettings();
            // Reference the key via widget settings
            $keys['builder_cache_key'] = EncryptorHelper::uniqueId($settings);
            $keys['builder_widget_id'] = $this->getWidget()->getId();
        }

        return $keys;
    }
}
