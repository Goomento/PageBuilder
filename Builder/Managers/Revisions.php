<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Builder\Base\AbstractDocument;
use Goomento\PageBuilder\Builder\Modules\Ajax as Ajax;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
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
        /** @var ContentInterface $content */
        $content = $buildableContent->getOriginContent();
        $revisions =  BuildableContentHelper::getRevisionsByContent( $content );
        $revisionData = [];

        foreach ($revisions as $revision) {
            $author = $revision->getAuthor();
            $author = $author && $author->getId() ? $author->getName() : __('Anonymous');

            $revisionData[] = [
                'id' => $revision->getId(),
                'author' => $author,
                'label' => $revision->getLabel(),
                'timestamp' => strtotime($buildableContent->getUpdateTime()),
                'type' => $revision->getStatus(),
                'hash' => $revision->getRevisionHash(),
                'date' => date(DATE_ATOM, strtotime($revision->getCreationTime())),
            ];
        }

        usort($revisionData, function ($item1, $item2) {
            return $item2['id'] <=> $item1['id'];
        });

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

        $revision = BuildableContentHelper::getRevision($data['id']);

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
        $buildableContent = $document->getModel();
        $latestRevisions = self::getRevisions( $buildableContent );

        $lastRevisionId = $currentRevisionId = null;
        foreach ($latestRevisions as $revision) {
            if (!$currentRevisionId && $revision['hash'] === $buildableContent->getRevisionHash()) {
                $currentRevisionId = $revision['id'];
            }
            if (!$lastRevisionId && $revision['type'] !== BuildableContentInterface::STATUS_AUTOSAVE) {
                $lastRevisionId = $revision['id'];
            }
            if ($currentRevisionId && $lastRevisionId) {
                break;
            }
        }

        if (!empty($latestRevisions)) {
            if (!$currentRevisionId) {
                $currentRevision = $document->getModel()
                    ->getOriginContent()
                    ->getCurrentRevision(true);
                $currentRevisionId = $currentRevision ? $currentRevision->getId() : null;
            }

            $returnData = array_replace_recursive($returnData, [
                'config' => [
                    'last_revision_id' => $lastRevisionId,
                    'current_revision_id' => $currentRevisionId,
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
