<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\Core\Traits\TraitStaticCaller;
use Goomento\Core\Traits\TraitStaticInstances;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\LayoutInterface;

/**
 *
 * NOTE: Use these static methods in template hook only - which wrapped in HooksHelper::doAction( 'header' ) or
 * HooksHelper::doAction( 'footer' ) ... . Otherwise might cause some issues with classes loader.
 * See https://developer.adobe.com/commerce/php/development/components/object-manager/#usage-rules
 *
 * @see LayoutInterface::createBlock()
 * @method static BlockInterface createBlock($type, $name = '', array $arguments = [])
 * @method static BlockInterface|false getBlock(string $name)
 */
// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class LayoutHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;
    /**
     * @inheriDoc
     */
    protected static function getStaticInstance()
    {
        return LayoutInterface::class;
    }
}
