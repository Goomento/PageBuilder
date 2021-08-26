<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\Content;

use Goomento\PageBuilder\Model\Content;
use Goomento\PageBuilder\Model\ResourceModel\Content\Collection;
use Goomento\PageBuilder\Model\ResourceModel\Content\CollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

/**
 * Class ContentDataProvider
 * @package Goomento\PageBuilder\Model\Content
 */
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
     * @var AuthorizationInterface
     */
    private $auth;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $contentCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     * @param AuthorizationInterface|null $auth
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $contentCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null,
        ?AuthorizationInterface $auth = null
    ) {
        $this->collection = $contentCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->auth = $auth ?? ObjectManager::getInstance()->get(AuthorizationInterface::class);
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
        if (is_null($this->loadedData)) {
            $this->loadedData = [];
            $items = $this->collection->getItems();
            /** @var $content Content */
            foreach ($items as $content) {
                $this->loadedData[$content->getId()] = $content->getData();
                $this->loadedData[$content->getId()]['content_data'] =
                    $content->getElements() ? base64_encode(json_encode($content->getElements())) : '';
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
