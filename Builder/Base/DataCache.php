<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Helper\DataHelper;
use Magento\Framework\DataObject;
use Goomento\PageBuilder\Helper\CacheHelper;

class DataCache extends DataObject
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @param string $identifier
     * @return DataCache
     */
    public function setId(string $identifier)
    {
        $this->identifier = 'data_cache-' . $identifier;
        return $this;
    }

    /**
     * @param string $path
     * @param $value
     * @return DataCache
     */
    public function setDataByPath(string $path, $value)
    {
        $data = $this->getData();
        DataHelper::arraySetValue($data, $path, $value);
        $this->setData($data);
        return $this;
    }

    /**
     * @param string $path
     * @return DataCache
     */
    public function unsetDataByPath(string $path)
    {
        $data = $this->getData();
        DataHelper::arrayUnsetValue($data, $path);
        $this->setData($data);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setData($key, $value = null)
    {
        parent::setData($key, $value);
        $this->save();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        parent::offsetSet($offset, $value);
        $this->save();
    }

    /**
     * Save data to cache
     *
     * @return $this
     */
    private function save()
    {
        CacheHelper::save($this->_data, $this->identifier, CacheHelper::BACKEND_CACHE_TAG, null);
        return $this;
    }

    /**
     * Load from cache
     *
     * @param string|null $identifier
     * @return $this
     */
    public function load(?string $identifier = null)
    {
        if ($identifier !== null) {
            $this->setId($identifier);
        }
        $cachedData = CacheHelper::load($this->identifier);
        if ($cachedData !== false) {
            $this->_data = $cachedData;
        }

        return $this;
    }
}
