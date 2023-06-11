<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterfaceFactory;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\PageCache\Model\Cache\Type as FullPageCache;

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
     * @var BetterCaching
     */
    private $cache;
    /**
     * @var ContentInterfaceFactory
     */
    private $contentFactory;
    /**
     * @var Content|null
     */
    private $processing;

    /**
     * @var array
     */
    private $identifiers;
    /**
     * @var ContentRepositoryInterface
     */
    private $contentRepository;
    /**
     * @var FullPageCache
     */
    private $fullPageCache;

    /**
     * ContentRegistry constructor.
     * @param ContentInterfaceFactory $contentFactory
     * @param ContentRepositoryInterface $contentRepository
     * @param FullPageCache $fullPageCache
     * @param BetterCaching $cache
     * @param Logger $logger
     */
    public function __construct(
        ContentInterfaceFactory $contentFactory,
        ContentRepositoryInterface $contentRepository,
        FullPageCache $fullPageCache,
        BetterCaching $cache,
        Logger $logger
    ) {
        $this->contentFactory = $contentFactory;
        $this->contentRepository = $contentRepository;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->fullPageCache = $fullPageCache;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $contentId)
    {
        return $this->getBy($contentId);
    }

    /**
     * @inheritDoc
     */
    public function getByIdentifier(string $identifier)
    {
        return $this->getBy($identifier);
    }

    /**
     * @param $value
     * @return ContentInterface|null
     * @throws Exception
     */
    public function getBy($value)
    {
        $content = null;
        if (!empty($value)) {
            $field = filter_var($value, FILTER_VALIDATE_INT) ? ContentInterface::CONTENT_ID : ContentInterface::IDENTIFIER;
            $actions = [
                [$this, 'getFromInstance'],
                [$this, 'getFromCache'],
                [$this, 'getFromRepo'],
                [$this, 'saveToCache'],
                [$this, 'saveToInstance'],
            ];
            try {
                $this->processing = null;
                foreach ($actions as $action) {
                    // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                    $result = call_user_func($action, $value, $field);
                    if ($result) {
                        break;
                    }
                }
            } catch (\Exception $e) {
                $this->logger->error($e);
                throw $e;
            } finally {
                $content = $this->processing;
                $this->processing = null;
            }
        }
        return $content;
    }

    /**
     * @param int|string $value
     * @param $field
     * @return false Return false to allow process to continue and caching
     */
    private function getFromRepo($value, $field)
    {
        if (!$this->processing) {
            try {
                if ($field === ContentInterface::CONTENT_ID) {
                    $model = $this->contentRepository->getById((int) $value);
                } else {
                    $model = $this->contentRepository->getByIdentifier((string) $value);
                }
            } catch (NoSuchEntityException $e) {
                $model = null;
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
                $content->setFlag('is_caching', true);
                $content->setOrigData();
                $content->setHasDataChanges(false);
                $content->afterLoad();
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
    private function cleanContentCache($content)
    {
        $id = $content;
        if ($content instanceof ContentInterface) {
            $id = $content->getId();
        }

        // Remove from instance
        if (isset($this->registry[$id])) {
            unset($this->registry[$id]);
        }

        // Remove from cache
        $this->cache->remove($this->getContentCacheKey($id));

        // Clean full page cache
        $this->fullPageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, [$this->getContentCacheKey($id)]);

        // Remove identifier
        $this->contentIdentifier(false, $id);
    }


    /**
     * @param $identifier
     * @param null $contentId
     * @return mixed|null
     */
    private function contentIdentifier($identifier, $contentId = null)
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
     * @inheriDoc
     */
    public function invalidateContent($content)
    {
        if ($content instanceof ContentInterface) {
            $id = $content->getId();
        } else {
            $id = $content;
        }

        $this->cleanContentCache($id);
        $this->contentIdentifier($id, null);
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

    /**
     * @param $id
     * @return string
     */
    private function getContentCacheKey($id)
    {
        return 'pagebuilder_content_' . (string) $id;
    }
}
