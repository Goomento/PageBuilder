<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class PageList
 * @package Goomento\PageBuilder\Model\Config\Source
 */
class PageList implements OptionSourceInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * PageList constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('type', [
                'in' => [ContentInterface::TYPE_SECTION, ContentInterface::TYPE_PAGE]
        ]);
        $collection->addFieldToFilter('status', ['eq' => ContentInterface::STATUS_PUBLISHED]);
        $results = [];
        foreach ($collection->getItems() as $content) {
            $results[] = [
                'value' => $content->getId(),
                'label' => $this->getLabel($content),
            ];
        }

        return $results;
    }

    /**
     * @param $content
     * @return string
     */
    private function getLabel($content)
    {
        return ucfirst($content->getType()) . ' ' . $content->getTitle() . ' ( ID: ' . $content->getId() . ' )';
    }
}
