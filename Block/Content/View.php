<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Content;

/**
 * This is using for viewing last reversion of content
 */
class View extends Preview
{
    /**
     * @inheritdoc
     */
    public function loadCurrentBuildableContent()
    {
        $content = parent::loadCurrentBuildableContent();
        if ($content && $content->getId()) {
            $content = $content->getLastRevision(true) ?: $content;
        }
        return $content;
    }
}
