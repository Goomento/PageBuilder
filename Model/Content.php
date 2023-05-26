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
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Traits\TraitBuildableModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class Content extends AbstractModel implements ContentInterface, IdentityInterface
{
    use TraitBuildableModel;

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
     * @var RevisionInterface|null
     */
    private $lastVersion;

    const CSS = 'css';

    /**
     * @var BuildableContentInterface|null
     */
    private $currentRevision;

    /**
     * @inheridoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Content::class);
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses() : array
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
    public function getTitle() : string
    {
        return (string) $this->getData(self::TITLE);
    }

    /**
     * @inheridoc
     */
    public function setTitle(string $title)
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
    public function getLastRevision($forceLoad = false): ?BuildableContentInterface
    {
        if (!$this->lastVersion && $forceLoad === true && $this->getId()) {
            /** @var RevisionRepositoryInterface $revisionRepo */
            $revisionRepo = ObjectManagerHelper::get(RevisionRepositoryInterface::class);
            try {
                $this->lastVersion = $revisionRepo->getLastRevisionByContentId((int) $this->getId());
            } catch (\Exception $e) {

            }
        }

        return $this->lastVersion ?: null;
    }

    /**
     * @inheritDoc
     */
    public function setLastRevision(BuildableContentInterface $content): BuildableContentInterface
    {
        $this->lastVersion = $content;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCurrentRevision($forceLoad = false): ?BuildableContentInterface
    {
        if (!$this->currentRevision && $forceLoad === true && $this->getRevisionHash()) {
            /** @var RevisionRepositoryInterface $revisionRepo */
            $revisionRepo = ObjectManagerHelper::get(RevisionRepositoryInterface::class);
            try {
                $this->currentRevision = $revisionRepo->getByRevisionHash($this->getRevisionHash());
            } catch (\Exception $e) {

            }
        }

        return $this->currentRevision;
    }

    /**
     * @inheritDoc
     */
    public function setCurrentRevision(BuildableContentInterface $content): BuildableContentInterface
    {
        $this->currentRevision = $content;
        return $this;
    }
}
