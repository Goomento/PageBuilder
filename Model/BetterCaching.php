<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Exception;
use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Cache\LockGuardedCacheLoader;
use Magento\Framework\DataObject;
use Magento\PageCache\Model\Cache\Type;
use Magento\Framework\App\Cache\TypeListInterface;
use Zend_Json;

class BetterCaching
{
    /**
     * @var CacheInterface
     */
    private $cache;
    /**
     * @var StateInterface
     */
    private $cacheState;
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

    public function __construct(
        CacheInterface $cache,
        StateInterface $cacheState,
        LockGuardedCacheLoader $lockGuardedCacheLoader,
        TypeListInterface $typeList
    )
    {
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        $this->cacheTypeList = $typeList;
        $this->lockGuardedCacheLoader = $lockGuardedCacheLoader;
    }

    /**
     * Collect and Save to cache
     *
     * @param $key
     * @param callable|null $source
     * @param array $tags
     * @param int $timeout
     * @return bool|float|int|mixed|string
     */
    public function resolve($key, ?callable $source, array $tags = [Type::CACHE_TAG], int $timeout = Cache::TEN_MINUTES)
    {
        if (!is_scalar($key)) {
            $key = Zend_Json::encode($key);
        }

        $key = md5($key);

        $collectAction = function () use ($source) {
            $result = call_user_func($source);
            if (!is_scalar($result)) {
                $result = Zend_Json::encode($result);
            }

            return $result;
        };

        $tags = $this->getEnabledTags((array) $tags);
        if (!$timeout || !$tags) {
            return $collectAction();
        }

        $loadAction = function () use ($key) {
            $result = $this->cache->load($key);
            if ($result && (strpos($result, '{') !== 0 || strpos($result, '[') !== 0)) {
                try {
                    $result = Zend_Json::decode($result);
                } catch (Exception $e) {}
            }

            return $result;
        };

        $saveAction = function ($data) use ($key, $tags, $timeout) {
            $this->cache->save($data, $key, $tags, $timeout);
        };

        return $this->lockGuardedCacheLoader->lockedLoadData(
            $key,
            $loadAction,
            $collectAction,
            $saveAction
        );
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
            $types = $this->cacheTypeList->getTypes();
            foreach ($types as $type) {
                if ($type instanceof DataObject) {
                    $type = $type->toArray();
                }
                $type = (array) $type;
                if ($type['status']) {
                    $this->enabledCacheTags = array_merge($this->enabledCacheTags, (array) $type['tags']);
                }
            }
        }

        return array_intersect($this->enabledCacheTags, $tags);
    }
}
