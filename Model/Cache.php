<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\App\CacheInterface;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilder;

/**
 * Class Cache
 * @package Goomento\PageBuilder\Model
 */
class Cache
{
    const DEFAULT_LIFE_TIME = 86400; // 01 day

    const CACHE_TAG = PageBuilder::CACHE_TAG;

    /**
     * @var Cache\Type\PageBuilder
     */
    private $cache;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param CacheInterface $cache
     * @param StateInterface $cacheState
     */
    public function __construct(
        CacheInterface $cache,
        StateInterface $cacheState
    )
    {
        $this->cache = $cache;
        $this->enabled = $cacheState->isEnabled(PageBuilder::TYPE_IDENTIFIER);
    }

    /**
     * @param string $identifier
     * @return mixed|string|null
     */
    public function load(string $identifier)
    {
        if (!$this->enabled) {
            return false;
        }
        $data = $this->cache->load($this->getCacheKey($identifier));
        return $this->unSerializer($data);
    }

    /**
     * @param mixed $data
     * @param $identifier
     * @param array|int $tags
     * @param null $lifeTime
     * @return bool
     */
    public function save($data, $identifier, $tags = [self::CACHE_TAG], $lifeTime = null)
    {
        if (!$this->enabled) {
            return false;
        }

        if (is_numeric($tags) && is_null($lifeTime)) {
            $lifeTime = (int) $tags;
            $tags = [self::CACHE_TAG];
        }
        $data = $this->serializer($data);
        return $this->cache->save((string)$data, $this->getCacheKey($identifier), $tags, $lifeTime);
    }

    /**
     * @param $identifier
     * @return bool
     */
    public function remove($identifier)
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->cache->remove(
            $this->getCacheKey($identifier)
        );
    }

    /**
     * @param array $tags
     * @return bool
     */
    public function clean($tags = [self::CACHE_TAG])
    {
        if (!$this->enabled) {
            return false;
        }
        return $this->cache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, (array) $tags);
    }

    /**
     * @param $data
     * @return mixed|string
     */
    private function serializer($data)
    {
        if (!is_scalar($data)) {
            try {
                $data = \Zend_Json::encode($data);
            } catch (\Exception $e) {}
        }

        return $data;
    }

    /**
     * @param $data
     * @return mixed|string|null
     */
    private function unSerializer($data)
    {
        if (is_string($data)) {
            try {
                $data = \Zend_Json::decode($data);
            } catch (\Exception $e) {}
        }

        return $data;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getCacheKey(string $key)
    {
        return 'pagebuilder_' . sha1($key);
    }
}
