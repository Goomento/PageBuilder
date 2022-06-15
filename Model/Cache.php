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
use Goomento\PageBuilder\Model\Cache\Type\PageBuilder;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\PageCache\Model\Cache\Type;
use Magento\Framework\App\Cache\Type\Block;
use Zend_Cache;
use Zend_Json;

class Cache
{
    const DAY_LIFE_TIME = 86400; // 01 day

    const HOUR_LIFE_TIME = 3600; // 01 hour

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
     * @var TypeListInterface
     */
    private $typeList;

    /**
     * @param CacheInterface $cache
     * @param StateInterface $cacheState
     * @param TypeListInterface $typeList
     */
    public function __construct(
        CacheInterface $cache,
        StateInterface $cacheState,
        TypeListInterface $typeList
    )
    {
        $this->cache = $cache;
        $this->typeList = $typeList;
        $this->enabled = $cacheState->isEnabled(PageBuilder::TYPE_IDENTIFIER);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool) $this->enabled;
    }

    /**
     * Set invalidate the cache
     *
     * @return void
     */
    public function invalid()
    {
        if ($this->isEnabled()) {
            $this->typeList->invalidate([
                PageBuilder::TYPE_IDENTIFIER,
                Type::TYPE_IDENTIFIER,
                Block::TYPE_IDENTIFIER
            ]);
        }
    }

    /**
     * Will return NULL if identifier did not exist
     * The data will be un-serialize automatically
     *
     * @param string $identifier
     * @return array|string|null
     */
    public function load(string $identifier)
    {
        if (!$this->isEnabled()) {
            return null;
        }
        $data = $this->cache->load($this->getCacheKey($identifier));
        return $data !== false ? $this->unSerializer($data) : null;
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
        if (!$this->isEnabled()) {
            return null;
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
        if (!$this->isEnabled()) {
            return null;
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
        if (!$this->isEnabled()) {
            return null;
        }

        return $this->cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, (array) $tags);
    }

    /**
     * @param $data
     * @return mixed|string
     */
    private function serializer($data)
    {
        if (!is_scalar($data)) {
            try {
                $data = Zend_Json::encode($data);
            } catch (Exception $e) {}
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
                $data = Zend_Json::decode($data);
            } catch (Exception $e) {}
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
