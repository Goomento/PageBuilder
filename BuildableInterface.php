<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

interface BuildableInterface
{
    /**
     * @param array $buildSubject
     */
    public function init(array $buildSubject = []);
}
