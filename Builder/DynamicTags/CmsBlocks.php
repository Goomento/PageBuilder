<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\DynamicTags;

use Goomento\PageBuilder\Builder\Base\AbstractDataTag;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Tags;
use Magento\Framework\App\ObjectManager;

class CmsBlocks extends AbstractDataTag
{
    const NAME = 'cms_block';

    const IDENTIFIER = 'identifier';

    /**
     * @var \Magento\Framework\View\Element\AbstractBlock
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
        $this->addControl(
            self::IDENTIFIER,
            [
                'label' => __( 'Block Identifier'),
                'type' => Controls::TEXT,
                'default' => '',
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getValue(array $options = [])
    {
        $identifier = (string) $this->getSettings(self::IDENTIFIER);
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
            /** @var \Magento\Cms\Block\Block renderer */
            $this->renderer = ObjectManager::getInstance()->create(\Magento\Cms\Block\Block::class);
        }

        return $this->renderer
            ->setBlockId($identifier)
            ->toHtml();
    }
}
