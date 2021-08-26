<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Model\Cache\Type\PageBuilder;
use Magento\Framework\App\Cache\Frontend\Pool;

/**
 * Class Cache
 * @package Goomento\PageBuilder\Helper
 */
class Cache extends \Magento\Framework\App\Cache
{
    const CONFIG_TAG = 'config';
    const TPM_TAG = 'tmp';
    const FRONTEND_TAG = 'frontend';
    const ADMINHTML_TAG = 'adminhtml';
    const CONTENT_COLLECTION_TAG = 'pagebuilder_content';

    const DEFAULT_LIFE_TIME = 3600; // 1 hour

    /**
     * Cache constructor.
     * @param Pool $frontendPool
     */
    public function __construct(
        Pool $frontendPool
    ) {
        parent::__construct($frontendPool, PageBuilder::TYPE_IDENTIFIER);
    }

    /**
     * @param $data
     * @param $identifier
     * @param null $lifeTime
     * @return bool
     */
    public function saveToContentCollection($data, $identifier, $lifeTime = null)
    {
        return $this->save(
            $data,
            $identifier,
            [self::CONTENT_COLLECTION_TAG],
            $lifeTime ?: self::DEFAULT_LIFE_TIME
        );
    }

    /**
     * @return bool
     */
    public function cleanContentCollection()
    {
        return parent::clean([self::DEFAULT_LIFE_TIME]);
    }
}
