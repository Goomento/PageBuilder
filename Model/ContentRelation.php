<?php

namespace Goomento\PageBuilder\Model;

use Exception;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Backend\Model\Url;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Adapter\AdapterInterface;

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
     * @var AdapterInterface
     */
    private $connection;
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
     * @param $relationType
     * @return string[]
     * @throws Exception
     */
    public function getRelationData($relationType)
    {
        if (isset(self::mapping()[$relationType])) {
            return self::mapping()[$relationType];
        }

        throw new Exception(
            __('Invalid relation type: %1', $relationType)
        );
    }

    /**
     * @param $relationType
     * @return mixed
     * @throws Exception
     */
    public function getRepositoryByType($relationType)
    {
        return StaticObjectManager::get(
            $this->getRelationData($relationType)['repository']
        );
    }


    /**
     * @param $relationType
     * @param $relationId
     * @return array
     * @throws Exception
     */
    public function getRelation($relationType, $relationId)
    {
        $repository = $this->getRepositoryByType($relationType);
        /** @var DataObject $object */
        $object = $repository->getById($relationId);
        return [
            self::FIELD_PAGEBUILDER_CONTENT_ID => $object->getData(self::FIELD_PAGEBUILDER_CONTENT_ID),
            self::FIELD_PAGEBUILDER_IS_ACTIVE => $object->getData(self::FIELD_PAGEBUILDER_IS_ACTIVE),
        ];
    }

    /**
     * @param $contentId
     * @param $relationType
     * @param $relationId
     * @param int $isActive
     * @throws Exception
     */
    public function setRelation($contentId, $relationType, $relationId, int $isActive = 0)
    {
        $repository = $this->getRepositoryByType($relationType);
        /** @var DataObject $object */
        $object = $repository->getById($relationId);
        $object->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, $contentId);
        $object->setData(self::FIELD_PAGEBUILDER_IS_ACTIVE, $isActive);
        $repository->save($object);
    }

    /**
     * @param $relationType
     * @param $relationId
     * @throws Exception
     */
    public function removeRelation($relationType, $relationId)
    {
        $repository = $this->getRepositoryByType($relationType);
        /** @var DataObject $object */
        $object = $repository->getById($relationId);
        $object->setData(self::FIELD_PAGEBUILDER_CONTENT_ID, null);
        $object->setData(self::FIELD_PAGEBUILDER_IS_ACTIVE, 0);
        $repository->save($object);
    }

    /**
     * Get Editable URL
     * @param $relationType
     * @param $relationId
     * @return string|null
     * @throws Exception
     */
    public function getRelationEditableUrl($relationType, $relationId)
    {
        $this->getRelationData($relationType);
        if ($relationType === self::TYPE_CMS_PAGE) {
            return $this->url->getUrl('cms/page/edit', [
                'page_id' => $relationId
            ]);
        }

        if ($relationType === self::TYPE_CMS_BLOCK) {
            return $this->url->getUrl('cms/block/edit', [
                'block_id' => $relationId
            ]);
        }

        return '';
    }
}
