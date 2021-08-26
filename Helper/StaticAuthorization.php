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
 * Class StaticAuthorization
 * @package Goomento\PageBuilder\Helper
 * @method static isAllowed($resource)
 */
class StaticAuthorization
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @return mixed
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(Authorization::class);
    }

    /**
     * @param string $part
     * @return mixed
     */
    public static function isCurrentUserCan(string $part)
    {
        return self::isAllowed("Goomento_PageBuilder::{$part}");
    }
}
