<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Setup\Patch\Data;

use Magento\Framework\App\Cache\StateInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderFrontend;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderBackend;

class EnablePageBuilderCache implements DataPatchInterface
{
    /**
     * @var CacheManager
     */
    private $cacheManager;
    /**
     * @var StateInterface
     */
    private $cacheState;

    /**
     * @param CacheManager $cacheManager
     * @param StateInterface $cacheState
     */
    public function __construct(
        CacheManager $cacheManager,
        StateInterface $cacheState
    ) {
        $this->cacheState = $cacheState;
        $this->cacheManager = $cacheManager;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        if (!$this->cacheState->isEnabled(PageBuilderFrontend::TYPE_IDENTIFIER)) {
            $this->cacheManager->setEnabled([PageBuilderFrontend::TYPE_IDENTIFIER], true);
        }
        if (!$this->cacheState->isEnabled(PageBuilderBackend::TYPE_IDENTIFIER)) {
            $this->cacheManager->setEnabled([PageBuilderBackend::TYPE_IDENTIFIER], true);
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
