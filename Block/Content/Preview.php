<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Content;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Block\Content;

/**
 * This is using for Editor preview during building page
 */
class Preview extends Content
{
    /**
     * @inheritDoc
     */
    protected function checkValidContent(BuildableContentInterface $content) : bool
    {
        return (bool)$content->getId();
    }
}
