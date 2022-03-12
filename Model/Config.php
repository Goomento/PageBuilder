<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Config
{
    const CSS_UPDATED_TIME = 'css_updated_time';
    const CUSTOM_CSS = 'custom_css';
    const DEFAULT_STORE_ID = Store::DEFAULT_STORE_ID;

    const CACHE_KEY = 'config_data';

    /**
     * @var bool
     */
    private $dataChanged = [];

    protected $loaded = false;
    /**
     * @var ResourceModel\Config
     */
    private $configResource;

    /**
     * Eg: [path => [storeId => value]]
     *
     * @var array
     */
    private $config = [];
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var array
     */
    private static $underscoreCache = [];

    /**
     * @param ResourceModel\Config $configResource
     * @param Cache $cache
     */
    public function __construct(
        ResourceModel\Config $configResource,
        Cache $cache
    )
    {
        $this->configResource = $configResource;
        $this->cache = $cache;
    }

    /**
     * @throws LocalizedException
     */
    private function loadAll()
    {
        if ($this->loaded === false) {
            $this->loaded = true;
            $configs = $this->cache->load(self::CACHE_KEY);
            if ($configs) {
                $this->config = $configs;
            } else {
                $configs = $this->configResource->fetchAll();
                if (!empty($configs)) {
                    foreach ($configs as $configData) {
                        $value = $configData['value'];
                        $this->_setValue($configData['path'], $value, (int) $configData['store_id']);
                        $this->dataChanged = false;
                    }
                }
            }
        }
    }

    /**
     * @param $path
     * @param $value
     * @param int $storeId
     * @return $this
     */
    private function _setValue($path, $value, int $storeId = self::DEFAULT_STORE_ID)
    {
        if (!isset($this->config[$path])) {
            $this->config[$path] = [];
        }
        $originValue = $this->config[$path][$storeId] ?? null;
        if ($originValue !== $value) {
            if (!isset($this->dataChanged[$path])) {
                $this->dataChanged[$path] = [];
            }
            $this->dataChanged[$path][$storeId] = $value;
        }
        $this->config[$path][$storeId] = $value;
        return $this;
    }


    /**
     * @param $path
     * @param int $storeId
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getValue($path,int $storeId = self::DEFAULT_STORE_ID)
    {
        $this->loadAll();
        $valueData = $this->config[$path] ?? [];
        $value = null;
        if (isset($valueData[$storeId])) {
            $value = $valueData[$storeId];
        } elseif ($storeId !== self::DEFAULT_STORE_ID && isset($valueData[self::DEFAULT_STORE_ID])) {
            $value = $valueData[self::DEFAULT_STORE_ID];
        }
        return $value;
    }

    /**
     * @param $path
     * @param $value
     * @param int $storeId
     * @return Config
     * @throws LocalizedException
     */
    public function setValue($path, $value, int $storeId = self::DEFAULT_STORE_ID)
    {
        $this->_setValue($path, $value, (int) $storeId);
        $this->save();
        return $this;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return Config
     * @throws LocalizedException
     */
    public function deleteValue($path, int $storeId = self::DEFAULT_STORE_ID)
    {
        if (isset($this->config[$path][$storeId])) {
            unset($this->config[$path][$storeId]);
        }

        if (!isset($this->dataChanged[$path])) {
            $this->dataChanged[$path] = [];
        }

        $this->dataChanged[$path][$storeId] = null;

        $this->save();

        return $this;
    }

    /**
     * @return Config
     * @throws LocalizedException
     */
    public function save()
    {
        if ($this->dataChanged) {
            foreach ($this->dataChanged as $path => $storeValue) {
                foreach ($storeValue as $storeId => $value) {
                    if ($value === null) {
                        $this->configResource->deleteConfig($path, $storeId);
                    } else {
                        $this->configResource->saveConfig($path, $value, $storeId);
                    }
                }
            }
            $this->dataChanged = [];
            $this->saveToCache();
        }

        return $this;
    }

    /**
     * @param $path
     * @param null $default
     * @param int $storeId
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getOption($path, $default = null, $storeId = 0)
    {
        $value = $this->getValue("option_{$path}", $storeId);
        return $value === null ? $default : $value;
    }

    /**
     * @param $path
     * @param mixed $value
     * @return $this
     * @throws LocalizedException
     */
    public function setOption($path, $value)
    {
        return $this->setValue("option_{$path}", $value);
    }

    /**
     * @param $path
     * @param int $storeId
     * @return $this
     * @throws LocalizedException
     */
    public function delOption($path, $storeId = 0)
    {
        return $this->deleteValue("option_{$path}", $storeId);
    }

    /**
     * Save configs to cache
     */
    private function saveToCache()
    {
        if (!empty($this->config)) {
            $this->cache->save($this->config, self::CACHE_KEY);
        }
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     * @throws LocalizedException
     */
    public function __call($method, $args)
    {
        $storeId = $args[0] ?? self::DEFAULT_STORE_ID;
        switch (substr($method, 0, 3)) {
            case 'get':
                $key = $this->underscore(substr($method, 3));
                return $this->getValue($key, $storeId);
            case 'set':
                $key = $this->underscore(substr($method, 3));
                $value = $args[1] ?? null;
                return $this->setValue($key, $value, $storeId);
            case 'del':
                $key = $this->underscore(substr($method, 3));
                return $this->deleteValue($key, $storeId);
        }
        throw new LocalizedException(
            __('Invalid method %1::%2', [get_class($this), $method])
        );
    }

    /**
     * Converts field names for setters and getters
     *
     * @param string $name
     * @return string
     */
    protected function underscore($name)
    {
        if (isset(self::$underscoreCache[$name])) {
            return self::$underscoreCache[$name];
        }
        $result = strtolower(trim(preg_replace('/([A-Z]|[0-9]+)/', "_$1", $name), '_'));
        self::$underscoreCache[$name] = $result;
        return $result;
    }
}
