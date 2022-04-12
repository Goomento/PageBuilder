<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\Core\Traits\TraitHttpExecutable;
use Magento\Framework\Exception\LocalizedException;

trait TraitHttpGet
{
    use TraitHttpExecutable;

    public function executePost()
    {
        throw new LocalizedException(
            __('This action does\'t allow.')
        );
    }
}
