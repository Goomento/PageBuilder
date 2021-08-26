<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model\ResourceModel\Revision\Relation\Revisions;

use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Model\Revision;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class SaveHandler
 * @package Goomento\PageBuilder\Model\ResourceModel\Revision\Relation\Revisions
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var RevisionRepositoryInterface
     */
    protected $revisionRepository;
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * SaveHandler constructor.
     * @param RevisionRepositoryInterface $revisionRepository
     * @param Data $dataHelper
     */
    public function __construct(
        RevisionRepositoryInterface $revisionRepository,
        Data $dataHelper
    )
    {
        $this->revisionRepository = $revisionRepository;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param Revision $entity
     * @param array $arguments
     * @return Revision
     * @throws LocalizedException
     */
    public function execute($entity, $arguments = [])
    {
        $contentId = (int) $entity->getContentId();
        if ($contentId) {
            $revisions = $this->revisionRepository->getListByContentId($contentId);
            $maxRevision = $this->dataHelper->getAllowedNumberOfRevision();
            if ($revisions->getTotalCount() > $maxRevision) {
                $removes = array_slice($revisions->getItems(), $maxRevision, $revisions->getTotalCount() - $maxRevision);
                foreach ($removes as $remove) {
                    $this->revisionRepository->delete($remove);
                }
            }
        }
        return $entity;
    }
}
