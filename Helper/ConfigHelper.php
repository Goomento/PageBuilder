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
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @see Config::getValue()
 * @method static getValue($path, $storeId = 0)
 * @method static getCustomCss($storeId = 0) Get Custom CSS
 * @method static setValue($path, $value, $storeId = 0)
 * @method static deleteValue($path, $storeId = 0)
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class ConfigHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;

    /**
     * @inheritDoc
     */
    protected static function getStaticInstance()
    {
        return Config::class;
    }
}
