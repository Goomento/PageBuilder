<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\User\Model\User;

/**
 * Interface RevisionInterface
 * @package Goomento\PageBuilder\Api\Data
 */
interface RevisionInterface
{
    const REVISION_ID              = 'revision_id';
    const CONTENT_ID               = 'content_id';
    const ELEMENTS                 = 'elements';
    const SETTINGS                 = 'settings';
    const CREATION_TIME            = 'creation_time';
    const AUTHOR_ID                = 'author_id';
    const STATUS                   = 'status';
    const STATUS_PENDING           = ContentInterface::STATUS_PENDING;
    const STATUS_PUBLISHED         = ContentInterface::STATUS_PUBLISHED;
    const STATUS_AUTOSAVE          = ContentInterface::STATUS_AUTOSAVE;
    const STATUS_DRAFT             = ContentInterface::STATUS_DRAFT;
    const STATUS_REVISION          = 'revision';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Content ID
     *
     * @return int|null
     */
    public function getContentId();

    /**
     * Get elements
     *
     * @return array
     */
    public function getElements();

    /**
     * Set settings
     *
     * @return array
     */
    public function getSettings();

    /**
     * Has setting
     * @param $name
     * @return mixed
     */
    public function hasSetting($name);

    /**
     * Has setting
     * @param $name
     * @return mixed
     */
    public function getSetting($name);

    /**
     * Set setting
     * @param $name
     * @param $value
     * @return mixed
     */
    public function setSetting($name, $value);

    /**
     * Get author ID
     *
     * @return int
     */
    public function getAuthorId();

    /**
     * @return User
     */
    public function getAuthor();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Set ID
     *
     * @param int $id
     * @return RevisionInterface
     */
    public function setId($id);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return RevisionInterface
     */
    public function setCreationTime($creationTime);


    /**
     * Set elements
     *
     * @param array|string $elements
     * @return RevisionInterface
     */
    public function setElements($elements);

    /**
     * Set elements
     *
     * @param array $settings
     * @return RevisionInterface
     */
    public function setSettings($settings);

    /**
     * Set author Id
     *
     * @param int $authorId
     * @return RevisionInterface
     */
    public function setAuthorId($authorId);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return RevisionInterface
     */
    public function setStatus($status);

    /**
     * Set status
     *
     * @param int $contentId
     * @return RevisionInterface
     */
    public function setContentId($contentId);
}
