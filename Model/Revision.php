<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\UserHelper;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\User\Model\User;
use Magento\User\Model\UserFactory;

/**
 * Class Revision
 * @package Goomento\PageBuilder\Model
 */
class Revision extends AbstractModel implements RevisionInterface, IdentityInterface
{
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
     * @var User
     */
    protected $author = null;

    /**
     * @var object
     */
    protected $userHelper = null;

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
        return [
            self::STATUS_DRAFT => __('Draft'),
            self::STATUS_AUTOSAVE => __('Autosave'),
            self::STATUS_REVISION => __('Revision')
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
        return parent::getData(self::REVISION_ID);
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
    public function setId($id)
    {
        return $this->setData(self::CONTENT_ID, $id);
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
    public function getElements()
    {
        $element = $this->getData(self::ELEMENTS);
        if (!is_array($element)) {
            try {
                $element = json_decode((string) $element, true);
                if (!$element) {
                    $element = [];
                }
            } catch (\Exception $e) {
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
            try {
                $settings = json_decode($settings, true);
                if (!$settings) {
                    $settings = [];
                }
            } catch (\Exception $e) {
                $settings = [];
            }
        }

        return $settings;
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
            try {
                $this->author = $userFactory->create()->load(
                    $this->getAuthorId()
                );
            } catch (\Exception $e) {
            }
        }

        return $this->author;
    }

    /**
     * @inheritDoc
     */
    public function getContentId()
    {
        return $this->getData(self::CONTENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setContentId($contentId)
    {
        return $this->setData(self::CONTENT_ID, $contentId);
    }
}
