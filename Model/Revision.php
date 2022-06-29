<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Traits\BuildableModelTrait;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\User\Model\User;

class Revision extends AbstractModel implements RevisionInterface, IdentityInterface
{

    use BuildableModelTrait;

    /**
     * Cache tag
     */
    const CACHE_TAG = 'pagebuilder_content_revision';

    /**
     * @inheridoc
     */
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * @inheridoc
     */
    protected $_eventPrefix = 'pagebuilder_content_revision';

    /**
     * @var User|null
     */
    protected $author;

    /**
     * @var ContentInterface
     */
    private $content;

    /**
     * @var string
     */
    private $contentHtml = '';

    /**
     * @inheridoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Revision::class);
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses()
    {
        return array_merge(
            Content::getAvailableStatuses(),
            [
                self::STATUS_AUTOSAVE => __('Autosave'),
                self::STATUS_REVISION => __('Revision')
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getContentId() : int
    {
        return (int) $this->getData(self::CONTENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setContentId($contentId)
    {
        return $this->setData(self::CONTENT_ID, $contentId);
    }

    /**
     * @inheritDoc
     */
    public function getType() : string
    {
        return self::REVISION;
    }

    /**
     * @inheritDoc
     */
    public function setUpdateTime(string $updateTime) : BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getUpdateTime() : string
    {
        return (string) $this->getData(self::CREATION_TIME);
    }

    /**
     * @inheritDoc
     */
    public function setType(string $type) : BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOriginContent(): BuildableContentInterface
    {
        if (!$this->content) {
            $this->content = ObjectManager::getInstance()->get(ContentRegistryInterface::class)->getById(
                $this->getContentId()
            );
        }

        return $this->content;
    }

    /**
     * @inheritDoc
     */
    public function setOriginContent(BuildableContentInterface $content): BuildableContentInterface
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getLastRevision(): ?BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setLastRevision(BuildableContentInterface $content): BuildableContentInterface
    {
        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function getInlineSettingKeys(): array
    {
        return [
            'id',
            self::STATUS,
            self::CONTENT_ID,
            self::REVISION_ID,
            ContentInterface::IS_ACTIVE,
        ];
    }
}
