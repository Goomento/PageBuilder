<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Content;

use Goomento\PageBuilder\Block\Content;

class View extends Content
{
    /**
     * @inheritdoc
     */
    protected function isValidContent()
    {
        $contentId = $this->getContentId();
        $contentId = $contentId ?: $this->getRequest()->getParam(self::CONTENT_ID);
        if ($contentId) {
            $this->setContentId((int) $contentId);
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getBuildableContent()
    {
        $content = parent::getBuildableContent();
        return $content->getLastRevision(true) ?: $content;
    }
}
