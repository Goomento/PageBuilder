<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\User\Model\UserFactory;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
trait TraitBuildableModel
{
    /**
     * Flag storage
     *
     * @var array
     */
    protected $flags = [];

    /**
     * @var DateTime|null
     */
    protected $dateTime;

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
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }


    /**
     * @inheridoc
     */
    public function setCreationTime(string $creationTime) : BuildableContentInterface
    {
        return $this->setData(self::CREATION_TIME, $creationTime);
    }

    /**
     * @inheridoc
     */
    public function getElements(bool $forDisplay = false) : array
    {
        return (array) $this->getData(self::ELEMENTS);
    }

    /**
     * @return $this
     */
    public function setFlag(string $key, $value)
    {
        $this->flags[$key] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFlag(string $key)
    {
        return $this->flags[$key] ?? null;
    }

    /**
     * @return bool
     */
    public function hasFlag(string $key)
    {
        return array_key_exists($key, $this->flags);
    }

    /**
     * @return $this
     */
    public function removeFlag(string $key)
    {
        if ($this->hasFlag($key)) {
            unset($this->flags[$key]);
        }
        return $this;
    }

    /**
     * @inheridoc
     */
    public function getSettings() : array
    {
        return (array) $this->getData(self::SETTINGS);
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
     * @inheritDoc
     */
    public function getAuthor()
    {
        if ($this->author === null && $this->getAuthorId()) {
            $this->author = false;
            /** @var UserFactory $userFactory */
            $userFactory = ObjectManagerHelper::get(UserFactory::class);
            $this->author = $userFactory->create()->load(
                $this->getAuthorId()
            );
        }

        return $this->author && $this->author->getId() ? $this->author : null;
    }

    /**
     * @inheridoc
     */
    public function setElements(array $elements) : BuildableContentInterface
    {
        return $this->setData(self::ELEMENTS, $elements);
    }

    /**
     * @inheridoc
     */
    public function setSettings(array $settings) : BuildableContentInterface
    {
        return $this->setData(self::SETTINGS, $settings);
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
    public function setStatus(string $status) : BuildableContentInterface
    {
        $statuses = self::getAvailableStatuses();
        if (!isset($statuses[$status])) {
            throw new LocalizedException(
                __('Invalid status %1', $status)
            );
        }
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheridoc
     */
    public function hasSetting($name) : bool
    {
        $settings = $this->getSettings();
        return array_key_exists($name, $settings);
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
    public function setSetting($name, $value) : BuildableContentInterface
    {
        $settings = $this->getSettings();
        DataHelper::arraySetValue($settings, $name, $value);
        return $this->setSettings($settings);
    }

    /**
     * @inheridoc
     */
    public function deleteSetting(string $name) : BuildableContentInterface
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
    public function getRevisionHash(): string
    {
        return (string) $this->getData(self::REVISION_HASH);
    }

    /**
     * @inheritDoc
     */
    public function setRevisionHash(string $hash) : BuildableContentInterface
    {
        return $this->setData(self::REVISION_HASH, $hash);
    }

    /**
     * @inheritDoc
     */
    public function getUniqueIdentity(): string
    {
        return EncryptorHelper::uniqueStringId(implode('_', $this->getIdentities()));
    }

    /**
     * @inheritDoc
     */
    public function getElementDataById(string $elementId): array
    {
        return (array) self::findElementById($elementId, $this->getElements());
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
    public function beforeSave()
    {
        if (empty($this->getRevisionHash())) {
            throw new LocalizedException(
                __('Wrong content saving method.')
            );
        }

        if ($this->dateTime === null) {
            $this->dateTime = ObjectManager::getInstance()->get(DateTime::class);
        }

        if (!$this->getCreationTime()) {
            $this->setCreationTime($this->dateTime->gmtDate());
        }

        $this->setUpdateTime($this->dateTime->gmtDate());

        if (!$this->getSetting('layout') && $this->getData('type')) {
            switch ($this->getData('type')) {
                case ContentInterface::TYPE_SECTION:
                    $this->setSetting('layout', 'pagebuilder_content_empty');
                    break;
                case ContentInterface::TYPE_TEMPLATE:
                    $this->setSetting('layout', 'pagebuilder_content_fullwidth');
                    break;
                case ContentInterface::TYPE_PAGE:
                default:
                    $this->setSetting('layout', 'pagebuilder_content_1column');
                    break;
            }
        }

        return parent::beforeSave();
    }
}
