<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Observer;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Logger\Logger;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProcessCleanRevisionsObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    private $dataHelper;
    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Data $dataHelper
     * @param Logger $logger
     * @param ResourceConnection $resource
     */
    public function __construct(
        Data $dataHelper,
        Logger $logger,
        ResourceConnection $resource
    ) {
        $this->dataHelper = $dataHelper;
        $this->resource = $resource;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        try {
            $numberOfRevisionToKeep = $this->dataHelper->getNumberOfRevisionToKeep();
            if ($numberOfRevisionToKeep) {
                $content = $observer->getEvent()->getObject();
                if ($content instanceof ContentInterface && $content->getId() &&
                    $content->getFlag('clean_revision') !== true) {
                    $content->setFlag('clean_revision', true);
                    $this->cleanRevisionByContent($content, $numberOfRevisionToKeep);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }

    /**
     * Clean the revisions
     *
     * @param ContentInterface $content
     * @param int $numberOfRevisionToKeep
     * @return void
     */
    private function cleanRevisionByContent(ContentInterface $content, int $numberOfRevisionToKeep)
    {
        $conn = $this->resource->getConnection();
        $revisionTable = $conn->getTableName('pagebuilder_content_revision');
        $selectCount = $conn->select()->from(
            $revisionTable,
            'COUNT(*)'
        )->where('content_id = ?', $content->getId());
        $count = (int) $conn->fetchOne($selectCount);

        if ($count && $count > $numberOfRevisionToKeep) {
            $removeNumber = $count - $numberOfRevisionToKeep;
            $select = $conn->select()->from(
                $revisionTable,
                'revision_id'
            )
                ->where('content_id = ?', $content->getId())
                ->order('revision_id asc')
                ->limit($removeNumber);
            $revisionIds = $conn->fetchCol($select);
            $conn->delete(
                $revisionTable,
                ['revision_id in (?)' => $revisionIds]
            );
        }
    }
}
