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

class PageList implements OptionSourceInterface
{
    /**
     * @var array
     */
    private $options;
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
        if (null === $this->options) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('type', [
                'in' => [ContentInterface::TYPE_SECTION, ContentInterface::TYPE_PAGE]
            ]);
            $collection->addFieldToFilter('status', ['eq' => ContentInterface::STATUS_PUBLISHED]);
            $this->options = [];
            /** @var ContentInterface $content */
            foreach ($collection->getItems() as $content) {
                $this->options[] = [
                    'value' => $content->getIdentifier(),
                    'label' => $this->getLabel($content),
                ];
            }
        }

        return $this->options;
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
