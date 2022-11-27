<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */
declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\Store\Model\ResourceModel;

use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Model\PageBuilderUrlRewriteGenerator;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\UrlRewrite\Model\UrlPersistInterface;

class Store
{
    /**
     * @var UrlPersistInterface
     */
    private $urlPersist;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ContentRepositoryInterface
     */
    private $contentRepository;
    /**
     * @var PageBuilderUrlRewriteGenerator
     */
    private $pageBuilderUrlRewriteGenerator;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ContentRepositoryInterface $contentRepository
     * @param PageBuilderUrlRewriteGenerator $pageBuilderUrlRewriteGenerator
     * @param Logger $logger
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ContentRepositoryInterface $contentRepository,
        PageBuilderUrlRewriteGenerator $pageBuilderUrlRewriteGenerator,
        Logger $logger
    ) {
        $this->urlPersist = $urlPersist;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->contentRepository = $contentRepository;
        $this->pageBuilderUrlRewriteGenerator = $pageBuilderUrlRewriteGenerator;
        $this->logger = $logger;
    }

    /**
     * Replace page url rewrites on store view save
     *
     * @param \Magento\Store\Model\ResourceModel\Store $object
     * @param \Magento\Store\Model\ResourceModel\Store|null $store
     * @return void
     */
    public function afterSave(
        \Magento\Store\Model\ResourceModel\Store $object,
        ?\Magento\Store\Model\ResourceModel\Store $store
    ): void {
        try {
            if ($store instanceof AbstractModel && $store->isObjectNew()) {
                $this->urlPersist->replace(
                    $this->generatePageBuilderUrls((int)$store->getId())
                );
            }
        } catch (Exception $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Generate url rewrites for cms pages to store view
     *
     * @param int $storeId
     * @return array
     * @throws LocalizedException
     */
    private function generatePageBuilderUrls(int $storeId): array
    {
        $rewrites = [];
        $urls = [];
        $this->searchCriteriaBuilder
            ->addFilter('store_id', '0')
            ->addFilter(ContentInterface::TYPE, ContentInterface::TYPE_PAGE);

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $pagesCollection = $this->contentRepository->getList($searchCriteria)->getItems();
        foreach ($pagesCollection as $page) {
            $page->setStoreIds($storeId);
            $rewrites[] = $this->pageBuilderUrlRewriteGenerator->generate($page);
        }
        return array_merge($urls, ...$rewrites);
    }
}
