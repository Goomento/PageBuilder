<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;

class Config implements ConfigInterface
{
    const CSS_UPDATED_TIME = 'css_updated_time';

    const CUSTOM_CSS = 'custom_css';

    const CACHE_KEY = 'pagebuilder_config_data';

    /**
     * @var array
     */
    private $dataChanged = [];

    /**
     * @deprecated
     * @var bool
     */
    protected $loaded = false;

    /**
     * @var ResourceModel\Config
     */
    private $configResource;

    /**
     * Eg: [$store_id => [ $path => $value]]
     *
     * @var array
     */
    private $config = null;

    /**
     * @var BetterCaching
     */
    private $cache;
    /**
     * @var array
     */
    private static $underscoreCache = [];

    /**
     * @param ResourceModel\Config $configResource
     * @param BetterCaching $cache
     */
    public function __construct(
        ResourceModel\Config $configResource,
        BetterCaching $cache
    ) {
        $this->configResource = $configResource;
        $this->cache = $cache;
    }

    /**
     * Load all configs
     */
    private function load()
    {
        if ($this->config === null) {
            $this->config = [];
            $configs = $this->cache->load(self::CACHE_KEY);
            if ($configs !== false) {
                $this->config = $configs;
            } else {
                $configs = $this->configResource->fetchAll();
                if (!empty($configs)) {
                    foreach ($configs as $configData) {
                        $value = $configData['value'];
                        $this->setConfigData($configData['path'], $value, (int) $configData['store_id']);
                    }

                    $this->dataChanged = [];
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
    private function setConfigData($path, $value, int $storeId = self::DEFAULT_STORE_ID)
    {
        if (!isset($this->config[$storeId])) {
            $this->config[$storeId] = [];
        }

        if (!isset($this->config[$storeId][$path]) ||
            (isset($this->config[$storeId][$path]) && $this->config[$storeId][$path] !== $value)) {
            if (!isset($this->dataChanged[$storeId])) {
                $this->dataChanged[$storeId] = [];
            }
            $this->dataChanged[$storeId][$path] = $value;

            $this->config[$storeId][$path] = $value;
        }

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function getValue($path, int $storeId = self::DEFAULT_STORE_ID)
    {
        $this->load();
        $storeData = $this->config[$storeId] ?? [];
        $value = null;
        if (isset($storeData[$path])) {
            $value = $storeData[$path];
        } elseif ($storeId !== self::DEFAULT_STORE_ID && isset($this->config[self::DEFAULT_STORE_ID][$path])) {
            $value = $this->config[self::DEFAULT_STORE_ID][$path];
        }
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function setValue($path, $value = null, int $storeId = self::DEFAULT_STORE_ID)
    {
        $this->load();
        if (is_array($path)) {
            foreach ($path as $key => $valueData) {
                $this->setConfigData($key, $valueData, $storeId);
            }
        } else {
            $this->setConfigData($path, $value, $storeId);
        }

        $this->save();
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function deleteValue($path, int $storeId = self::DEFAULT_STORE_ID)
    {
        $this->load();
        $this->setValue($path, null);
        return $this;
    }

    /**
     * @return Config
     * @throws LocalizedException
     */
    public function save()
    {
        $this->load();
        if (!empty($this->dataChanged)) {
            $changedData = $this->dataChanged;
            $this->dataChanged = [];

            foreach ($changedData as $storeId => $storeConfig) {
                foreach ($storeConfig as $path => $value) {
                    if ($value === null) {
                        $this->configResource->deleteConfig($path, $storeId);
                    } else {
                        $this->configResource->saveConfig($path, $value, $storeId);
                    }
                }
            }

            $this->saveToCache();
        }

        return $this;
    }

    /**
     * Save configs to cache
     */
    private function saveToCache()
    {
        $this->cache->save((array) $this->config, self::CACHE_KEY, BetterCaching::BACKEND_CACHE_TAG);
    }

    /**
     * Set/Get attribute wrapper
     *
     * @param   string $method
     * @param   array $args
     * @return  mixed
     * @noinspection PhpDocMissingThrowsInspection
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
