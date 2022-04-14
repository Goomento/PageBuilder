<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

class Content extends AbstractModel implements
    ContentInterface,
    IdentityInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'pagebuilder_content';

    /**
     * @inheridoc
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @inheridoc
     */
    protected $_eventPrefix = 'pagebuilder_content';

    /**
     * @var User
     */
    protected $author = null;
    /**
     * @var User
     */
    protected $lastEditor = null;
    /**
     * @var object
     */
    protected $userHelper = null;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var StoreInterface[]|[]|null
     */
    protected $stores;

    /**
     * @var RevisionInterface[]
     */
    protected $revisions = [];

    private $revisionFlag = true;

    /**
     * @inheridoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Content::class);
    }

    /**
     * @return StoreManagerInterface
     */
    protected function getStoreManager()
    {
        if (is_null($this->storeManager)) {
            $this->storeManager = ObjectManagerHelper::get(StoreManagerInterface::class);
        }

        return $this->storeManager;
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_PUBLISHED => __('Published'),
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableTypes()
    {
        return [
            self::TYPE_PAGE => __('Page'),
            self::TYPE_TEMPLATE=> __('Template'),
            self::TYPE_SECTION => __('Section'),
        ];
    }

    /**
     * @inheridoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheridoc
     */
    public function getId()
    {
        return parent::getData(self::CONTENT_ID);
    }

    /**
     * @inheridoc
     */
    public function isPublished()
    {
        return $this->getStatus() === self::STATUS_PUBLISHED;
    }

    /**
     * @inheridoc
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * @inheridoc
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * @inheridoc
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * @inheridoc
     */
    public function setId($id)
    {
        return $this->setData(self::CONTENT_ID, $id);
    }

    /**
     * @inheridoc
     */
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheridoc
     */
    public function setCreationTime($creationTime)
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @inheridoc
     */
    public function setUpdateTime($updateTime)
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
    }

    /**
     * @inheridoc
     */
    public function getElements()
    {
        return (array) $this->getData(self::ELEMENTS);
    }

    /**
     * @inheridoc
     */
    public function getSettings()
    {
        return (array) $this->getData(self::SETTINGS);
    }

    /**
     * @inheridoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheridoc
     */
    public function setElements(array $elements)
    {
        return $this->setData(self::ELEMENTS, $elements);
    }

    /**
     * @inheridoc
     */
    public function setSettings(array $settings)
    {
        return $this->setData(self::SETTINGS, $settings);
    }

    /**
     * @inheridoc
     */
    public function setType($type)
    {
        if (!in_array($type, array_keys(self::getAvailableTypes()))) {
            throw new LocalizedException(
                __('Invalid content type: %1', $type)
            );
        }
        return $this->setData(self::TYPE, $type);
    }
    /**
     * @inheridoc
     */
    public function setStoreIds($storeIds)
    {
        $storeIds = (array) $storeIds;
        return $this->setData(self::STORE_IDS, $storeIds);
    }

    /**
     * @inheridoc
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * @inheridoc
     */
    public function getAuthorId()
    {
        return $this->getData(self::AUTHOR_ID);
    }

    /**
     * @inheridoc
     */
    public function setAuthorId($authorId)
    {
        return $this->setData(self::AUTHOR_ID, $authorId);
    }

    /**
     * @inheridoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheridoc
     */
    public function setStatus($status)
    {
        if (!in_array($status, array_keys(self::getAvailableStatuses()))) {
            throw new LocalizedException(
                __('Invalid content status: %1', $status)
            );
        }
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheridoc
     */
    public function hasSetting($name)
    {
        return (bool) $this->getSetting($name);
    }

    /**
     * @inheridoc
     */
    public function getSetting($name)
    {
        $settings = $this->getSettings();
        return DataHelper::arrayGetValue($settings, $name);
    }

    /**
     * @inheridoc
     */
    public function setSetting($name, $value)
    {
        $settings = $this->getSettings();
        DataHelper::arraySetValue($settings, $name, $value);
        return $this->setSettings($settings);
    }

    /**
     * @inheridoc
     */
    public function deleteSetting($name)
    {
        if ($this->hasSetting($name)) {
            $settings = $this->getSettings();
            DataHelper::arrayUnsetValue($settings, $name);
            $this->setSettings($settings);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAuthor()
    {
        if (is_null($this->author) && $this->getAuthorId()) {
            $this->author = false;
            /** @var UserFactory $userFactory */
            $userFactory = ObjectManagerHelper::get(UserFactory::class);
            $this->author = $userFactory->create()->load(
                $this->getAuthorId()
            );
        }

        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function getLastEditorId()
    {
        return $this->getData(self::LAST_EDITOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function getLastEditorUser()
    {
        if (is_null($this->lastEditor) && $this->getLastEditorId()) {
            if ($this->getLastEditorId() === $this->getAuthorId()) {
                return $this->getAuthor();
            } else {
                /** @var UserFactory $userFactory */
                $userFactory = ObjectManagerHelper::get(UserFactory::class);
                $this->lastEditor = $userFactory->create()->load(
                    $this->getLastEditorId()
                );
            }
        }

        return $this->lastEditor;
    }

    /**
     * @inheritDoc
     * @deplacated Use history
     */
    public function setLastEditorId($editorId)
    {
        return $this->setData(self::LAST_EDITOR_ID, $editorId);
    }

    /**
     * @inheritDoc
     */
    public function getRoleName(string $role)
    {
        return sprintf('%s_%s', $this->getType(), $role);
    }

    /**
     * @inheritDoc
     */
    public function getIdentifier()
    {
        return $this->getData(self::IDENTIFIER);
    }

    /**
     * @inheritDoc
     */
    public function setIdentifier($value)
    {
        return $this->setData(self::IDENTIFIER, $value);
    }

    /**
     * @inheritDoc
     */
    public function setRevisionFlag(bool $flag)
    {
        $this->revisionFlag = $flag;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRevisionFlag()
    {
        return (bool) $this->revisionFlag;
    }

    /**
     * @inheritDoc
     */
    public function getElementDataById(string $elementId): ?array
    {
        return self::findElementById($elementId, $this->getElements());
    }

    /**
     * @param string $elementId
     * @param array $elements
     * @return array
     */
    private static function findElementById(string $elementId, array $elements)
    {
        $result = [];
        if (!empty($elements)) {
            foreach ($elements as $element) {
                if ($element['id'] === $elementId) {
                    $result = $element;
                    break;
                }

                $check = self::findElementById($elementId, $element['elements']);
                if (!empty($check)) {
                    $result = $check;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function setIsActive(bool $active)
    {
        return $this->setData(self::IS_ACTIVE, (int) $active);
    }

    /**
     * @inheritDoc
     */
    public function getIsActive(): bool
    {
        return (bool) $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritDoc
     */
    public function setMetaTitle(string $meta)
    {
        return $this->setData(self::META_TITLE, $meta);
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitle(): string
    {
        return (string) $this->getData(self::META_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function setMetaKeywords(string $meta)
    {
        return $this->setData(self::META_KEYWORDS, $meta);
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywords(): string
    {
        return (string) $this->getData(self::META_KEYWORDS);
    }

    /**
     * @inheritDoc
     */
    public function setMetaDescription(string $meta)
    {
        return $this->setData(self::META_DESCRIPTION, $meta);
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescription(): string
    {
        return (string) $this->getData(self::META_DESCRIPTION);
    }
}
