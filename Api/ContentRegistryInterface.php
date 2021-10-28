<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api;

interface ContentRegistryInterface
{
    /**
     * Flexible get content by id
     * Return null if content not found
     *
     * @param int $contentId Content Id
     * @return Data\ContentInterface|null
     */
    public function getById(int $contentId);

    /**
     * Flexible get content by identifier
     * Return null if content not found
     *
     * @param string $identifier
     * @return Data\ContentInterface|null
     */
    public function getByIdentifier(string $identifier);

    /**
     * Clean content cache
     *
     * @param Data\ContentInterface|int $content
     */
    public function invalidateContent($content);
}
