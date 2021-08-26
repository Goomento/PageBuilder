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
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;
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
     * @var Data
     */
    private $dataHelper;

    /**
     * ContentManagement constructor.
     * @param ContentFactory $contentFactory
     * @param RevisionFactory $revisionFactory
     * @param ContentRepository $contentRepository
     * @param RevisionRepository $revisionRepository
     * @param UserHelper $userHelper
     * @param Data $dataHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param StoreManagerInterface $storeManager
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ContentFactory $contentFactory,
        RevisionFactory $revisionFactory,
        ContentRepository $contentRepository,
        RevisionRepository $revisionRepository,
        UserHelper $userHelper,
        Data $dataHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        StoreManagerInterface $storeManager,
        FilterGroupBuilder $filterGroupBuilder,
        ResourceConnection $resourceConnection
    ) {
        $this->contentFactory = $contentFactory;
        $this->revisionFactory = $revisionFactory;
        $this->userHelper = $userHelper;
        $this->contentRepository = $contentRepository;
        $this->revisionRepository = $revisionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->storeManager = $storeManager;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->resourceConnection = $resourceConnection;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @param array $filters
     * @return ContentSearchResultsInterface
     * @throws LocalizedException
     */
    public function getBuildableContents(array $filters = [])
    {
        $pageTypes = $this->filterBuilder->setField(ContentInterface::TYPE)
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

        if (!isset(Content::getAvailableTypes()[$data['type']])) {
            throw new LocalizedException(
                __('Invalid content type: %1', $data['type'])
            );
        }

        if (!isset(Content::getAvailableStatuses()[$data['status']])) {
            throw new LocalizedException(
                __('Invalid content status: %1', $data['status'])
            );
        }

        $model = $this->contentFactory->create();
        $model->addData($data);
        return $this->contentRepository->save($model);
    }

    /**
     * @inheritDoc
     */
    public function refreshContentCache($content)
    {
    }

    /**
     * @inheritDoc
     */
    public function refreshAllContentCache()
    {
        StaticConfig::updateThemeOption('css_updated_time', time());
    }

    /**
     * @inheritDoc
     */
    public function refreshGlobalCache()
    {
        $this->refreshAllContentCache();
        StaticConfig::deleteThemeOption(GlobalCss::META_KEY);
    }

    /**
     * @param string $from
     * @param string $to
     * @param null $content
     * @throws LocalizedException
     */
    public function replaceUrls(string $from, string $to, $content = null)
    {
        $from = trim($from);
        $to = trim($to);

        if ($from === $to) {
            throw new LocalizedException(
                __('The `from` and `to` URL\'s must be different')
            );
        }

        $is_valid_urls = (filter_var($from, FILTER_VALIDATE_URL) && filter_var($to, FILTER_VALIDATE_URL));
        if (! $is_valid_urls) {
            throw new LocalizedException(
                __('The `from` and `to` URL\'s must be valid URL\'s')
            );
        }

        $connection = $this->resourceConnection->getConnection();
        $table = $connection->getTableName('pagebuilder_content');
        $query = "UPDATE `" . $table . "` SET `elements` =  REPLACE(`elements`, '" . str_replace('/', '\\\/', $from) . "', '" . str_replace('/', '\\\/', $to) . "') ";

        $connection->query($query);

        $this->refreshAllContentCache();
    }

    /**
     * @return array
     */
    private static function defaultContentData()
    {
        return [
            'title' => '',
            'status' => 'pending',
            'type' => '',
            'elements' => null,
            'content' => null,
            'settings' => null,
        ];
    }

    /**
     * @inheridoc
     */
    public function exportContent(ContentInterface $content): void
    {
        /** @var Local $localSource */
        $localSource = StaticObjectManager::get(Local::class);
        $localSource->exportTemplate($content->getId());
    }
}
