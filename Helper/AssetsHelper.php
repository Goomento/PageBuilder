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
 * @method static getDefaultPlaceholderUrl() string
 * @method static string getModulePath($module, $type = '')
 * @method static string pathToUrl($path, $store = null)
 * @method static mkDirIfNotExisted($folder)
 * @method static writeToFile($fileName, $content)
 * @method static delete($fileName)
 * @method static save($fileName, $content)
 * @method static copy($origin, $destination)
 * @method static string[] parsePath($path)
 */
class AssetsHelper
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
