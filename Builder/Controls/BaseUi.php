<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

/**
 * Class BaseUi
 * @package Goomento\PageBuilder\Builder\Controls
 */
abstract class BaseUi extends Base
{

    /**
     * Get features.
     *
     * Retrieve the list of all the available features.
     *
     *
     * @return array Features array.
     */
    public static function getFeatures()
    {
        return [ 'ui' ];
    }
}
