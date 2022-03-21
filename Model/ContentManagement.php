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
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\ConfigInterface;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\AdminUser;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Api\FilterBuilder;
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
     * ContentManagement constructor.
     * @param ContentFactory $contentFactory
     * @param RevisionFactory $revisionFactory
     * @param ContentRepositoryInterface $contentRepository
     * @param RevisionRepositoryInterface $revisionRepository
     * @param AdminUser $userHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param ConfigInterface $config
     */
    public function __construct(
        ContentFactory $contentFactory,
        RevisionFactory $revisionFactory,
        ContentRepositoryInterface $contentRepository,
        RevisionRepositoryInterface $revisionRepository,
        AdminUser $userHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder,
        ConfigInterface $config
    ) {
        $this->contentFactory = $contentFactory;
        $this->revisionFactory = $revisionFactory;
        $this->userHelper = $userHelper;
        $this->contentRepository = $contentRepository;
        $this->revisionRepository = $revisionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
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
        if (!$content->hasDataChanges()) {
            return false;
        }

        $revision = $this->revisionFactory->create();

        $revision->setStatus($status);
        $revision->setContentId($content->getId());
        $revision->setAuthorId($content->getLastEditorId() ?: $content->getAuthorId());

        if ($status === RevisionInterface::STATUS_AUTOSAVE) {
            $revision->setElements($content->getElements());
            $revision->setSettings($content->getSettings());
        } else {
            $revision->setElements($content->getOrigData(ContentInterface::ELEMENTS));
            $revision->setSettings($content->getOrigData(ContentInterface::SETTINGS));
        }

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
    public function refreshContentAssets(ContentInterface $content)
    {
        PageBuilder::initialize();

        $content->setSetting('css/' . Config::CSS_UPDATED_TIME, 0);
        $flag = $content->getRevisionFlag();
        /** @var Content $content */
        $content->setRevisionFlag(false);
        $this->contentRepository->save($content);
        $content->setRevisionFlag($flag);
        $content->setDataChanges(false);

        $css = new ContentCss($content->getId());
        $css->update();
    }

    /**
     * @inheritDoc
     */
    public function refreshGlobalAssets()
    {
        $this->config->setValue(Config::CSS_UPDATED_TIME, time());
        PageBuilder::initialize();

        $globalCss = new GlobalCss();
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
        ObjectManagerHelper::getSourcesManager()
            ->getLocalSource()
            ->exportTemplate((int) $content->getId());
    }
}
