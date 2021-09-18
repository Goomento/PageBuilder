<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilder;

class EnablePageBuilderCache implements DataPatchInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function __construct(
        CacheManager $cacheManager
    )
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->cacheManager->setEnabled([PageBuilder::TYPE_IDENTIFIER], true);
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
