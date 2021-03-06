<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

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
    const CREATION_TIME            = 'creation_time';
    const UPDATE_TIME              = 'update_time';

    /**
     * Get elements
     *
     * @return array
     */
    public function getElements() : array;

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
    public function getSetting( $name );

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
    public function hasSetting( $name ) : bool;

    /**
     * Settings that's not allow to save
     *
     * @return array
     */
    public static function getInlineSettingKeys() : array;

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
     * Set renderer content
     *
     * @param string $content
     * @return BuildableContentInterface
     */
    public function setRenderContent(string $content) : BuildableContentInterface;

    /**
     * Get renderer content
     *
     * @return string
     */
    public function getRenderContent() : string;

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
     * @return BuildableContentInterface
     */
    public function getLastRevision() : ?BuildableContentInterface;

    /**
     * @param BuildableContentInterface $content
     * @return BuildableContentInterface|null
     */
    public function setLastRevision(BuildableContentInterface $content) : BuildableContentInterface;

    /**
     * Get unique hash of the current version
     *
     * @return string
     */
    public function getRevisionHash() : string;

    /**
     * Find element by id
     *
     * @param string $elementId
     * @return array|null
     */
    public function getElementDataById(string $elementId) : array;
}
