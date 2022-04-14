<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Config\Source;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\ContentHelper;
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
            $collection->addFieldToFilter(ContentInterface::TYPE, [
                'in' => [ContentInterface::TYPE_SECTION, ContentInterface::TYPE_PAGE]
            ]);
            $this->options = [];
            /** @var ContentInterface $content */
            foreach ($collection->getItems() as $content) {
                $this->options[] = [
                    'value' => $content->getIdentifier(),
                    'label' => ContentHelper::getContentLabel($content),
                ];
            }
        }

        return $this->options;
    }
}
