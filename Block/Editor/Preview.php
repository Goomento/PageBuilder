<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Editor;

use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Helper\StaticContent;

/**
 * Class Preview
 * @package Goomento\PageBuilder\Block\Editor
 */
class Preview extends Content
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
}
