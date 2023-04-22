<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Helper\DataHelper;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Cache\LockGuardedCacheLoader;
use Magento\Framework\DataObject;
use Magento\Framework\App\Cache\TypeListInterface;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderFrontend;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderBackend;

class BetterCaching
{
    const FIFTEEN_MIN_TIME = 54000; // 15 mins

    const DAY_LIFE_TIME = 86400; // 01 day

    const HOUR_LIFE_TIME = 3600; // 01 hour

    const DEFAULT_TIMEOUT = self::HOUR_LIFE_TIME;

    const FRONTEND_CACHE_TAG = PageBuilderFrontend::CACHE_TAG;

    const BACKEND_CACHE_TAG = PageBuilderBackend::CACHE_TAG;

    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var string
     */
    private $enabledCacheTags;
    /**
     * @var LockGuardedCacheLoader
     */
    private $lockGuardedCacheLoader;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;
    /**
     * @var array
     */
    private $cacheTypes;

    /**
     * @param CacheInterface $cache
     * @param LockGuardedCacheLoader $lockGuardedCacheLoader
     * @param TypeListInterface $typeList
     */
    public function __construct(
        CacheInterface $cache,
        LockGuardedCacheLoader $lockGuardedCacheLoader,
        TypeListInterface $typeList
    ) {
        $this->cache = $cache;
        $this->cacheTypeList = $typeList;
        $this->lockGuardedCacheLoader = $lockGuardedCacheLoader;
    }

    /**
     * Collect and Save to cache
     *
     * @param string $key
     * @param callable|mixed|null $source Callable must return array to store
     * @param array|string $tags
     * @param int|null $timeout null then data will be saved forever
     * @return bool|float|int|mixed|string
     */
    public function resolve(string $key, $source = false, $tags = self::FRONTEND_CACHE_TAG, ?int $timeout = self::DEFAULT_TIMEOUT)
    {
        /**
         * @return bool|float|int|string
         */
        $collectAction = function () use ($source) {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            return is_callable($source) ? call_user_func($source) : $source;
        };

        $tags = $this->getEnabledTags((array) $tags);
        if (empty($tags)) {
            return $collectAction();
        }

        /**
         * @return mixed|string|null
         */
        $loadAction = function () use ($key) {
            return $this->load($key);
        };

        /**
         * @param string|false $data
         * @return void
         */
        $saveAction = function ($data) use ($key, $tags, $timeout) {
            if ($data !== false) {
                $this->save($data, $key, $tags, $timeout);
            }
        };

        return $this->lockGuardedCacheLoader->lockedLoadData(
            $key,
            $loadAction,
            $collectAction,
            $saveAction
        );
    }

    /**
     * @param string $identifier
     * @return bool
     */
    public function remove(string $identifier)
    {
        return $this->cache->remove($identifier);
    }

    /**
     * @param array|string $tags
     * @return bool
     */
    public function clean($tags = self::FRONTEND_CACHE_TAG)
    {
        return $this->cache->clean((array) $tags);
    }

    /**
     * @param string|array $tags
     * @return void
     */
    public function invalidByTags($tags) : void
    {
        $tags = (array) $tags;
        $cacheIds = $this->getCacheIdByTags($tags);
        $this->invalid(array_values($cacheIds));
    }

    /**
     * @param string|array $cacheId
     * @return void
     */
    public function invalid($cacheId) : void
    {
        $cacheId = (array) $cacheId;
        $this->cacheTypeList->invalidate($cacheId);
    }

    /**
     * @param $data
     * @param string $identifier
     * @param array|string $tags
     * @param int|null $timeout
     * @return void
     */
    public function save($data, string $identifier, $tags = self::FRONTEND_CACHE_TAG, ?int $timeout = self::DEFAULT_TIMEOUT) : void
    {
        $tags = (array) $tags;
        $tags = $this->getEnabledTags($tags);
        if (!empty($tags)) {
            if (!is_scalar($data)) {
                try {
                    $data = DataHelper::encode($data);
                } catch (\Exception $e) {

                }
            }
            $this->cache->save($data, $identifier, (array) $tags, $timeout);
        }
    }

    /**
     * @param string $identifier
     * @return mixed|string|null return false if have no cache
     */
    public function load(string $identifier)
    {
        $result = $this->cache->load($identifier);
        if ($result && (strpos($result, '{') !== 0 || strpos($result, '[') !== 0)) {
            try {
                $result = DataHelper::decode($result);
            } catch (Exception $e) {

            }
        }

        return $result;
    }

    /**
     * Check caches should enable
     *
     * @param $tags
     * @return array
     */
    private function getEnabledTags($tags)
    {
        $tags = (array) $tags;
        if ($this->enabledCacheTags === null) {
            $this->enabledCacheTags = [];
            foreach ($this->getCacheTypes() as $typeId => $type) {
                // get enable cache
                if ($type['status']) {
                    $this->enabledCacheTags = array_merge($this->enabledCacheTags, (array) $type['tags']);
                }
            }
        }

        return (array) array_intersect($this->enabledCacheTags, $tags);
    }

    /**
     * @param string|array $tags
     * @return array
     */
    protected function getCacheIdByTags($tags) : array
    {
        $tags = (array) $tags;
        $result = [];
        foreach ($this->getCacheTypes() as $typeId => $type) {
            if (in_array($type['tags'], $tags)) {
                $result[$type['tags']] = $typeId;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function getCacheTypes()
    {
        if ($this->cacheTypes === null) {
            $this->cacheTypes = [];
            $types = $this->cacheTypeList->getTypes();
            foreach ($types as $type) {
                if ($type instanceof DataObject) {
                    $type = $type->toArray();
                }
                $type = (array) $type;
                $this->cacheTypes[$type['id']] = $type;
            }
        }

        return $this->cacheTypes;
    }
}
