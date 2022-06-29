<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Traits\BuildableModelTrait;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

class Content extends AbstractModel implements ContentInterface, IdentityInterface
{
    use BuildableModelTrait;

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
     * @var User|null
     */
    protected $author;
    /**
     * @var User|null
     */
    protected $lastEditor;

    /**
     * @var StoreManagerInterface|null
     */
    protected $storeManager;

    /**
     * @var StoreInterface[]|[]|null
     */
    protected $stores;

    private $revisionFlag;

    /**
     * @var RevisionInterface|null
     */
    private $lastContentVersion;

    /**
     * @var string
     */
    private $contentHtml = '';

    const VERSION = Developer::VERSION;

    const CSS = 'css';

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
    public function isPublished()
    {
        return $this->getStatus() === self::STATUS_PUBLISHED;
    }


    /**
     * @inheridoc
     */
    public function getUpdateTime() : string
    {
        return (string) $this->getData(self::UPDATE_TIME);
    }

    /**
     * @inheridoc
     */
    public function setUpdateTime(string $updateTime) : BuildableContentInterface
    {
        return $this->setData(self::UPDATE_TIME, $updateTime);
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
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * @inheridoc
     */
    public function getType() : string
    {
        return (string) $this->getData(self::TYPE);
    }

    /**
     * @inheridoc
     */
    public function setType(string $type) : BuildableContentInterface
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
     * @inheritDoc
     */
    public function getLastEditorId() : int
    {
        return (int) $this->getData(self::LAST_EDITOR_ID);
    }

    /**
     * @inheritDoc
     */
    public function getLastEditorUser()
    {
        if ($this->lastEditor === null && $this->getLastEditorId()) {
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

    /**
     * @inheritDoc
     */
    public function getOriginContent(): BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOriginContent(BuildableContentInterface $content): BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastRevision(): ?BuildableContentInterface
    {
        if (null === $this->lastContentVersion) {
            $revision = ContentHelper::getLastRevisionByContent($this);
            $this->lastContentVersion = $revision ?: false;
        }

        return $this->lastContentVersion ? $this->lastContentVersion : null;
    }

    /**
     * @inheritDoc
     */
    public function setLastRevision(BuildableContentInterface $content): BuildableContentInterface
    {
        $this->lastContentVersion = $content;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getInlineSettingKeys(): array
    {
        return [
            'id',
            self::TITLE,
            self::STATUS,
            self::CONTENT_ID,
            self::IS_ACTIVE,
        ];
    }

}
