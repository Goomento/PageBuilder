<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ItemProvider;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Model\ResourceModel\Content\Collection;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

class ContentDataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $contentCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contentCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        $this->collection = $contentCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if ($this->loadedData === null) {
            $this->loadedData = [];
            $items = $this->collection->getItems();
            /** @var $content ContentInterface */
            foreach ($items as $content) {
                $this->loadedData[$content->getId()] = $content->getData();
                $this->loadedData[$content->getId()]['content_data'] =
                    $content->getElements() ? base64_encode(
                        DataHelper::encode($content->getElements())
                    ) : '';

                // Remove this for save the POST expense
                unset($this->loadedData[$content->getId()][ContentInterface::ELEMENTS]);
                unset($this->loadedData[$content->getId()][ContentInterface::SETTINGS]);
            }

            $data = $this->dataPersistor->get('pagebuilder_content');

            if (!empty($data)) {
                $page = $this->collection->getNewEmptyItem();
                $page->setData($data);
                $this->loadedData[$page->getId()] = $page->getData();
                $this->dataPersistor->clear('pagebuilder_content');
            }
        }

        return $this->loadedData;
    }
}
