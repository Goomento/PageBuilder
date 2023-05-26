<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\ContentSearchResultsInterface;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\AdminUser;
use Goomento\PageBuilder\Model\Cache\Type\PageBuilderFrontend;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\PageCache\Model\Cache\Type;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
class BuildableContentManagement implements BuildableContentManagementInterface
{
    /**
     * @var ContentFactory
     */
    private $contentFactory;
    /**
     * @var ContentRepositoryInterface
     */
    private $contentRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var AdminUser
     */
    private $userHelper;
    /**
     * @var RevisionRepositoryInterface
     */
    private $revisionRepository;
    /**
     * @var RevisionFactory
     */
    private $revisionFactory;
    /**
     * @var BetterCaching
     */
    private $cache;
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * BuildableContentManagement constructor.
     * @param ContentFactory $contentFactory
     * @param RevisionFactory $revisionFactory
     * @param ContentRepositoryInterface $contentRepository
     * @param RevisionRepositoryInterface $revisionRepository
     * @param AdminUser $userHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param BetterCaching $cache
     */
    public function __construct(
        ContentFactory $contentFactory,
        RevisionFactory $revisionFactory,
        ContentRepositoryInterface $contentRepository,
        RevisionRepositoryInterface $revisionRepository,
        AdminUser $userHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        BetterCaching $cache
    ) {
        $this->contentFactory = $contentFactory;
        $this->revisionFactory = $revisionFactory;
        $this->userHelper = $userHelper;
        $this->contentRepository = $contentRepository;
        $this->revisionRepository = $revisionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->cache = $cache;
    }

    /**
     * @param int|null $limit
     * @param int|null $currentPage
     * @return ContentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getBuildableTemplates(?int $limit, ?int $currentPage)
    {
        $pageTypes = $this->filterBuilder
            ->setField(BuildableContentInterface::TYPE)
            ->setValue([ContentInterface::TYPE_SECTION, ContentInterface::TYPE_TEMPLATE])
            ->setConditionType('in')
            ->create();

        $status = $this->filterBuilder
            ->setField(ContentInterface::IS_ACTIVE)
            ->setValue(1)
            ->setConditionType('eq')
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField(ContentInterface::CONTENT_ID)
            ->setDescendingDirection()
            ->create();

        $this->searchCriteriaBuilder->addFilters([$pageTypes]);
        $this->searchCriteriaBuilder->addFilters([$status]);
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);

        $searchCriteria = $this->searchCriteriaBuilder->create();

        if ($limit) {
            $searchCriteria->setPageSize($limit);
        }

        if ($currentPage) {
            $searchCriteria->setCurrentPage($currentPage);
        }

        return $this->contentRepository->getList($searchCriteria);
    }

    /**
     * @inheritDoc
     */
    public function refreshBuildableContentAssets(BuildableContentInterface $buildableContent)
    {
        PageBuilder::initialize();
        HooksHelper::doAction('pagebuilder/document/update_css', $buildableContent);
        // Update last revision
        if ($revision = $buildableContent->getLastRevision(true)) {
            HooksHelper::doAction('pagebuilder/document/update_css', $revision);
        }
    }

    /**
     * @inheritDoc
     */
    public function refreshGlobalAssets()
    {
        PageBuilder::initialize();
        HooksHelper::doAction('pagebuilder/documents/update_css');
    }

    /**
     * @inheritDoc
     */
    public function replaceUrls(string $find, string $replace, $content = null)
    {
        $find = trim($find);
        $replace = trim($replace);

        if ($find === $replace) {
            throw new LocalizedException(
                __('The URL to find and URL to replace must be different')
            );
        }

        $isValidUrls = (filter_var($find, FILTER_VALIDATE_URL) && filter_var($replace, FILTER_VALIDATE_URL));
        if (!$isValidUrls) {
            throw new LocalizedException(
                __('The URL to find and URL to replace must be valid URL\'s')
            );
        }

        $contentResource = $this->contentFactory->create()->getResource();
        $connection = $contentResource->getConnection();

        $bind = [
            BuildableContentInterface::ELEMENTS =>  new \Zend_Db_Expr(
                'REPLACE(' . $connection->quoteIdentifier(BuildableContentInterface::ELEMENTS) . ',' . $connection->quote(
                    str_replace('/', '\/', $find)
                ) . ', ' . $connection->quote(
                    str_replace('/', '\/', $replace)
                ) . ')'
            ),
            BuildableContentInterface::SETTINGS =>  new \Zend_Db_Expr(
                'REPLACE(' . $connection->quoteIdentifier(BuildableContentInterface::SETTINGS) . ',' . $connection->quote(
                    str_replace('/', '\/', $find)
                ) . ', ' . $connection->quote(
                    str_replace('/', '\/', $replace)
                ) . ')'
            ),
        ];
        $where = [];
        if ($content) {
            if ($content instanceof ContentInterface) {
                $where[ContentInterface::CONTENT_ID] = $content->getId();
            } else {
                $where[ContentInterface::CONTENT_ID] = (int) $content;
            }
        }

        $contentCount = $connection->update(
            $contentResource->getTable('pagebuilder_content'),
            $bind,
            $where
        );

        $revisionCount = $connection->update(
            $contentResource->getTable('pagebuilder_content_revision'),
            $bind,
            $where
        );

        if ($contentCount || $revisionCount) {
            $this->cache->invalid([
                PageBuilderFrontend::TYPE_IDENTIFIER,
                Type::TYPE_IDENTIFIER,
                Block::TYPE_IDENTIFIER
            ]);
        }
    }

