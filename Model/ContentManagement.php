<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Model;

use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\Data\ContentSearchResultsInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Builder\TemplateLibrary\Sources\Local;
use Goomento\PageBuilder\Core\Files\Css\GlobalCss;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\StaticConfig;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\UserHelper;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class ContentManagement
 * @package Goomento\PageBuilder\Model
 */
class ContentManagement implements ContentManagementInterface
{
    /**
     * @var ContentFactory
     */
    private $contentFactory;
    /**
     * @var ContentRepository
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
     * @var UserHelper
     */
    private $userHelper;
    /**
     * @var RevisionRepository
     */
    private $revisionRepository;
    /**
     * @var RevisionFactory
     */
    private $revisionFactory;
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;
    /**
     * @var ContentRegistry
     */
    private $contentRegistry;
    /**
     * @var Config
     */
    private $config;

    /**
     * ContentManagement constructor.
     * @param ContentFactory $contentFactory
     * @param RevisionFactory $revisionFactory
     * @param ContentRepository $contentRepository
     * @param RevisionRepository $revisionRepository
     * @param UserHelper $userHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ResourceConnection $resourceConnection
     * @param ContentRegistry $contentRegistry
     * @param Config $config
     */
    public function __construct(
        ContentFactory $contentFactory,
        RevisionFactory $revisionFactory,
        ContentRepository $contentRepository,
        RevisionRepository $revisionRepository,
        UserHelper $userHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ResourceConnection $resourceConnection,
        ContentRegistry $contentRegistry,
        Config $config
    ) {
        $this->contentFactory = $contentFactory;
        $this->revisionFactory = $revisionFactory;
        $this->userHelper = $userHelper;
        $this->contentRepository = $contentRepository;
        $this->revisionRepository = $revisionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->contentRegistry = $contentRegistry;
        $this->config = $config;
    }

    /**
     * @param array $filters
     * @return ContentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getBuildableContents(array $filters = [])
    {
        $pageTypes = $this->filterBuilder
            ->setField(ContentInterface::TYPE)
            ->setValue([ContentInterface::TYPE_SECTION, ContentInterface::TYPE_TEMPLATE])
            ->setConditionType('in')
            ->create();
        $this->searchCriteriaBuilder->addFilters([$pageTypes]);
        return $this->contentRepository->getList(
            $this->searchCriteriaBuilder->create()
        );
    }

    /**
     * @inheritDoc
     */
    public function createRevision(ContentInterface $content, $status = RevisionInterface::STATUS_REVISION)
    {
        if (!$content->getId()) {
            throw new LocalizedException(
                __('Content Id does not specify')
            );
        }
        /** @var Content $content */
        $revision = $this->revisionFactory->create();
        $revision->setContentId($content->getId());
        $revision->setElements($content->getElements());
        $revision->setSettings($content->getSettings());
        $revision->setAuthorId($content->getLastEditorId() ?: $content->getAuthorId());
        $revision->setStatus($status);

        return $this->revisionRepository->save($revision);
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
    public function refreshContentCache(ContentInterface $content)
    {
        PageBuilder::initialize();

        $content->setSetting('css/' . Config::CSS_UPDATED_TIME, 0);
        $flag = $content->getCreateRevisionFlag();
        $content->setCreateRevisionFlag(false);
        $content->save();

        $css = new \Goomento\PageBuilder\Core\Files\Css\ContentCss($content->getId());
        $css->update();

        $content->setCreateRevisionFlag($flag);
    }

    /**
     * @inheritDoc
     */
    public function refreshGlobalCache()
    {
        $this->config->setOption(Config::CSS_UPDATED_TIME, time());
        PageBuilder::initialize();

        $globalCss = new \Goomento\PageBuilder\Core\Files\Css\GlobalCss();
        $globalCss->update();
    }

    /**
     * @param string $from
     * @param string $to
     * @param null $content
     */
    public function replaceUrls(string $from, string $to, $content = null)
    {
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
        /** @var Local $localSource */
        $localSource = StaticObjectManager::get(Local::class);
        $localSource->exportTemplate($content->getId());
    }
}
