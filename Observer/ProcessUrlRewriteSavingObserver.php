<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Observer;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Framework\Event\ObserverInterface;
use Goomento\PageBuilder\Model\PageBuilderUrlRewriteGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class ProcessUrlRewriteSavingObserver implements ObserverInterface
{
    /**
     * @var PageBuilderUrlRewriteGenerator
     */
    protected $pageBuilderUrlRewriteGenerator;
    /**
     * @var UrlPersistInterface
     */
    protected $urlPersist;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param PageBuilderUrlRewriteGenerator $cmsPageUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param Logger $logger
     */
    public function __construct(
        PageBuilderUrlRewriteGenerator $cmsPageUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        Logger $logger
    ) {
        $this->pageBuilderUrlRewriteGenerator = $cmsPageUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->logger = $logger;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param EventObserver $observer
     * @return void
     * @throws UrlAlreadyExistsException
     */
    public function execute(EventObserver $observer)
    {
        try {
            /** @var $content ContentInterface */
            $content = $observer->getEvent()->getObject();

            if ($content->getType() === ContentInterface::TYPE_PAGE &&
                $content->getFlag('updated_url_rewrite') !== true &&
                ($content->dataHasChangedFor('identifier') || $content->dataHasChangedFor('store_ids'))) {
                $urls = $this->pageBuilderUrlRewriteGenerator->generate($content);

                $this->urlPersist->deleteByData([
                    UrlRewrite::ENTITY_ID => $content->getId(),
                    UrlRewrite::ENTITY_TYPE => PageBuilderUrlRewriteGenerator::ENTITY_TYPE,
                ]);

                $this->urlPersist->replace($urls);

                $content->setFlag('updated_url_rewrite', true);
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
            throw $e;
        }
    }
}
