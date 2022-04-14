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
 * @see Config::getValue()
 * @method static getValue($path, $storeId = 0)
 * @method static getCustomCss($storeId = 0) Get Custom CSS
 * @method static setValue($path, $value, $storeId = 0)
 * @method static deleteValue($path, $storeId = 0)
 */
class ConfigHelper
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
