<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Content;

use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Helper\EscaperHelper;

class Published extends Content
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->isValidCurrentContent();
        $content = $this->loadCurrentBuildableContent();
        $this->pageConfig->addBodyClass('gmt-' .
            EscaperHelper::slugify($content->getOriginContent()->getIdentifier()));
        $metaTitle = $content->getMetaTitle();
        $this->pageConfig->getTitle()->set(!empty($metaTitle) ? $metaTitle : $content->getTitle());
        $this->pageConfig->setKeywords($content->getMetaKeywords());
        $this->pageConfig->setDescription($content->getMetaDescription());
        return parent::_prepareLayout();
    }
}
