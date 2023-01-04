<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Helper;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Builder\Widgets\TextEditor;
use Magento\Framework\App\Helper\Context;
use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Api\BuildableContentManagementInterface;
use Goomento\PageBuilder\Api\ContentRegistryInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Api\RevisionRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

class BuildableContent extends AbstractHelper
{
    /**
     * @var ContentRepositoryInterface
     */
    private $contentRepository;
    /**
     * @var BuildableContentManagementInterface
     */
    private $contentManagement;
    /**
     * @var RevisionRepositoryInterface
     */
    private $revisionRepository;
    /**
     * @var ContentRegistryInterface
     */
    private $contentRegistry;

    /**
     * @param Context $context
     * @param ContentRepositoryInterface $contentRepository
     * @param BuildableContentManagementInterface $contentManagement
     * @param ContentRegistryInterface $contentRegistry
     * @param RevisionRepositoryInterface $revisionRepository
     */
    public function __construct(
        Context                             $context,
        ContentRepositoryInterface          $contentRepository,
        BuildableContentManagementInterface $contentManagement,
        ContentRegistryInterface            $contentRegistry,
        RevisionRepositoryInterface         $revisionRepository
    ) {
        parent::__construct($context);
        $this->contentRepository = $contentRepository;
        $this->contentManagement = $contentManagement;
        $this->revisionRepository = $revisionRepository;
        $this->contentRegistry = $contentRegistry;
    }


    /**
     * @param ContentInterface $content
     * @param array|null $statuses
     * @param int|null $limit
     * @param int|null $currentPage
     * @return RevisionInterface[]
     * @throws LocalizedException
     */
    public function getRevisionsByContent(ContentInterface $content, ?array $statuses = null, ?int $limit = 12, ?int $currentPage = 1)
    {
        return $this->revisionRepository->getListByContentId(
            (int) $content->getId(),
            $statuses,
            $limit,
            $currentPage
        )->getItems();
    }

    /**
     * @param int|null $limit
     * @param int|null $currentPage
     * @return BuildableContentInterface[]
     * @throws LocalizedException
     */
    public function getBuildableTemplates(?int $limit = 12, ?int $currentPage = 1)
    {
        return $this->contentManagement->getBuildableTemplates($limit, $currentPage)->getItems();
    }

    /**
     * @param $revisionId
     * @return RevisionInterface
     * @throws LocalizedException
     */
    public function getRevision($revisionId)
    {
        return $this->revisionRepository->getById(
            (int) $revisionId
        );
    }

    /**
     * @param string|int $contentId
     * @return ContentInterface|null
     */
    public function getContent($contentId)
    {
        return $this->contentRegistry->getByIdentifier((string) $contentId);
    }

    /**
     * @param array $data
     * @return BuildableContentInterface
     */
    public function createContent(array $data)
    {
        /** @var ContentInterface $content */
        $content = $this->contentManagement->buildBuildableContent(ContentInterface::CONTENT, $data);
        return $this->contentManagement->saveBuildableContent($content);
    }


    /**
     * Create Content with HTML
     *
     * @param string $html
     * @param array $data
     * @return array
     */
    public static function getContentElementsWithHtml(string $html, array $data = []) : array
    {
        $data['elements'] = [[
            'id' => EncryptorHelper::randomString(7),
            'isInner' => false,
            'elType' => Section::NAME,
            'settings' => [],
            'elements' => [[
                'id' => EncryptorHelper::randomString(7),
                'isInner' => false,
                'elType' => Column::NAME,
                'settings' => [
                    '_column_size' => 100
                ],
                'elements' => [[
                    'id' => EncryptorHelper::randomString(7),
                    'isInner' => false,
                    'elType' => TextEditor::TYPE,
                    'widgetType' => TextEditor::NAME,
                    'elements' => [],
                    'settings' => [
                        TextEditor::NAME . '_editor' => /** @noEscape */ $html
                    ],
                ]]
            ]],
        ]];

        return $data;
    }

    /**
     * @param BuildableContentInterface $content
     * @param string $saveMassage
     * @return BuildableContentInterface|null
     */
    public function saveBuildableContent(BuildableContentInterface $content, string $saveMassage = ''): ?BuildableContentInterface
    {
        return $this->contentManagement->saveBuildableContent($content, $saveMassage);
    }

    /**
     * @param BuildableContentInterface $content
     */
    public function deleteBuildableContent(BuildableContentInterface $content)
    {
        $this->contentManagement->deleteBuildableContent($content);
    }
}
