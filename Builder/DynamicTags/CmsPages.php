<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Magento\Framework\App\ObjectManager;

class CmsPages extends CmsBlocks
{
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
        $this->addControl(
            self::IDENTIFIER,
            [
                'label' => __('Page URL Key'),
                'type' => Controls::TEXT,
                'default' => '',
            ]
        );
    }

    /**
     * @return string
     */
    protected function getContentHtml(string $identifier)
    {
        if ($this->renderer === null) {
            /** @var \Goomento\PageBuilder\Block\Cms\Page renderer */
            $this->renderer = ObjectManager::getInstance()->create(\Goomento\PageBuilder\Block\Cms\Page::class);
        }

        return $this->renderer
            ->setPageId($identifier)
            ->toHtml();
    }
}
