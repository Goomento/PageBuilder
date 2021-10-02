<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;


use Exception;
use Goomento\PageBuilder\Model\ResourceModel\Content as ContentResourceModel;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory as ContentCollectionFactory;
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
    const CACHE_KEY_IDENTIFIER_MAPPING = 'pagebuilder_identifier_mapping';
    /**
     * @var Content[]
     */
    protected $registry = [];
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

    private $allowFields = [
        ContentInterface::CONTENT_ID,
        ContentInterface::IDENTIFIER,
    ];
    /**
     * @var ContentCollectionFactory
     */
    private $contentCollectionFactory;

    /**
     * @var array
     */
    private $identifiers;

    /**
     * ContentRegistry constructor.
     * @param ContentResourceModel $contentResourceModel
     * @param ContentInterfaceFactory $contentFactory
     * @param ContentCollectionFactory $contentCollectionFactory
     * @param Cache $cache
     * @param Logger $logger
     */
    public function __construct(
        ContentResourceModel $contentResourceModel,
        ContentInterfaceFactory $contentFactory,
        ContentCollectionFactory $contentCollectionFactory,
        Cache $cache,
        Logger $logger
    ) {
        $this->contentCollectionFactory = $contentCollectionFactory;
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
     * @throws Exception
     */
    public function getBy($value, string $field)
    {
        $content = null;
        if (!empty($value) && in_array($field, $this->allowFields)) {
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
            if ($field === ContentInterface::CONTENT_ID) {
                $model = $this->contentFactory->create();
                $this->contentResourceModel->load($model, $value);
            } else {
                $collection = $this->contentCollectionFactory->create();
                $collection->addFieldToFilter($field, ['eq' => $value]);
                $model = $collection->getFirstItem();
            }

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
     * @throws Exception
     */
    private function getFromCache($value, $field)
    {
        if (!$this->processing) {
            if ($field === ContentInterface::IDENTIFIER) {
                $value = $this->contentIdentifier($value);
                if (!$value) {
                    return false;
                }
            }
            $key = $this->getContentCacheKey($value);
            $data = $this->cache->load($key);
            if (!empty($data)) {
                /** @var Content $content */
                $content = $this->contentFactory->create();
                $content->setData($data);
                $content->setIsCaching(true);
                $content->setOrigData();
                $content->setHasDataChanges(false);
                $this->processing = $content;
            }
        }

        return false;
    }

    /**
     * @param null $value
     * @param null $field
     * @return false
     * @throws Exception
     */
    private function saveToCache($value = null, $field = null)
    {
        if ($this->processing && $this->processing->getIsCaching() !== true) {
            $this->cache->save(
                $this->processing->toArray(),
                $this->getContentCacheKey($this->processing->getId())
            );
            $identifier = $this->processing->getIdentifier();
            $this->contentIdentifier($identifier, $this->processing->getId());
        }

        return false;
    }

    /**
     * @param $content
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
        $this->contentIdentifier(false, $id);
    }


    /**
     * @param $identifier
     * @param null $contentId
     * @return mixed|null
     */
    public function contentIdentifier($identifier, $contentId = null)
    {
        $result = null;

        if (is_null($this->identifiers)) {
            $this->identifiers = [];
            $this->identifiers = (array) $this->cache->load(self::CACHE_KEY_IDENTIFIER_MAPPING);
        }

        if (!is_null($contentId)) {
            if (!$identifier && isset($this->identifiers[$contentId])) {
                unset($this->identifiers[$contentId]);
            } elseif ($identifier) {
                $this->identifiers[$contentId] = $identifier;
            }

            $this->cache->save($this->identifiers, self::CACHE_KEY_IDENTIFIER_MAPPING);
            $result = $contentId;
        } else {
            foreach ($this->identifiers as $id => $url) {
                if ($identifier === $url) {
                    $result = $id;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @param $id
     * @return string
     */
    private function getContentCacheKey($id)
    {
        return 'pagebuilder_content_' . (string) $id;
    }

    /**
     * @param int $id
     * @return void
     * @throws LocalizedException
     */
    public function delete(int $id)
    {
        $this->cleanContentCache($id);
        $this->contentResourceModel->delete(
            $this->getById($id)
        );
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
