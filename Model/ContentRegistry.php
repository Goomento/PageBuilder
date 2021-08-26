<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterfaceFactory;
use Goomento\PageBuilder\Helper\Cache;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ContentRegistry
 * @package Goomento\PageBuilder\Model
 */
class ContentRegistry implements ContentRegistryInterface
{
    /**
     * @var Content[]
     */
    protected $registry = [];

    /**
     * @var ContentRepositoryInterface
     */
    protected $contentRepository;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var Cache
     */
    private $cacheHelper;
    /**
     * @var ContentInterfaceFactory
     */
    private $contentFactory;

    /**
     * ContentRegistry constructor.
     * @param ContentRepositoryInterface $contentRepository
     * @param ContentInterfaceFactory $contentFactory
     * @param Cache $cacheHelper
     * @param Logger $logger
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        ContentInterfaceFactory $contentFactory,
        Cache $cacheHelper,
        Logger $logger
    ) {
        $this->contentRepository = $contentRepository;
        $this->contentFactory = $contentFactory;
        $this->logger = $logger;
        $this->cacheHelper = $cacheHelper;
    }

    /**
     * @inheritDoc
     */
    public function get(int $contentId)
    {
        $content = null;
        try {
            $content = $this->getInstance($contentId);
            if (!$content) {
                $content = $this->getFromCache($contentId);
                if (empty($content)) {
                    $content = $this->contentRepository->getById($contentId);
                    if ($content && $content->getId()) {
                        $this->saveToCache($content);
                    }
                }
                $this->setInstance($contentId, $content);
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }

        return $content;
    }

    /**
     * @param $id
     * @return Content|null
     * @throws LocalizedException
     */
    private function getFromCache($id)
    {
        $key = $this->getContentCacheKey($id);
        $data = $this->cacheHelper->load($key);
        if (!empty($data)) {
            try {
                $data = \Zend_Json::decode($data);
            } catch (\Exception $e) {
                $data = null;
            }
            if ($data) {
                /** @var Content $content */
                $content = $this->contentFactory->create();
                $content->setData($data);
                $content->setIsCaching(true);
                $content->setOrigData();
                $content->setHasDataChanges(false);
                return $content;
            }
        }

        return null;
    }

    /**
     * @param ContentInterface $content
     * @throws LocalizedException
     */
    private function saveToCache(ContentInterface $content)
    {
        /** @var Content $content */
        if ($content->getIsCaching() !== true) {
            $data = \Zend_Json::encode($content);
            if (!empty($data)){
                $this->cacheHelper->saveToContentCollection(
                    $data,
                    $this->getContentCacheKey($content->getId())
                );
            }
        }
    }

    /**
     * @param $content
     * @throws LocalizedException
     */
    public function cleanContentCache($content)
    {
        $id = $content;
        if ($content instanceof ContentInterface) {
            $id = $content->getId();
        }

        if (isset($this->registry[$id])) {
            unset($this->registry[$id]);
        }

        $this->cacheHelper->remove($this->getContentCacheKey($id));
    }

    /**
     * @param $id
     * @return string
     * @throws LocalizedException
     */
    private function getContentCacheKey($id)
    {
        $id = (int) $id;
        if ($id < 1) {
            throw new LocalizedException(
                __('Invalid content Id: %1', $id)
            );
        }
        return 'pagebuilder_content_' . (string) $id;
    }

    /**
     * @param $id
     * @return void
     * @throws LocalizedException
     */
    public function delete($id)
    {
        $this->cleanContentCache($id);
        $this->contentRepository->deleteById($id);
    }

    /**
     * @param $id
     * @param ContentInterface|null $content
     * @return ContentRegistry
     */
    private function setInstance($id, ContentInterface $content = null)
    {
        $this->registry[$id] = $content;
        return $this;
    }

    /**
     * @param $id
     * @return Content|null
     */
    private function getInstance($id)
    {
        return $this->registry[$id] ?? null;
    }
}
