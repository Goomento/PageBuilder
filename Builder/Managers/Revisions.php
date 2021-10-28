<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Builder\Base\AbstractDocument;
use Goomento\PageBuilder\Builder\Modules\Ajax as Ajax;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\RequestHelper;

class Revisions
{

    /**
     * History revisions manager constructor.
     *
     * Initializing SagoTheme history revisions manager.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ __CLASS__,'registerAjaxActions' ]);

        if (RequestHelper::isAjax()) {
            HooksHelper::addFilter('pagebuilder/documents/ajax_save/return_data', [ __CLASS__,'onAjaxSaveBuilderData' ]);
        }
    }

    /**
     * @param $contentId
     * @return array
     * @throws Exception
     */
    public static function getRevisions($contentId)
    {
        $content = ContentHelper::get($contentId);
        $revisions =  ContentHelper::getRevisionsByContent($content);
        $revisionData = [];

        foreach ($revisions->getItems() as $revision) {
            $author = $revision->getAuthor();
            $author = $author && $author->getId() ? $author->getName() : __('Anonymous');

            $revisionData[] = [
                'id' => $revision->getId(),
                'author' => $author,
                'timestamp' => $content->getUpdateTime(),
                'type' => $revision->getStatus(),
                'date' => DataHelper::timeElapsedString($revision->getCreationTime()),
            ];
        }

        return $revisionData;
    }

    /**
     *
     * @param array $data
     *
     * @return array
     * @throws Exception
     */
    public static function ajaxGetRevisionData(array $data)
    {
        if (!isset($data['id'])) {
            throw new Exception(
                'You must set the revision ID.'
            );
        }

        $revision = ContentHelper::getRevision($data['id']);

        if (!$revision) {
            throw new Exception(
                'Invalid revision.'
            );
        }

        return [
            'settings' => $revision->getSettings(),
            'elements' => $revision->getElements(),
        ];
    }


    /**
     * @param array $returnData
     * @param AbstractDocument $document
     *
     * @return array
     * @throws Exception
     */
    public static function onAjaxSaveBuilderData($returnData, $document)
    {
        $contentId = $document->getId();

        $latestRevisions = self::getRevisions($contentId);

        $allRevisionIds = array_column($latestRevisions, 'id');

        if (!empty($latestRevisions)) {
            $currentRevisionId = null;
            foreach ($latestRevisions as $revision) {
                if ($revision['type'] === RevisionInterface::STATUS_AUTOSAVE) {
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
     * @throws Exception
     */
    public static function ajaxGetRevisions($data)
    {
        return self::getRevisions($data['content_id']);
    }


    public static function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('get_revisions', [ __CLASS__, 'ajaxGetRevisions' ]);
        $ajax->registerAjaxAction('get_revision_data', [ __CLASS__, 'ajaxGetRevisionData' ]);
    }
}