    /**
     * @return array
     */
    private static function defaultContentData()
    {
        return [
            'title' => '',
            'status' => ContentInterface::STATUS_PUBLISHED,
            'type' => '',
            'elements' => [],
            'content' => '',
            'settings' => [],
        ];
    }

    /**
     * @inheridoc
     */
    public function httpContentExport(ContentInterface $content): void
    {
        PageBuilder::initialize();

        ObjectManagerHelper::getSourcesManager()
            ->getLocalSource()
            ->exportTemplate((int) $content->getId());
    }

    /**
     * @return string
     */
    public static function generateRevisionHash() : string
    {
        return EncryptorHelper::randomString(12);
    }

    /**
     * @inheridoc
     */
    public function saveBuildableContent(BuildableContentInterface $buildableContent, string $saveMassage = '') : BuildableContentInterface
    {
        if ($buildableContent instanceof ContentInterface) {
            $isContentStatus = BuildableContentHelper::isContentStatus($buildableContent);
            if ($isContentStatus) {
                $this->contentRepository->save($buildableContent);
            }
            $this->setRevisionOnContent($buildableContent, $saveMassage, $isContentStatus !== true);
        } elseif ($buildableContent instanceof RevisionInterface) {
            if ($saveMassage) {
                $buildableContent->setLabel($saveMassage);
            }
            $this->revisionRepository->save($buildableContent);
        }

        return $buildableContent;
    }

    /**
     * @param bool $isLast
     * @param ContentInterface $buildableContent
     * @param string $saveMassage
     * @return RevisionInterface
     * @throws LocalizedException
     */
    private function setRevisionOnContent(ContentInterface $buildableContent, string $saveMassage = '', bool $isLast = true) : RevisionInterface
    {
        if (!$buildableContent->getId()) {
            throw new LocalizedException(
                __('Content Id does not specify')
            );
        }

        if ($isLast) {
            $revision = $buildableContent->getLastRevision($buildableContent->getFlag('direct_save') === true);
        } else {
            $revision = $buildableContent->getCurrentRevision($buildableContent->getFlag('direct_save') === true);
        }

        if (!$revision) {
            $revision = $this->buildBuildableContent(RevisionInterface::REVISION, $buildableContent);
            if ($isLast) {
                $buildableContent->setLastRevision($revision);
            } else {
                $buildableContent->setCurrentRevision($revision);
            }
        }

        $revision->setSettings($buildableContent->getSettings());
        $revision->setElements($buildableContent->getElements());
        $revision->setOriginContent($buildableContent);

        if (!isset(Revision::getAvailableStatuses()[$revision->getStatus()])) {
            $revision->setStatus(BuildableContentInterface::STATUS_REVISION);
        }

        if ($isLast) {
            $revision->setRevisionHash(self::generateRevisionHash());
        } else {
            $revision->setRevisionHash($buildableContent->getRevisionHash());
        }

        /** @var Revision $revision */
        if (!$saveMassage && !$revision->getLabel() && $revision->getFlag('ignore_label') !== true) {
            $revision->setLabel($isLast ? __('Saved revision')->__toString() : __('Published change')->__toString());
        } elseif ($saveMassage) {
            $revision->setLabel($saveMassage);
        }

        /** @var RevisionInterface $revision */
        return $this->revisionRepository->save($revision);
    }

    /**
     * @inheridoc
     */
    public function buildBuildableContent(string $buildableType = ContentInterface::CONTENT, $params = []): ?BuildableContentInterface
    {
        if ($buildableType === ContentInterface::CONTENT) {
            $model = $this->contentFactory->create();
        } elseif ($buildableType === RevisionInterface::REVISION) {
            $model = $this->revisionFactory->create();
        } else {
            return null;
        }

        if ($params instanceof DataObject) {
            $params = $params->toArray();
        }

        $privateFields = [
            BuildableContentInterface::UPDATE_TIME,
            BuildableContentInterface::CREATION_TIME,
            BuildableContentInterface::REVISION_HASH,
        ];

        foreach ($privateFields as $field) {
            if (array_key_exists($field, $params)) {
                unset($params[$field]);
            }
        }

        return $model->setData($params);
    }

    /**
     * @inheridoc
     */
    public function deleteBuildableContent(BuildableContentInterface $buildableContent)
    {
        if ($buildableContent instanceof ContentInterface) {
            $this->contentRepository->delete($buildableContent);
        } elseif ($buildableContent instanceof RevisionInterface) {
            $this->revisionRepository->delete($buildableContent);
        }
    }
}
