<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\ContentSearchResultsInterface;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Api\ConfigInterface;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\AdminUser;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class ContentManagement implements ContentManagementInterface
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
     * @var Config
     */
    private $config;
    /**
     * @var Cache
     */
    private $cache;
    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * ContentManagement constructor.
     * @param ContentFactory $contentFactory
     * @param RevisionFactory $revisionFactory
     * @param ContentRepositoryInterface $contentRepository
     * @param RevisionRepositoryInterface $revisionRepository
     * @param AdminUser $userHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param ConfigInterface $config
     * @param Cache $cache
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
        ConfigInterface $config,
        Cache $cache
    ) {
        $this->contentFactory = $contentFactory;
        $this->revisionFactory = $revisionFactory;
        $this->userHelper = $userHelper;
        $this->contentRepository = $contentRepository;
        $this->revisionRepository = $revisionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->config = $config;
        $this->cache = $cache;
    }

    /**
     * @param array $filters
     * @return ContentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getBuildableContents(array $filters = [])
    {
        $pageTypes = $this->filterBuilder
            ->setField(BuildableContentInterface::TYPE)
            ->setValue([ContentInterface::TYPE_SECTION, ContentInterface::TYPE_TEMPLATE])
            ->setConditionType('in')
            ->create();

        $sortOrder = $this->sortOrderBuilder
            ->setField(ContentInterface::CONTENT_ID)
            ->setDescendingDirection()
            ->create();

        $this->searchCriteriaBuilder->addFilters([$pageTypes]);
        $this->searchCriteriaBuilder->addSortOrder($sortOrder);

        return $this->contentRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
    }

    /**
     * @inheritDoc
     */
    public function createRevision(ContentInterface $content, $status = BuildableContentInterface::STATUS_REVISION)
    {
        if (!$content->getId()) {
            throw new LocalizedException(
                __('Content Id does not specify')
            );
        }

        $lastRevision = $content->getLastRevision();

        if ($lastRevision && $lastRevision->getRevisionHash() === $content->getRevisionHash()) {
            return $lastRevision;
        } else {
            $revision = $this->revisionFactory->create();

            $revision->setOriginContent($content);
            $revision->setStatus($status);
            $revision->setContentId((int) $content->getId());

            $revision->setElements($content->getElements());
            $revision->setSettings($content->getSettings());

            return $this->revisionRepository->save($revision);
        }
    }

    /**
     * @inheritdoc
     */
    public function createContent(array $data): ContentInterface
    {
        $currentUser = $this->userHelper->getCurrentAdminUser();
        $data = array_merge(
            self::defaultContentData(),
            $data,
            [
                'author_id' => $currentUser ? $currentUser->getId() : 0,
                'last_editor_id' => $currentUser ? $currentUser->getId() : 0,
            ]
        );

        $model = $this->contentFactory->create();
        $model->addData($data);
        $model->setStatus($data['status'] ?? null);
        $model->setType($data['type'] ?? null);
        return $this->contentRepository->save($model);
    }

    /**
     * @inheritDoc
     */
    public function refreshContentAssets(ContentInterface $content)
    {
        PageBuilder::initialize();

        $content->setSetting('css/' . Config::CSS_UPDATED_TIME, 0);
        $this->contentRepository->save($content);
        $content->setDataChanges(false);

        $css = new ContentCss( $content );
        $css->update();

        if ($content->getLastRevision()) {
            $css = new ContentCss( $content->getLastRevision() );
            $css->update();
        }
    }

    /**
     * @inheritDoc
     */
    public function refreshGlobalAssets()
    {
        PageBuilder::initialize();

        $this->config->setValue(Config::CSS_UPDATED_TIME, time());
        $globalCss = new GlobalCss();
        $globalCss->update();
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
            $this->cache->invalid();
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
}
