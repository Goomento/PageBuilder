<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Observer;

use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProcessRefreshContentCacheObserver implements ObserverInterface
{
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param ContentRegistryInterface $contentRegistry
     */
    public function __construct(
        ContentRegistryInterface $contentRegistry
    ) {
        $this->contentRegistry = $contentRegistry;
    }

    /**
     * Refreshing Content Cache
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var $content ContentInterface */
        $content = $observer->getEvent()->getObject();
        if ($content instanceof ContentInterface) {
            $this->contentRegistry->invalidateContent($content);
        }
    }
}
