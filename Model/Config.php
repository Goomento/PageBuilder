<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Content
 * @package Goomento\PageBuilder\Model
 */
class Config extends DataObject
{
    protected $loaded = false;
    /**
     * @var ResourceModel\Config
     */
    private $configResource;

    /**
     * @var array
     */
    private $config = [];

    /**
     * @param ResourceModel\Config $configResource
     * @param array $data
     */
    public function __construct(
        ResourceModel\Config $configResource,
        array $data = []
    )
    {
        $this->configResource = $configResource;
        parent::__construct($data);
    }

    /**
     * @throws LocalizedException
     */
    public function load()
    {
        if ($this->loaded === false) {
            $configs = $this->configResource->fetchAll();
            if (!empty($configs)) {
                foreach ($configs as $configData) {
                    $this->setValue($configData['path'], $configData['value'], (int) $configData['store_id']);
                }
            }

            $this->loaded = true;
        }
    }

    /**
     * @param $path
     * @param int $storeId
     * @return mixed|null
     * @throws LocalizedException
     */
    public function getValue($path,int $storeId = 0)
    {
        $this->load();
        if ($storeId !== 0) {
            if (!isset($this->config[$storeId])) {
                return $this->getValue($path, 0);
            }
        }

        if (!isset($this->config[$storeId])) {
            $value = null;
        } else {
            $value = $this->config[$storeId][$path] ?? null;
        }

        return $value;
    }

    /**
     * @param $path
     * @param $value
     * @param int $storeId
     * @return Config
     */
    public function setValue($path, $value, int $storeId = 0)
    {
        if (is_string($value)) {
            try {
                $value = \Zend_Json::decode($value);
            } catch (\Exception $e) {
            }
        }

        if (!isset($this->config[$storeId])) {
            $this->config[$storeId] = [];
        }
        $this->config[$storeId][$path] = $value;
        return $this;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return Config
     */
    public function deleteValue($path, int $storeId = 0)
    {
        if (isset($this->config[$storeId])) {
            unset($this->config[$storeId][$path]);
        }

        return $this;
    }

    /**
     * @param $path
     * @param $value
     * @param int $storeId
     * @return Config
     * @throws LocalizedException
     */
    public function save($path, $value, int $storeId = 0)
    {
        if (!is_scalar($value)) {
            $value = \Zend_Json::encode($value);
        }
        $this->configResource->saveConfig($path, $value, $storeId);
        $this->setValue($path, $value, $storeId);
        return $this;
    }

    /**
     * @param $path
     * @param int $storeId
     * @return $this
     * @throws LocalizedException
     */
    public function delete($path, int $storeId = 0)
    {
        $this->configResource->deleteConfig($path, $storeId);
        $this->deleteValue($path, $storeId);
        return $this;
    }
}
