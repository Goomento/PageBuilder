<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Model\Cache;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static save($data, $identifier, $tags = [], $lifeTime = null);
 * @method static remove($identifier);
 * @method static clean($tags = []);
 * @method static load($identifier);
 * @method static createKey();
 */
class CacheHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @return Cache
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(Cache::class);
    }
}
