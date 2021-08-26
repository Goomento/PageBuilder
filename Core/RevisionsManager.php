<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core;

use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Core\Base\Document;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticUtils;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RevisionsManager
 * @package Goomento\PageBuilder\Core
 */
class RevisionsManager
{

    /**
     * History revisions manager constructor.
     *
     * Initializing SagoTheme history revisions manager.
     *
     */
    public function __construct()
    {
        self::registerActions();
    }

    /**
     * @param $contentId
     * @return array
     * @throws LocalizedException
     */
    public static function getRevisions($contentId)
    {
        $content = StaticContent::get($contentId);
        /** @var RevisionRepositoryInterface $revisionRepo */
        $revisionRepo = StaticObjectManager::get(RevisionRepositoryInterface::class);

        $contents = $revisionRepo->getListByContentId($contentId);
        $revisions = [];
        foreach ($contents->getItems() as $revision) {
            $author = $revision->getAuthor();
            $author = $author && $author->getId() ? $author->getName() : __('Anonymous');

            $revisions[] = [
                'id' => $revision->getId(),
                'author' => $author,
                'timestamp' => $content->getUpdateTime(),
                'type' => $revision->getStatus(),
                'date' => StaticUtils::timeElapsedString($revision->getCreationTime()),
            ];
        }

        return $revisions;
    }

    /**
     *
     * @param array $data
     *
     * @return array
     * @throws LocalizedException
     */
    public static function ajaxGetRevisionData(array $data)
    {
        if (! isset($data['id'])) {
            throw new \Exception('You must set the revision ID.');
        }

        /** @var RevisionRepositoryInterface $revisionRepository */
        $revisionRepository = StaticObjectManager::get(RevisionRepositoryInterface::class);

        $revision = $revisionRepository->getById((int) $data['id']);

        if (! $revision) {
            throw new \Exception(
                __('Invalid revision.')
            );
        }

        return [
            'settings' => $revision->getSettings(),
            'elements' => $revision->getElements(),
        ];
    }


    /**
     * @param array $returnData
     * @param Document $document
     *
     * @return array
     * @throws LocalizedException
     */
    public static function onAjaxSaveBuilderData($returnData, $document)
    {
        $contentId = $document->getId();

        $latestRevisions = self::getRevisions($contentId);

        $allRevisionIds = array_column($latestRevisions, 'id');

        if (!empty($latestRevisions)) {
            $currentRevisionId = null;
            foreach ($latestRevisions as $revision) {
                if ($revision['type'] === RevisionInterface::STATUS_REVISION) {
                    $currentRevisionId = $revision['id'];
                    break;
                }
            }

            $returnData = array_replace_recursive($returnData, [
                'config' => [
                    'current_revision_id' => $currentRevisionId,
                ],
                'latest_revisions' => $latestRevisions,
                'revisions_ids' => $allRevisionIds,
            ]);
        }

        return $returnData;
    }

    /**
     * @param $data
     * @return array
     * @throws LocalizedException
     */
    public static function ajaxGetRevisions($data)
    {
        return self::getRevisions($data['editor_post_id']);
    }


    public static function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('get_revisions', [ __CLASS__, 'ajaxGetRevisions' ]);
        $ajax->registerAjaxAction('get_revision_data', [ __CLASS__, 'ajaxGetRevisionData' ]);
    }


    private static function registerActions()
    {
        Hooks::addAction('pagebuilder/ajax/register_actions', [ __CLASS__,'registerAjaxActions' ]);

        if (StaticUtils::isAjax()) {
            Hooks::addFilter('pagebuilder/documents/ajax_save/return_data', [ __CLASS__,'onAjaxSaveBuilderData' ]);
        }
    }
}
