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
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static isAllowed($resource)
 */
class AuthorizationHelper
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
