<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\PageBuilder\Model\Config;

/**
 * Class StaticConfig
 * @package Goomento\PageBuilder\Helper
 * @method static getOption($name, $default = null, $storeId = 0)
 * @method static setOption($path, $storeId = 0)
 * @method static delOption($path, $storeId = 0)
 * @method static getValue($path, $storeId = 0)
 * @method static setValue($path, $value, $storeId = 0)
 * @method static deleteValue($path, $value, $storeId = 0)
 */
class StaticConfig
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @inheritDoc
     */
    static protected function getStaticInstance()
    {
        return Config::class;
    }
}
