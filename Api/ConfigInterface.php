<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

use Magento\Store\Model\Store;

interface ConfigInterface
{
    const DEFAULT_STORE_ID = Store::DEFAULT_STORE_ID;

    /**
     * @param array|string $path
     * @param int $storeId
     * @return array|string|null
     */
    public function getValue($path, int $storeId = self::DEFAULT_STORE_ID);

    /**
     * @param array|string $path
     * @param null|array|string $value
     * @param int $storeId
     * @return mixed
     */
    public function setValue($path, $value = null, int $storeId = self::DEFAULT_STORE_ID);

    /**
     * @param array|string $path
     * @param int $storeId
     * @return mixed
     */
    public function deleteValue($path, int $storeId = self::DEFAULT_STORE_ID);
}
