<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\PageBuilder\Model\ContentRepository;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\Content;
use Goomento\PageBuilder\Model\ContentRepository;
use Goomento\PageBuilder\Model\ContentManagement;
use Goomento\PageBuilder\Model\Revision;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveAutosave
 * @package Goomento\PageBuilder\Plugin\PageBuilder\Model\ContentRepository
 */
class SaveAutosave
{

    /**
     * @var ContentManagement
     */
    private $contentManagement;

    /**
     * @param ContentManagement $contentManagement
     */
    public function __construct(
        ContentManagement $contentManagement
    )
    {
        $this->contentManagement = $contentManagement;
    }

    /**
     * @param ContentRepository $subject
     * @param callable $proceed
     * @param ContentInterface $content
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function aroundSave(
        ContentRepository $subject,
        callable $proceed,
        ContentInterface $content
    )
    {
        /** @var $content Content */
        if (isset(Revision::getAvailableStatuses()[$content->getStatus()])) {
            $revisionContent = clone $content;
            $content->setData($content->getOrigData());
            $content->setHasDataChanges(false);
            $revision = $this->contentManagement->createRevision($revisionContent, $revisionContent->getStatus());
            $content->setData('revision', $revision);
            $content->setHasDataChanges(false);
        } else {
            $content = $proceed($content);
        }

        return $content;
    }
}
