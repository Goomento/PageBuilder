<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;


use Goomento\Core\Traits\TraitStaticInstances;

/**
 * Class StaticObjectManager
 * @package Goomento\PageBuilder\Helper
 */
class StaticObjectManager
{
    use TraitStaticInstances;

    /**
     * Retrieve cached object instance
     *
     * @param $type
     * @return mixed $type
     */
    public static function get($type)
    {
        return self::getObjectManager()
            ->get($type);
    }

    /**
     * Create new object instance
     *
     * @param $type
     * @param array $arguments
     * @return mixed
     */
    public static function create($type, array $arguments = [])
    {
        return self::getObjectManager()
            ->create($type, $arguments);
    }
}
