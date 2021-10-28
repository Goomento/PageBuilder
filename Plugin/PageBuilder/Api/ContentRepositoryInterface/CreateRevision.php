<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Plugin\PageBuilder\Api\ContentRepositoryInterface;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Model\Revision;
use Magento\Framework\Exception\LocalizedException;

class CreateRevision
{
    /**
     * @var ContentManagementInterface
     */
    private $contentManagement;

    /**
     * @var array
     */
    private $queue = [];

    /**
     * @param ContentManagementInterface $contentManagement
     */
    public function __construct(
        ContentManagementInterface $contentManagement
    )
    {
        $this->contentManagement = $contentManagement;
        HooksHelper::addFilter('pagebuilder/documents/ajax_save/return_data', [$this, 'saveQueueRevision'], 9);
        HooksHelper::addAction('core/end', [$this, 'saveQueueRevision']);
    }

    /**
     * @param ContentRepositoryInterface $subject
     * @param callable $proceed
     * @param ContentInterface $content
     * @return ContentInterface
     * @throws LocalizedException
     */
    public function aroundSave(
        ContentRepositoryInterface $subject,
        callable $proceed,
        ContentInterface $content
    )
    {
        $isContentRevision = isset(Revision::getAvailableStatuses()[$content->getStatus()]);
        $revisionStatus = $isContentRevision ? $content->getStatus() : RevisionInterface::STATUS_REVISION;
        /** @var \Goomento\PageBuilder\Model\Content $content */
        $shouldCreateRevision = $content->hasDataChanges() && !$content->isObjectNew() && $content->getRevisionFlag();
        $shouldSaveContent = !$isContentRevision;
        if ($shouldCreateRevision) {
            $shouldSaveRevisionImmediately = !HooksHelper::didAction('core/start');
            if ($shouldSaveRevisionImmediately === true) {
                $this->contentManagement->createRevision($content, $revisionStatus);
            } else {
                $revisionContent = clone $content;
                if (!isset($this->queue[$content->getId()])) {
                    $this->queue[$content->getId()] = [];
                }
                $this->queue[$content->getId()][$revisionStatus] = $revisionContent;
            }
        }

        if ($shouldSaveContent) {
            $content = $proceed($content);
        }

        return $content;
    }

    /**
     * @throws LocalizedException
     */
    public function saveQueueRevision($data = null)
    {
        if (!empty($this->queue)) {
            foreach ($this->queue as $contentData) {
                foreach ($contentData as $revisionStatus => $content) {
                    $this->contentManagement->createRevision($content, $revisionStatus);
                }
            }
            $this->queue = [];
        }

        return $data;
    }
}
