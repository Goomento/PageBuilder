<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Model\BetterCaching;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderBackend;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderFrontend;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static resolve(string $key, callable $source = false, $tags = BetterCaching::FRONTEND_CACHE_TAG, ?int $timeout = BetterCaching::DEFAULT_TIMEOUT);
 * @see BetterCaching::resolve()
 * @method static save($data, $identifier, $tags = self::FRONTEND_CACHE_TAG, $lifeTime = null);
 * @see BetterCaching::save()
 * @method static remove(string $identifier);
 * @see BetterCaching::remove()
 * @method static clean($tags);
 * @see BetterCaching::clean()
 * @method static load($identifier);
 * @see BetterCaching::load()
 * @method static createKey();
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class CacheHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    const FRONTEND_CACHE_TAG = PageBuilderFrontend::CACHE_TAG;

    const BACKEND_CACHE_TAG = PageBuilderBackend::CACHE_TAG;

    const DAY_LIFE_TIME = 86400; // 01 day

    const HOUR_LIFE_TIME = 3600; // 01 hour

    /**
     * @inheritDoc
     */
    protected static function getStaticInstance()
    {
        return BetterCaching::class;
    }
}
