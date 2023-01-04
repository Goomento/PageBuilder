<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Catalog;

use Goomento\PageBuilder\Controller\Adminhtml\Ajax\AbstractAjax;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Model\BetterCaching;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Search extends AbstractAjax implements HttpGetActionInterface
{
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var BetterCaching
     */
    private $betterCaching;

    /**
     * @param Context $context
     * @param BetterCaching $betterCaching
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        BetterCaching $betterCaching,
        CollectionFactory $productCollectionFactory
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->betterCaching = $betterCaching;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = [];

        $params = $this->getRequest()->getParams();
        $params = EscaperHelper::filter($params, true);
        $term = (string) ($params['term'] ?? '');
        if (strlen($term) >= 2) {
            $key = 'sku_search_terms_' . $term . time();
            $collect = function () use ($term) {
                $result = [];
                $collection = $this->productCollectionFactory->create();
                $collection
                    ->addAttributeToSelect('name')
                    ->addAttributeToFilter('status', ['eq' => Status::STATUS_ENABLED])
                    ->addFieldToFilter('sku', ['like' => sprintf('%%%s%%', $term)]);

                if (isset($params['store'])) {
                    $collection->addStoreFilter((int) $params['store']);
                }

                $collection->setFlag('has_stock_status_filter', true);

                if ($collection->count()) {
                    $data = $collection->toArray(['sku', 'name', 'entity_id']);
                    foreach ($data as $row) {
                        $result[$row['sku']] = [
                            'id' => $row['sku'],
                            'text' => $row['sku']
                        ];
                    }
                }

                return array_values($result);
            };

            $result = $this->betterCaching->resolve($key, $collect, BetterCaching::BACKEND_CACHE_TAG, BetterCaching::FIFTEEN_MIN_TIME);
        }

        return $this->setResponseData([
            'results' => $result
        ])->sendResponse();
    }
}
