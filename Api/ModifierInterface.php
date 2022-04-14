<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

interface ModifierInterface
{
    /**
     * Modify the data
     *
     * @param string|array|object $data
     * @return string|array|object
     */
    public function modify($data);
}
