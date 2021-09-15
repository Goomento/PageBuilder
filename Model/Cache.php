<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Magento\Framework\App\CacheInterface;

/**
 * Class Cache
 * @package Goomento\PageBuilder\Model
 */
class Cache implements CacheInterface
{
    const CONTENT_COLLECTION_TAG = 'pagebuilder_content';

    const DEFAULT_LIFE_TIME = 3600;

    /**
     * @var Cache\Type\PageBuilder
     */
    private $frontend;

    /**
     * @param Cache\Type\PageBuilder $builderCache
     */
    public function __construct(
        Cache\Type\PageBuilder $builderCache
    )
    {
        $this->frontend = $builderCache;
    }

    /**
     * @inheritDoc
     */
    public function getFrontend()
    {
        return $this->frontend;
    }

    /**
     * @inheritDoc
     */
    public function load($identifier)
    {
        return $this->frontend->load($identifier);
    }

    /**
     * @inheritDoc
     */
    public function save($data, $identifier, $tags = [], $lifeTime = null)
    {
        return $this->frontend->save((string)$data, $identifier, $tags, $lifeTime);
    }

    /**
     * @inheritDoc
     */
    public function remove($identifier)
    {
        return $this->frontend->remove($identifier);
    }

    /**
     * @inheritDoc
     */
    public function clean($tags = [])
    {
        return $this->frontend->clean(\Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, (array)$tags);
    }

    /**
     * @param $data
     * @param $identifier
     * @param null $lifeTime
     * @return bool
     */
    public function saveToContentCollection($data, $identifier, $lifeTime = self::DEFAULT_LIFE_TIME)
    {
        return $this->save(
            $data,
            $identifier,
            [self::CONTENT_COLLECTION_TAG],
            $lifeTime
        );
    }

    /**
     * @return bool
     */
    public function cleanContentCollection()
    {
        return $this->clean([self::DEFAULT_LIFE_TIME]);
    }
}
