<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;

/**
 * Class StaticCache
 * @package Goomento\PageBuilder\Helper
 * @method static save($data, $identifier, $tags = [], $lifeTime = null);
 * @method static remove($identifier);
 * @method static clean($tags = []);
 * @method static load($identifier);
 * @method static createKey();
 */
class StaticCache
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @var bool|null
     */
    private static $isValidCache = null;

    /**
     * @return Cache
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(Cache::class);
    }
}
