<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Magento\Backend\Model\Url;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;

class ContentRelation
{
    const TYPE_CMS_PAGE = 'cms_page';
    const TYPE_CMS_BLOCK = 'cms_block';
    const FIELD_PAGEBUILDER_CONTENT_ID = 'pagebuilder_content_id';
    const FIELD_PAGEBUILDER_IS_ACTIVE = 'pagebuilder_is_active';

    /**
     * @var Url
     */
    private $url;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var int
     */
    private $storeId;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Url $url
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Url $url
    )
    {
        $this->url = $url;
        $this->objectManager = $objectManager;
    }

    /**
     * @return string[][]
     */
    public static function mapping()
    {
        return [
            self::TYPE_CMS_PAGE => [
                'repository' => PageRepositoryInterface::class,
                'label' => 'Cms Page',
                'pagebuilder_type' => 'page',
            ],
            self::TYPE_CMS_BLOCK => [
                'repository' => BlockRepositoryInterface::class,
                'label' => 'Cms Block',
                'pagebuilder_type' => 'section',
            ],
        ];
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return (int) $this->storeId;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function setStoreId(int $storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * @param $entityType
     * @return string[]
     * @throws Exception
     */
    public function getRelationData($entityType)
    {
        if (isset(self::mapping()[$entityType])) {
            return self::mapping()[$entityType];
        }

        throw new Exception(
            sprintf('Invalid relation type: %s', $entityType)
        );
    }

    /**
     * @param string $entityType
     * @param int $entityId
     * @return AbstractModel
     * @throws Exception
     */
    public function getEntityObject(string $entityType, int $entityId)
    {
        $repository = $this->getRepositoryByType($entityType);
        return $repository->getById($entityId);
    }

    /**
     * @param $entityType
     * @return mixed
     * @throws Exception
     */
    public function getRepositoryByType($entityType)
    {
        return $this->objectManager->get(
            $this->getRelationData($entityType)['repository']
        );
    }

    /**
     * @param string $entityType
     * @param $relationObject
     * @param array $contentData
     * @return array
     * @throws Exception
     */
    public function prepareContent(string $entityType, $relationObject, array $contentData)
    {
        $relationData = $this->getRelationData($entityType);
        switch ($entityType) {
            case self::TYPE_CMS_PAGE:
            case self::TYPE_CMS_BLOCK:
                $contentData = [
                    'title' => $relationData['label'] . ': #' . $relationObject->getId() . ' ' . $relationObject->getTitle(),
                    'type' => $relationData['pagebuilder_type'],
                    'status' => ContentInterface::STATUS_PUBLISHED,
                ];
                if ($relationObject->getStores()) {
                    $contentData['store_id'] = $relationObject->getStores();
                } else {
                    $contentData['store_id'] = [0];
                }
                $contentData['identifier'] = $relationObject->getIdentifier() . '-' . EncryptorHelper::uniqueString();
                break;
            default:
        }
        return $contentData;
    }


    /**
     * @param $entityType
     * @param $entityId
     * @return array
     * @throws Exception
     */
    public function getRelation($entityType, $entityId)
    {
        $entity = $this->getEntityObject($entityType, $entityId);
        return $this->getRelationByEntity($entityType, $entity);
    }

    /**
     * @param $entityType
     * @param $entity
     * @return array
     * @throws Exception
     */
    public function getRelationByEntity($entityType, $entity)
    {
        return [
            self::FIELD_PAGEBUILDER_CONTENT_ID => (int) $entity->getData(self::FIELD_PAGEBUILDER_CONTENT_ID),
            self::FIELD_PAGEBUILDER_IS_ACTIVE => (bool) $entity->getData(self::FIELD_PAGEBUILDER_IS_ACTIVE),
        ];
    }

    /**
     * @param $contentId
     * @param $entityType
     * @param $entityId
     * @param int $isActive
     * @param array $arguments
     * @throws Exception
     */
    public function setRelation($contentId, $entityType, $entityId, int $isActive = 0, array $arguments = [])
    {
        switch ($entityType) {
            case self::TYPE_CMS_BLOCK:
            case self::TYPE_CMS_PAGE:
                $entity = $this->getEntityObject($entityType, $entityId);
                $entity->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, $contentId);
                $entity->setData(self::FIELD_PAGEBUILDER_IS_ACTIVE, $isActive);
                $repository = $this->getRepositoryByType($entityType);
                $repository->save($entity);
                break;
        }
    }

    /**
     * @param $entityType
     * @param $entityId
     * @throws Exception
     */
    public function removeRelation($entityType, $entityId)
    {
        $repository = $this->getRepositoryByType($entityType);
        /** @var DataObject $object */
        $object = $this->getEntityObject($entityType, $entityId);
        $object->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, 0);
        $object->setData(self::FIELD_PAGEBUILDER_IS_ACTIVE, 0);
        $repository->save($object);
    }

    /**
     * Get Editable URL
     * @param $entityType
     * @param $entityId
     * @return string|null
     * @throws Exception
     */
    public function getEntityEditableUrl($entityType, $entityId)
    {
        $this->getRelationData($entityType);
        if ($entityType === self::TYPE_CMS_PAGE) {
            return $this->url->getUrl('cms/page/edit', [
                'page_id' => $entityId
            ]);
        }

        if ($entityType === self::TYPE_CMS_BLOCK) {
            return $this->url->getUrl('cms/block/edit', [
                'block_id' => $entityId
            ]);
        }

        return '';
    }
}
