<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Block\Cms\Page;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Widgets\Magento\CmsPage;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class CmsPages extends CmsBlocks
{
    /**
     * @inheritDoc
     */
    const NAME = 'cms_page';

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return [Tags::WYSIWYG_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getGroup()
    {
        return [Tags::WYSIWYG_CATEGORY];
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return (string) __('CMS Page Content');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        CmsPage::registerCmsPageWidgetInterface($this, self::NAME . '_id');
    }

    /**
     * @inheritDoc
     */
    protected function render(array $options = [])
    {
        $identifier = (string) $this->getSettings(self::NAME . '_id');
        if ($identifier = trim($identifier)) {
            return $this->getContentHtml($identifier);
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getContentHtml(string $identifier)
    {
        if ($this->renderer === null) {
            $this->renderer = ObjectManagerHelper::create(Page::class);
        }

        return $this->renderer
            ->setPageId($identifier)
            ->toHtml();
    }
}
