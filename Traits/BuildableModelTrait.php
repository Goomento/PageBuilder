<?php
/**
 * @package Goomento_Core
 * @link https://github.com/Goomento/Core
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Traits;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Magento\User\Model\UserFactory;

trait BuildableModelTrait
{
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
    public function getElements() : array
    {
        return (array) $this->getData(self::ELEMENTS);
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
            try {
                $this->author = $userFactory->create()->load(
                    $this->getAuthorId()
                );
            } catch (\Exception $e) {
            }
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
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheridoc
     */
    public function hasSetting($name) : bool
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
        return md5(\Zend_Json::encode($this->getElements()));
    }


    /**
     * @inheritDoc
     */
    public function getUniqueIdentity(): string
    {
        return implode('_', $this->getIdentities());
    }

    /**
     * @inheritDoc
     */
    public function setRenderContent(string $content): BuildableContentInterface
    {
        $this->contentHtml = $content;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRenderContent(): string
    {
        return $this->contentHtml;
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

}
