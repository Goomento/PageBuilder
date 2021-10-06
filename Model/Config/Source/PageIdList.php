<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class PageIdList
 * @package Goomento\PageBuilder\Model\Config\Source
 */
class PageIdList extends AbstractSource
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
     * @param $content
     * @return string
     */
    private function getLabel($content)
    {
        return ucfirst($content->getType()) . ' ' . $content->getTitle() . ' ( ID: ' . $content->getId() . ' )';
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if (null === $this->options) {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('type', [
                'in' => [ContentInterface::TYPE_SECTION, ContentInterface::TYPE_PAGE]
            ]);
            $collection->addFieldToFilter('status', ['eq' => ContentInterface::STATUS_PUBLISHED]);
            $this->options[] = [
                'value' => '',
                'label' => __('-- Select content --'),
            ];
            /** @var ContentInterface $content */
            foreach ($collection->getItems() as $content) {
                $this->options[] = [
                    'value' => $content->getId(),
                    'label' => $this->getLabel($content),
                ];
            }
        }

        return $this->options;
    }
}
