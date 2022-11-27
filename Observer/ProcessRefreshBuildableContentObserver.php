<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Observer;

use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class ProcessRefreshBuildableContentObserver implements ObserverInterface
{
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var BuildableContentManagementInterface
     */
    private $buildableContentManagement;

    /**
     * @param BuildableContentManagementInterface $buildableContentManagement
     * @param Logger $logger
     */
    public function __construct(
        BuildableContentManagementInterface $buildableContentManagement,
        Logger $logger
    ) {
        $this->buildableContentManagement = $buildableContentManagement;
        $this->logger = $logger;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        try {
            /** @var $content BuildableContentInterface */
            $content = $observer->getEvent()->getObject();

            if ($content instanceof BuildableContentInterface && $content->getFlag('is_refreshing_assets') !== true) {
                $this->buildableContentManagement->refreshBuildableContentAssets($content);
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}
