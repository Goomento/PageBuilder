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
     * Return null if content not found
     *
     * @param int $contentId Content Id
     * @return Data\ContentInterface|null
     */
    public function getById(int $contentId);

    /**
     * Get content by identifier
     * Return null if content not found
     *
     * @param string $identifier
     * @return Data\ContentInterface|null
     */
    public function getByIdentifier(string $identifier);
}
