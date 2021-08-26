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
 * Class StaticAssets
 * @package Goomento\PageBuilder\Helper
 * @method static getDefaultPlaceholderUrl() string
 * @method static getModulePath($module, $type = '') string
 * @method static pathToUrl($path, $store = null) string
 * @method static mkDirIfNotExisted($folder)
 * @method static writeToFile($fileName, $content)
 * @method static delete($fileName)
 * @method static save($fileName, $content)
 * @method static copy($origin, $destination)
 */
class StaticAssets
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @return Assets
     */
    protected static function getStaticInstance()
    {
        return self::getInstance(Assets::class);
    }
}
