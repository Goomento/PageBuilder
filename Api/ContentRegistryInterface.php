<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

/**
 * Interface ContentRegistryInterface
 * @package Goomento\PageBuilder\Api
 */
interface ContentRegistryInterface
{
    /**
     * Get content by id
     * Return Null if content not found
     *
     * @param int $contentId
     * @return Data\ContentInterface|null
     */
    public function get(int $contentId);
}
