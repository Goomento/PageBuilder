<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Block\Editor;

use Goomento\PageBuilder\Helper\EscaperHelper;

class View extends Preview
{
    /**
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->isValidContent();
        $content = $this->getContent();
        $this->pageConfig->addBodyClass('gmt-' . EscaperHelper::slugify($content->getIdentifier()));
        $metaTitle = $content->getMetaTitle();
        $this->pageConfig->getTitle()->set(!empty($metaTitle) ? $metaTitle : $content->getTitle());
        $this->pageConfig->setKeywords($content->getMetaKeywords());
        $this->pageConfig->setDescription($content->getMetaDescription());
        return parent::_prepareLayout();
    }
}
