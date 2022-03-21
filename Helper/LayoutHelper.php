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
 * @see LayoutInterface::createBlock()
 * @method static BlockInterface createBlock($type, $name = '', array $arguments = [])
 * @method static BlockInterface|false getBlock(string $name)
 */
class LayoutHelper
{
    use TraitStaticInstances;
    use TraitStaticCaller;
    /**
     * @inheriDoc
     */
    static protected function getStaticInstance()
    {
        return LayoutInterface::class;
    }
}
