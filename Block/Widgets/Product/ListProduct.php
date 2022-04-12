<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Widgets\Product;

use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Render;
use Magento\Framework\Url\Helper\Data;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * Number of products
     */
    const PRODUCTS_PER_PAGE = 'products_per_page';

    /**
     * Single SKU or SKUs
     */
    const PRODUCT_SKU = 'sku';

    /**
     * Category IDs
     */
    const CATEGORY_IDS = 'category_ids';

    /**
     * Name of key - which is stored the Widget instance class
     */
    const PRODUCT_LIST_WIDGET = 'product_list_widget';

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Collection
     */
    protected $loadedCollection;

    /**
     * ListProduct constructor.
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $urlHelper
     * @param CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        PostHelper $postDataHelper,
        Resolver $layerResolver,
        CategoryRepositoryInterface
        $categoryRepository,
        Data $urlHelper,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->productCollectionFactory = $collectionFactory;
        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * @return Collection|AbstractCollection|AbstractDb
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getLoadedProductCollection()
    {
        if (is_null($this->loadedCollection)) {
            $this->loadedCollection = $this->getCollection();
        }

        return $this->loadedCollection;
    }

    /**
     * @return Collection|AbstractDb
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getCollection()
    {
        $collection = $this->productCollectionFactory->create()->addFieldToSelect('*');

        $collection->setPageSize(
            $this->getProductsPerPage()
        )->setCurPage(1);
        $currentStoreId = $this->_storeManager->getStore()->getId();

        if ($this->getCategoryIds()) {
            $collection->addCategoriesFilter(['in' => $this->getCategoryIds()]);
            $collection->setStoreId($currentStoreId)->addStoreFilter($currentStoreId);
            $collection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
            $collection->addAttributeToFilter('status', Status::STATUS_ENABLED);
        }

        if ($this->getProductSkus()) {
            if ($this->getCategoryIds()) {
                $productsCollection = $this->productCollectionFactory->create()->addFieldToSelect('*');
                $productsCollection->addAttributeToFilter('sku', ['in' => $this->getProductSkus()]);

                $productsCollection->setStoreId($currentStoreId)->addStoreFilter($currentStoreId);
                $productsCollection->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH);
                $productsCollection->addAttributeToFilter('status', Status::STATUS_ENABLED);

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
     * @param $num
     * @return ListProduct
     */
    public function setProductsPerPage($num)
    {
        return $this->setData(self::PRODUCTS_PER_PAGE, $num);
    }

    /**
     * @return array|int|mixed
     */
    public function getProductsPerPage()
    {
        return $this->getData(self::PRODUCTS_PER_PAGE) ?: 12;
    }

    /**
     * @return array|mixed
     */
    public function getCategoryIds()
    {
        return $this->getData(self::CATEGORY_IDS) ?: [];
    }

    /**
     * @param $ids
     * @return ListProduct
     */
    public function setCategoryIds($ids)
    {
        return $this->setData(self::CATEGORY_IDS, $ids);
    }

    /**
     * @return array
     */
    public function getProductSkus()
    {
        return $this->getData(self::PRODUCT_SKU) ?: [];
    }

    /**
     * @param $sku
     * @return ListProduct
     */
    public function setProductSkus($sku)
    {
        return $this->setData(self::PRODUCT_SKU, $sku);
    }

    /**
     * @return AbstractWidget
     */
    public function getWidget()
    {
        return $this->getData(self::PRODUCT_LIST_WIDGET);
    }

    /**
     * @param AbstractWidget $widget
     * @return ListProduct
     */
    public function setWidget(AbstractWidget $widget)
    {
        return $this->setData(self::PRODUCT_LIST_WIDGET, $widget);
    }

    /**
     * @inheirtDoc
     */
    protected function getPriceRender()
    {
        if (!$this->getLayout()->getBlock('product.price.render.default')) {
            return $this->getLayout()->createBlock(
                Render::class,
                'product.price.render.default',
                [
                    'data' => [
                        'price_render_handle' => 'catalog_product_prices',
                        'is_product_list' => true,
                    ]
                ]
            );
        } else {
            return parent::getPriceRender();
        }
    }

    /**
     * @return array|mixed|string|null
     */
    public function getPositioned()
    {
        $positioned = $this->getData('positioned');
        if (!$positioned) {
            $positioned = 'positions:list-secondary';
        }

        return $positioned;
    }
}
