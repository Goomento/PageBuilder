<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\UserHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

/**
 * Class Content
 * @package Goomento\PageBuilder\Model
 */
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
            $this->storeManager = StaticObjectManager::get(StoreManagerInterface::class);
        }

        return $this->storeManager;
    }

    /**
     * @inheridoc
     */
    public function getStores()
    {
        if (is_null($this->stores)) {
            $storeIds = $this->getStoreId();
            foreach ($storeIds as $storeId) {
                $this->stores[] = $this->getStoreManager()->getStore($storeId);
            }
        }

        return $this->stores;
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
        return (int) parent::getData(self::CONTENT_ID);
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
    public function getContent()
    {
        return $this->getData(self::CONTENT);
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
    public function setContent($content)
    {
        return $this->setData(self::CONTENT, $content);
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
        $element = $this->getData(self::ELEMENTS);
        if (!is_array($element)) {
            $element = json_decode((string) $element, true);
            if (!$element) {
                $element = [];
            }
        }

        return $element;
    }

    /**
     * @inheridoc
     */
    public function getSettings()
    {
        $settings = $this->getData(self::SETTINGS);
        if ($settings && !is_array($settings)) {
            $settings = json_decode($settings, true);
            if (!$settings) {
                $settings = [];
            }
        }

        return $settings;
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
    public function setElements($elements)
    {
        if (is_array($elements)) {
            $elements = json_encode($elements);
        }
        return $this->setData(self::ELEMENTS, $elements);
    }

    /**
     * @inheridoc
     */
    public function setSettings($settings)
    {
        if (is_array($settings)) {
            $settings = json_encode($settings);
        }
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
    public function setStores($stores)
    {
        return $this->setData(self::STORES, $stores);
    }

    /**
     * @inheridoc
     */
    public function setStoreId($storeIds)
    {
        $storeIds = (array) $storeIds;
        return $this->setData(self::STORE_ID, $storeIds);
    }

    /**
     * @inheridoc
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
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
     * @return UserHelper
     */
    protected function getUserHelper()
    {
        if (is_null($this->userHelper)) {
            /** @var UserHelper userHelper */
            $this->userHelper = StaticObjectManager::get(UserHelper::class);
        }

        return $this->userHelper;
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
        return $settings[$name] ?? null;
    }

    /**
     * @inheridoc
     */
    public function setSetting($name, $value)
    {
        $settings = $this->getSettings();
        $settings[$name] = $value;
        return $this->setSettings($settings);
    }

    /**
     * @inheridoc
     */
    public function deleteSetting($name)
    {
        if ($this->hasSetting($name)) {
            $settings = $this->getSettings();
            unset($settings[$name]);
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
            $userFactory = StaticObjectManager::get(UserFactory::class);
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
                $userFactory = StaticObjectManager::get(UserFactory::class);
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
}
