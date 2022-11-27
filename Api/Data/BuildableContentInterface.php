<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

/**
 * @method mixed getData($key)
 * @method BuildableContentInterface setData($key, $value = null)
 * @method BuildableContentInterface setFlag($key, $value)
 * @method mixed getFlag()
 * @method bool hasFlag()
 * @method BuildableContentInterface removeFlag()
 */
interface BuildableContentInterface
{
    const STATUS                   = 'status';
    const STATUS_PENDING           = 'pending';
    const STATUS_PUBLISHED         = 'publish';
    const STATUS_AUTOSAVE          = 'autosave';
    const TYPE                     = 'type';
    const STATUS_REVISION          = 'revision';
    const ELEMENTS                 = 'elements';
    const SETTINGS                 = 'settings';
    const REVISION_HASH            = 'revision_hash';
    const CREATION_TIME            = 'creation_time';
    const UPDATE_TIME              = 'update_time';

    /**
     * Get elements
     *
     * @param bool $forDisplay Add default config to elements
     * @return array
     */
    public function getElements(bool $forDisplay = false) : array;

    /**
     * Set elements
     *
     * @param array $elements
     * @return BuildableContentInterface
     */
    public function setElements(array $elements) : BuildableContentInterface;

    /**
     * @return string
     */
    public function getUniqueIdentity() : string;

    /**
     * Set settings
     *
     * @return array
     */
    public function getSettings() : array;

    /**
     * Get setting
     * @param $name
     * @return array|string|bool
     */
    public function getSetting($name);

    /**
     * Set setting
     * @param $name
     * @param $value
     * @return BuildableContentInterface
     */
    public function setSetting($name, $value) : BuildableContentInterface;

    /**
     * Set settings
     *
     * @param array $settings
     * @return BuildableContentInterface
     */
    public function setSettings(array $settings) : BuildableContentInterface;


    /**
     * Delete setting
     * @param string $name
     * @return ContentInterface
     */
    public function deleteSetting(string $name) : BuildableContentInterface;

    /**
     * Has setting
     * @param $name
     * @return bool
     */
    public function hasSetting($name) : bool;

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Set ID
     *
     * @param int $id
     * @return BuildableContentInterface
     */
    public function setId($id);

    /**
     * Set type
     *
     * @return string
     */
    public function getType() : string;

    /**
     * Set elements
     *
     * @param string $type
     * @return BuildableContentInterface
     */
    public function setType(string $type) : BuildableContentInterface;

    /**
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return BuildableContentInterface
     */
    public function setStatus(string $status) : BuildableContentInterface;

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return BuildableContentInterface
     */
    public function setCreationTime(string $creationTime) : BuildableContentInterface;

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return BuildableContentInterface
     */
    public function setUpdateTime(string $updateTime) : BuildableContentInterface;

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string
     */
    public function getUpdateTime() : string;

    /**
     * @return BuildableContentInterface
     */
    public function getOriginContent() : BuildableContentInterface;

    /**
     * @param BuildableContentInterface $content
     * @return BuildableContentInterface
     */
    public function setOriginContent(BuildableContentInterface $content) : BuildableContentInterface;

    /**
     * @param bool $forceLoad
     * @return BuildableContentInterface|null
     */
    public function getLastRevision($forceLoad = false) : ?BuildableContentInterface;

    /**
     * @param BuildableContentInterface $content
     * @return BuildableContentInterface|null
     */
    public function setLastRevision(BuildableContentInterface $content) : BuildableContentInterface;

    /**
     * @param bool $forceLoad
     * @return BuildableContentInterface|null
     */
    public function getCurrentRevision($forceLoad = false) : ?BuildableContentInterface;

    /**
     * @param BuildableContentInterface $content
     * @return BuildableContentInterface|null
     */
    public function setCurrentRevision(BuildableContentInterface $content) : BuildableContentInterface;

    /**
     * Get unique hash of the current version
     *
     * @return string
     */
    public function getRevisionHash() : string;

    /**
     * Set unique hash
     *
     * @param string $hash
     * @return BuildableContentInterface
     */
    public function setRevisionHash(string $hash) : BuildableContentInterface;

    /**
     * Find element by id
     *
     * @param string $elementId
     * @return array|null
     */
    public function getElementDataById(string $elementId) : array;

    /**
     * Get allowed statuses
     * @return array
     */
    public static function getAvailableStatuses() : array;
}
