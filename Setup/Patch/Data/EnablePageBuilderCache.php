<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilder;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

class EnablePageBuilderCache implements DataPatchInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var CacheTypeListInterface
     */
    private $cacheTypeList;

    /**
     * @param CacheManager $cacheManager
     * @param CacheTypeListInterface $cacheTypeList
     */
    public function __construct(
        CacheManager $cacheManager,
        CacheTypeListInterface $cacheTypeList
    )
    {
        $this->cacheManager = $cacheManager;
        $this->cacheTypeList = $cacheTypeList;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $isInvalided = in_array(PageBuilder::TYPE_IDENTIFIER, $this->cacheTypeList->getInvalidated());
        if (!$isInvalided) {
            $this->cacheManager->setEnabled([PageBuilder::TYPE_IDENTIFIER], true);
        }
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
