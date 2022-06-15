<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
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

        if (RequestHelper::isAjax() === true) {
            HooksHelper::addFilter('pagebuilder/documents/ajax_save/return_data', [ __CLASS__,'onAjaxSaveBuilderData' ]);
        }
    }

    /**
     * @param BuildableContentInterface $buildableContent
     * @return array
     * @throws Exception
     */
    public static function getRevisions( BuildableContentInterface $buildableContent )
    {
        $revisions =  ContentHelper::getRevisionsByContent( $buildableContent->getOriginContent() );
        $revisionData = [];

        foreach ($revisions as $revision) {
            $author = $revision->getAuthor();
            $author = $author && $author->getId() ? $author->getName() : __('Anonymous');

            $revisionData[] = [
                'id' => $revision->getId(),
                'author' => $author,
                'timestamp' => $buildableContent->getUpdateTime(),
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
    public static function onAjaxSaveBuilderData($returnData, AbstractDocument $document)
    {
        $latestRevisions = self::getRevisions( $document->getModel() );

        $lastRevisionId = null;
        foreach ($latestRevisions as $revision) {
            if ($revision['type'] !== BuildableContentInterface::STATUS_AUTOSAVE) {
                $lastRevisionId = $revision['id'];
                break;
            }
        }

        if (!empty($latestRevisions)) {
            $returnData = array_replace_recursive($returnData, [
                'config' => [
                    'current_revision_id' => $lastRevisionId, // get last
                ],
                'latest_revisions' => $latestRevisions,
                'revisions_ids' => array_column($latestRevisions, 'id'),
            ]);
        }

        return $returnData;
    }

    /**
     * @param array $data
     * @param BuildableContentInterface $buildableContent
     * @return array
     * @throws Exception
     */
    public static function ajaxGetRevisions(array $data, BuildableContentInterface $buildableContent)
    {
        return self::getRevisions( $buildableContent );
    }


    public static function registerAjaxActions(Ajax $ajax)
    {
        $ajax->registerAjaxAction('get_revisions', [ __CLASS__, 'ajaxGetRevisions' ]);
        $ajax->registerAjaxAction('get_revision_data', [ __CLASS__, 'ajaxGetRevisionData' ]);
    }
}
