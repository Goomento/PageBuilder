<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Controls;

use Goomento\PageBuilder\Builder\Base\AbstractControl;

abstract class AbstractControlUi extends AbstractControl
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
