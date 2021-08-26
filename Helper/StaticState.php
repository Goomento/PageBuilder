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
 * Class StaticState
 * @package Goomento\PageBuilder\Helper
 * @method static string getAreaCode()
 * @method static bool isAdminhtml()
 */
class StaticState
{
    use TraitStaticCaller;
    use TraitStaticInstances;

    /**
     * @inheritdoc
     */
    static protected function getStaticInstance()
    {
        return \Goomento\Core\Helper\State::class;
    }
}
