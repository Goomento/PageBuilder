<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Api\Data;

use Magento\User\Model\User;

interface RevisionInterface extends BuildableContentInterface
{
    const REVISION_ID              = 'revision_id';
    const REVISION                 = 'revision';
    const LABEL                    = 'label';

    const CONTENT_ID               = ContentInterface::CONTENT_ID;
    const AUTHOR_ID                = ContentInterface::AUTHOR_ID;

    /**
     * Get Content ID
     *
     * @return int|null
     */
    public function getContentId() : int;

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
     * Set author Id
     *
     * @param int $authorId
     * @return RevisionInterface
     */
    public function setAuthorId($authorId);

    /**
     * Set status
     *
     * @param int $contentId
     * @return RevisionInterface
     */
    public function setContentId($contentId);

    /**
     * Set label
     *
     * @param string $label
     * @return RevisionInterface
     */
    public function setLabel(string $label) : RevisionInterface;

    /**
     * Set label
     *
     * @return string
     */
    public function getLabel() : string;
}
