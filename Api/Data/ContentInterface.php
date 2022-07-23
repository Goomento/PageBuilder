<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\User\Api\Data\UserInterface;

interface ContentInterface extends BuildableContentInterface
{
    const CONTENT                  = 'content';

    const CONTENT_ID               = 'content_id';
    const TITLE                    = 'title';
    const STORE_IDS                = 'store_ids';
    const IDENTIFIER               = 'identifier';
    const AUTHOR_ID                = 'author_id';
    const LAST_EDITOR_ID           = 'last_editor_id';

    const IS_ACTIVE                = 'is_active';
    const ENABLED                  = 1;
    const DISABLED                 = 0;

    const TYPE_PAGE                = 'page';
    const TYPE_TEMPLATE            = 'template';
    const TYPE_SECTION             = 'section';

    const META_TITLE               = 'meta_title';
    const META_KEYWORDS            = 'meta_keywords';
    const META_DESCRIPTION         = 'meta_description';

    /**
     * @return bool
     */
    public function isPublished();

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle() : string;

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
     * Get author ID
     *
     * @return int
     */
    public function getAuthorId();

    /**
     * @return UserInterface|false
     */
    public function getAuthor();

    /**
     * Get last editor ID
     *
     * @return int
     */
    public function getLastEditorId() : int;

    /**
     * @return UserInterface
     */
    public function getLastEditorUser();

    /**
     * Set store ids
     *
     * @return int[]|[]
     */
    public function getStoreIds();

    /**
     * Set title
     *
     * @param string $title
     * @return ContentInterface
     */
    public function setTitle(string $title);

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
     */
    public function setLastEditorId($editorId);

    /**
     * @param string $role
     * @return string
     */
    public function getRoleName(string $role);

    /**
     * @param bool $active
     * @return ContentInterface
     */
    public function setIsActive(bool $active);

    /**
     * @return bool
     */
    public function getIsActive() : bool;

    /**
     * @param string $meta
     * @return ContentInterface
     */
    public function setMetaTitle(string $meta);

    /**
     * @return string
     */
    public function getMetaTitle() : string;

    /**
     * @param string $meta
     * @return ContentInterface
     */
    public function setMetaKeywords(string $meta);

    /**
     * @return string
     */
    public function getMetaKeywords() : string;

    /**
     * @param string $meta
     * @return ContentInterface
     */
    public function setMetaDescription(string $meta);

    /**
     * @return string
     */
    public function getMetaDescription() : string;
}
