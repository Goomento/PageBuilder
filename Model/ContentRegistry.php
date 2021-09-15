<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content as ContentResourceModel;
use Goomento\PageBuilder\Api\Data;
use Goomento\PageBuilder\Api\Data\ContentInterfaceFactory;
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
    private $cache;
    /**
     * @var ContentInterfaceFactory
     */
    private $contentFactory;
    /**
     * @var ContentResourceModel
     */
    private $contentResourceModel;
    /**
     * @var Content|null
     */
    private $processing;

    /**
     * ContentRegistry constructor.
     * @param ContentRepositoryInterface $contentRepository
     * @param ContentResourceModel $contentResourceModel
     * @param ContentInterfaceFactory $contentFactory
     * @param Cache $cache
     * @param Logger $logger
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        ContentResourceModel $contentResourceModel,
        ContentInterfaceFactory $contentFactory,
        Cache $cache,
        Logger $logger
    ) {
        $this->contentRepository = $contentRepository;
        $this->contentResourceModel = $contentResourceModel;
        $this->contentFactory = $contentFactory;
        $this->logger = $logger;
        $this->cache = $cache;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $contentId)
    {
        return $this->getBy($contentId, ContentInterface::CONTENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getByIdentifier(string $identifier)
    {
        return $this->getBy($identifier, ContentInterface::IDENTIFIER);
    }

    /**
     * @param string $field
     * @param $value
     * @return null
     * @throws \Exception
     */
    public function getBy($value, string $field)
    {
        $content = null;
        if (!empty($value)) {
            $actions = [
                'getFromInstance',
                'getFromCache',
                'getFromRepo',
                'saveToCache',
                'saveToInstance',
            ];
            try {
                $this->processing = null;
                foreach ($actions as $action) {
                    $result = $this->{$action}($value, $field);
                    if ($result) {
                        break;
                    }
                }
            } finally {
                $content = $this->processing;
                $this->processing = null;
            }
        }
        return $content;
    }

    /**
     * @param $value
     * @param $field
     * @return false Return false to allow process to continue and caching
     */
    public function getFromRepo($value, $field)
    {
        if (!$this->processing) {
            $model = $this->contentFactory->create();
            $this->contentResourceModel->load($model, $value, $field);
            if ($model && $model->getId()) {
                $this->processing = $model;
            } else {
                // stop processing
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @param $field
     * @return bool
     * @throws \Exception
     */
    private function getFromCache($value, $field)
    {
        if (!$this->processing) {
            $key = $this->getContentCacheKey($value);
            $data = $this->cache->load($key);
            // if identifier
            if ($data && is_numeric($data)) {
                $data = $this->cache->load(
                    $this->getContentCacheKey($data)
                );
            }
            if (!empty($data)) {
                try {
                    $data = \Zend_Json::decode($data);
                } catch (\Exception $e) {}
                if ($data) {
                    /** @var Content $content */
                    $content = $this->contentFactory->create();
                    $content->setData($data);
                    $content->setIsCaching(true);
                    $content->setOrigData();
                    $content->setHasDataChanges(false);
                    $this->processing = $content;
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param null $value
     * @param null $field
     * @return false
     * @throws \Exception
     */
    private function saveToCache($value = null, $field = null)
    {
        if ($this->processing && $this->processing->getIsCaching() !== true) {
            $data = \Zend_Json::encode($this->processing);
            $this->cache->saveToContentCollection(
                $data,
                $this->getContentCacheKey($this->processing->getId())
            );
            $identifier = $this->processing->getIdentifier();
            if ($identifier) {
                $this->cache->saveToContentCollection(
                    $this->processing->getId(),
                    $this->getContentCacheKey($identifier)
                );
            }
        }

        return false;
    }

    /**
     * @param $content
     * @throws LocalizedException|\Exception
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

        $this->cache->remove($this->getContentCacheKey($id));
//        $this->cache->remove($this->getContentCacheKey($content->getIdentifier()));
    }


    private function cleanContentIdentifier($contentId)
    {

    }

    /**
     * @param $id
     * @return string
     * @throws \Exception
     */
    private function getContentCacheKey($id)
    {
        if (!($id = trim((string) $id))) {
            throw new LocalizedException(
                __('Content identifier must not empty.')
            );
        }
        return md5('pagebuilder_content_' . $id);
    }

    /**
     * @param int $id
     * @return void
     * @throws LocalizedException
     */
    public function delete(int $id)
    {
        $this->cleanContentCache($id);
        $this->contentRepository->deleteById($id);
    }

    /**
     * @param $value
     * @param $field
     * @return false
     */
    private function saveToInstance($value, $field)
    {
        if ($this->processing && $this->processing->getId()) {
            $this->registry[$this->processing->getId()] = $this->processing;
        }

        return false;
    }

    /**
     * @param int|string $value
     * @param string $field
     * @return bool
     */
    private function getFromInstance($value, string $field)
    {
        if (!$this->processing) {
            foreach ($this->registry as $instance) {
                if ($instance && $instance->getData($field) == $value) {
                    $this->processing = $instance;
                    return true;
                }
            }
        }

        return false;
    }
}
