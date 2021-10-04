<?php

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\StaticEncryptor;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Backend\Model\Url;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class ContentRelation
 * @package Goomento\PageBuilder\Model
 */
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
     * @param Url $url
     */
    public function __construct(
        Url $url
    )
    {
        $this->url = $url;
    }

    /**
     * @return \string[][]
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
            __('Invalid relation type: %1', $entityType)
        );
    }

    /**
     * @param string $entityType
     * @param int $entityId
     * @return DataObject
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
        return StaticObjectManager::get(
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
                $contentData['identifier'] = $relationObject->getIdentifier() . '-' . StaticEncryptor::uniqueString();
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
        $repository = $this->getRepositoryByType($entityType);
        /** @var DataObject $object */
        $object = $repository->getById($entityId);
        return [
            self::FIELD_PAGEBUILDER_CONTENT_ID => (int) $object->getData(self::FIELD_PAGEBUILDER_CONTENT_ID),
            self::FIELD_PAGEBUILDER_IS_ACTIVE => (bool) $object->getData(self::FIELD_PAGEBUILDER_IS_ACTIVE),
        ];
    }

    /**
     * @param $contentId
     * @param $entityType
     * @param $entityId
     * @param int $isActive
     * @throws Exception
     */
    public function setRelation($contentId, $entityType, $entityId, int $isActive = 0)
    {
        $repository = $this->getRepositoryByType($entityType);
        /** @var DataObject $object */
        $object = $repository->getById($entityId);
        $object->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, $contentId);
        $object->setData(self::FIELD_PAGEBUILDER_IS_ACTIVE, $isActive);
        $repository->save($object);
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
        $object = $repository->getById($entityId);
        $object->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, null);
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
