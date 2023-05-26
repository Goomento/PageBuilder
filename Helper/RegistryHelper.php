<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Goomento\Core\Model\Registry;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @method static registry($key);
 * @see \Goomento\Core\Model\Registry::registry()
 * @method static register($key, $value, $graceful = false);
 * @see \Goomento\Core\Model\Registry::register()
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class RegistryHelper
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @inheritDoc
     */
    private static function getStaticInstance()
    {
        return Registry::class;
    }
}
