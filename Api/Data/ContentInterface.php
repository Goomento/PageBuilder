<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Api\Data\StoreInterface;
use Magento\User\Model\User;

interface ContentInterface
{
    const CONTENT_ID               = 'content_id';
    const TITLE                    = 'title';
    const TYPE                     = 'type';
    /**
     * @deprecated
     */
    const STORES                   = 'stores';
    const STORE_IDS                = 'store_ids';
    const CONTENT                  = 'content';
    const ELEMENTS                 = 'elements';
    const SETTINGS                 = 'settings';
    const IDENTIFIER               = 'identifier';
    const CREATION_TIME            = 'creation_time';
    const UPDATE_TIME              = 'update_time';
    const AUTHOR_ID                = 'author_id';
    const LAST_EDITOR_ID           = 'last_editor_id';

    const STATUS                   = 'status';
    const STATUS_PENDING           = 'pending';
    const STATUS_PUBLISHED         = 'publish';
    const STATUS_AUTOSAVE          = 'autosave';
    const STATUS_DRAFT             = 'draft';

    const TYPE_PAGE                = 'page';
    const TYPE_TEMPLATE            = 'template';
    const TYPE_SECTION             = 'section';

    const CREATE_REVISION_FLAG     = 'create_revision_flag';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get content
     *
     * @return string|null
     */
    public function getContent();

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
     * @see ContentInterface::getSetting()
     */
    public function getSettings();

    /**
     * Has setting
     * @param $name
     * @return bool
     */
    public function hasSetting($name);

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier();

    /**
     * Set identifier
     * @param $value
     * @return ContentInterface
     */
    public function setIdentifier($value);

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
     * @return ContentInterface
     */
    public function setSetting($name, $value);

    /**
     * Delete setting
     * @param $name
     * @return ContentInterface
     */
    public function deleteSetting($name);

    /**
     * Set type
     *
     * @return string
     */
    public function getType();

    /**
     * Get author ID
     *
     * @return int
     */
    public function getAuthorId();

    /**
     * @return User|false
     */
    public function getAuthor();

    /**
     * Get last editor ID
     *
     * @return int
     */
    public function getLastEditorId();

    /**
     * @return User
     */
    public function getLastEditorUser();

    /**
     * Set stores
     *
     * @return StoreInterface[]|[]
     * @deprecated
     */
    public function getStores();

    /**
     * Set store ids
     *
     * @return int[]|[]
     */
    public function getStoreIds();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();

    /**
     * Set ID
     *
     * @param int $id
     * @return ContentInterface
     */
    public function setId($id);

    /**
     * Set title
     *
     * @param string $title
     * @return ContentInterface
     */
    public function setTitle($title);

    /**
     * Set content
     *
     * @param string $content
     * @return ContentInterface
     */
    public function setContent($content);

    /**
     * Set creation time
     *
     * @param string $creationTime
     * @return ContentInterface
     */
    public function setCreationTime($creationTime);

    /**
     * Set update time
     *
     * @param string $updateTime
     * @return ContentInterface
     */
    public function setUpdateTime($updateTime);

    /**
     * Set elements
     *
     * @param array $elements
     * @return ContentInterface
     */
    public function setElements(array $elements);

    /**
     * Set elements
     *
     * @param array $settings
     * @return ContentInterface
     * @deprecated
     * @see ContentInterface::setSetting()
     */
    public function setSettings(array $settings);

    /**
     * Set elements
     *
     * @param string $type
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function setType($type);

    /**
     * Set elements
     *
     * @param array $stores
     * @return ContentInterface
     */
    public function setStores($stores);

    /**
     * Set store ids
     *
     * @param int[]|int $storeIds
     * @return ContentInterface
     */
    public function setStoreIds($storeIds);

    /**
     * Set author Id
     *
     * @param int $authorId
     * @return ContentInterface
     */
    public function setAuthorId($authorId);

    /**
     * Set last editor Id
     *
     * @param int $editorId
     * @return ContentInterface
     * @deplacated Use history
     */
    public function setLastEditorId($editorId);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function setStatus($status);

    /**
     * @param string $role
     * @return string
     */
    public function getRoleName(string $role);

    /**
     * Allow to create revision for every saving action
     * @param bool $flag
     * @return ContentInterface
     */
    public function setRevisionFlag(bool $flag);

    /**
     * @return bool
     */
    public function getRevisionFlag();

    /**
     * Find element by id
     *
     * @param string $elementId
     * @return array|null
     */
    public function getElementDataById(string $elementId) : ?array;
}
