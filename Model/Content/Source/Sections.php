<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Content\Source;

use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Sections implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * Pages constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $result = [];
        $pages = $this->collectionFactory->create()
            ->addFieldToFilter('type', ['eq' => ContentInterface::TYPE_SECTION]);
        foreach ($pages->getItems() as $page) {
            $result[] = [
                'label' => $page->getTitle(),
                'value' => $page->getId(),
            ];
        }

        return $result;
    }
}
