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
 * @method static getDefaultPlaceholderUrl() string
 * @method static string getModulePath($module, $type = '')
 * @method static string pathToUrl($path)
 * @method static mkDirIfNotExisted($folder)
 * @method static writeToFile($fileName, $content)
 * @method static delete($fileName)
 * @method static save($fileName, $content)
 * @method static copy($origin, $destination)
 * @method static string[] parsePath($path)
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class AssetsHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @inheritDoc
     */
    protected static function getStaticInstance()
    {
        return Assets::class;
    }
}
