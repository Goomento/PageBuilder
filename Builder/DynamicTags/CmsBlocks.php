<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Builder\Base\AbstractTag;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Widgets\Magento\CmsBlock;
use Magento\Cms\Block\Block;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;

class CmsBlocks extends AbstractTag
{
    const NAME = 'cms_block';

    /**
     * @var AbstractBlock
     */
    protected $renderer;

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
        return (string) __('CMS Block Content');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        CmsBlock::registerCmsBlockWidgetInterface($this, self::NAME . '_');
    }

    /**
     * @inheritDoc
     */
    protected function render()
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
            /** @var Block renderer */
            $this->renderer = ObjectManager::getInstance()->create(Block::class);
        }

        return $this->renderer
            ->setBlockId($identifier)
            ->toHtml();
    }
}
