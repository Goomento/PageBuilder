<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Block\Content;
use Goomento\PageBuilder\Builder\Base\AbstractTag;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Goomento\PageBuilder\Builder\Widgets\PageBuilder;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class PageBuilderContents extends AbstractTag
{
    /**
     * @inheritDoc
     */
    const NAME = 'pagebuilder_content';

    /**
     * @var Content
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
        return (string) __('Page Builder Content');
    }

    /**
     * @inheritDoc
     */
    protected function registerControls()
    {
        PageBuilder::registerPageBuilderContentInterface($this, static::NAME . '_');
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
            $this->renderer = ObjectManagerHelper::get(Content::class);
        }

        return $this->renderer
            ->setIdentifier($identifier)
            ->toHtml();
    }
}
