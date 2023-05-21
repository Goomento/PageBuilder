<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\Core\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Builder\Resolver\RequireJsResolver;

class Resolvers
{
    public function __construct()
    {
        ObjectManagerHelper::get(RequireJsResolver::class);
    }
}
