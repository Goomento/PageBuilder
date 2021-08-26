<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class NestedRepeater
 * @package Goomento\PageBuilder\Builder\Controls
 */
class NestedRepeater extends Repeater
{

    /**
     * Get repeater control type.
     *
     * Retrieve the control type, in this case `repeater`.
     *
     *
     * @return string Control type.
     */
    public function getType()
    {
        return 'nested_repeater';
    }
}
