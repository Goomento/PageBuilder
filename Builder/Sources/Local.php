<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Sources;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Builder\Base\AbstractSource;
use Goomento\PageBuilder\Builder\Managers\PageSettings;
use Goomento\PageBuilder\Builder\Settings\Page;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

class Local extends AbstractSource
{
    const NAME = 'local';

    /**
     * Template types.
     *
     * Holds the list of supported template types that can be displayed.
     *
     *
     * @var array
     */
    private static $templateTypes = [];

    /**
     * @return array
     */
    public static function getTemplateTypes()
    {
        return self::$templateTypes;
    }

    /**
     * Get local template type.
     *
     * Retrieve the template type from the post meta.
     *
     *
     * @param int $templateId The template ID.
     *
     * @return mixed The value of meta data field.
     */
    public static function getTemplateType(int $templateId)
    {
        $content = BuildableContentHelper::getContent($templateId);
        return $content->getType();
    }

    /**
     * Add template type.
     *
     * Register new template type to the list of supported local template types.
     *
     *
     * @param string $type Template type.
     */
    public static function addTemplateType(string $type)
    {
        self::$templateTypes[ $type ] = $type;
    }

    /**
     * Remove template type.
     *
     * Remove existing template type from the list of supported local template
     * types.
     *
     *
     * @param string $type Template type.
     */
    public static function removeTemplateType(string $type)
    {
        if (isset(self::$templateTypes[ $type ])) {
            unset(self::$templateTypes[ $type ]);
        }
    }

    /**
     * Get local template title.
     *
     * Retrieve the local template title.
     *
     *
     * @return string The local template title.
     */
    public function getTitle()
    {
        return __('Local');
    }

    /**
     * Register local template data.
     *
     * Used to register custom template data like a post type, a taxonomy or any
     * other data.
     *
     * The local template class registers a new `goomento_library` post type
     * and an `goomento_library_type` taxonomy. They are used to store data for
     * local templates saved by the user on his site.
     *
     */
    public function registerData()
    {
    }

    /**
     * Get local templates.
     *
     * Retrieve local templates saved by the user on his site.
     *
     *
     * @param array $args Optional. Filter templates based on a set of
     *                    arguments. Default is an empty array.
     *
     * @return array Local templates.
     * @throws Exception
     */
    public function getItems(array $args = [])
    {
        $contents = (array) BuildableContentHelper::getBuildableTemplates(200, $args['page'] ?? null);
        $templates = [];

        foreach ($contents as $content) {
            $templates[] = $this->getItem((int) $content->getId());
        }

        return $templates;
    }

    /**
     * Save local template.
     *
     * Save new or update existing template on the database.
     *
     *
     * @param array $templateData Local template data.
     *
     * @return int
     * @throws Exception
     */
    public function saveItem(array $templateData)
    {
        $defaults = [
            'title' => __('(no title)'),
            'status' => BuildableContentInterface::STATUS_PENDING,
        ];

        $templateData = array_merge($defaults, $templateData);

        $documentManager = ObjectManagerHelper::getDocumentsManager();

        $document = $documentManager->create(
            $templateData['type'],
            [
                'title' => $templateData['title'],
                'status' => BuildableContentInterface::STATUS_PUBLISHED,
            ]
        );

        $document->save([
            'elements' => $templateData['content'],
            'settings' => $templateData['page_settings'],
        ]);

        $templateId = (int) $document->getId();

        /**
         * After template library save.
         *
         * Fires after Goomento template library was saved.
         *
         *
         * @param int   $templateId   The ID of the template.
         * @param array $templateData The template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_save_template', $templateId, $templateData);

        /**
         * After template library update.
         *
         * Fires after Goomento template library was updated.
         *
         *
         * @param int   $templateId   The ID of the template.
         * @param array $templateData The template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_update_template', $templateId, $templateData);

        return $templateId;
    }

    /**
     * Update local template.
     *
     * Update template on the database.
     *
     *
     * @param array $newData New template data.
     *
     * @return true
     * @throws Exception
     */
    public function updateItem(array $newData)
    {
        $document = ObjectManagerHelper::getDocumentsManager()->getByContent(
            BuildableContentHelper::getContent((int) $newData['id'])
        );

        if (!$document) {
            throw new BuilderException('Template not exist.');
        }

        $document->save([
            'elements' => $newData['content'],
        ]);

        /**
         * After template library update.
         *
         * Fires after Goomento template library was updated.
         *
         *
         * @param int   $newDataId The ID of the new template.
         * @param array $newData    The new template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_update_template', $newData['id'], $newData);

        return true;
    }

    /**
     * @param int $templateId
     * @return array
     * @throws Exception
     */
    public function getItem(int $templateId)
    {
        $buildableContent = BuildableContentHelper::getContent($templateId);

        $pageSettings = $buildableContent->getSettings();

        $author = $buildableContent->getAuthor();

        return [
            'template_id' => $buildableContent->getId(),
            'content_id' => $buildableContent->getId(),
            'source' => $this->getName(),
            'type' => $buildableContent->getType(),
            'title' => $buildableContent->getTitle(),
            'date' => date(DATE_ATOM, strtotime($buildableContent->getCreationTime())),
            'author' => $author ? $author->getName() : null,
            'hasPageSettings' => ! empty($pageSettings),
            'export_link' => UrlBuilderHelper::getContentExportUrl($buildableContent),
            'url' => UrlBuilderHelper::getContentViewUrl($buildableContent),
            'edit_url' => UrlBuilderHelper::getContentEditUrl($buildableContent),
        ];
    }

    /**
     * Get template data.
     *
     * Retrieve the data of a single local template saved by the user on his site.
     *
     *
     * @param array $args Custom template arguments.
     *
     * @return array Local template data.
     */
    public function getData(array $args)
    {
        $documentManager = ObjectManagerHelper::getDocumentsManager();

        $templateId = (int) $args['template_id'];

        $buildableContent = BuildableContentHelper::getContent($templateId);

        $document = $documentManager->getByContent($buildableContent);

        if (!empty($args['display'])) {
            $content = $document ? $document->getElementsRawData(null, true) : [];
        } else {
            $content = $document ? $document->getElementsData() : [];
        }

        if (!empty($content)) {
            $content = $this->replaceElementsIds($content);
        }

        $data = [
            'content' => $content,
        ];

        if (!empty($args['page_settings'])) {
            $pageSettingsManager = ObjectManagerHelper::getSettingsManager()
                ->getSettingsManagers(PageSettings::NAME);

            $page = $pageSettingsManager->getSettingModel($buildableContent);

            $data['page_settings'] = $page->getData('settings');
        }

        return $data;
    }

    /**
     * @param int $templateId
     * @return bool
     * @throws Exception
     */
    public function deleteTemplate(int $templateId)
    {
        $content = BuildableContentHelper::getContent($templateId);
        if ($content) {
            BuildableContentHelper::deleteBuildableContent($content);
        }
        return true;
    }

    /**
     * @param int $templateId
     * @return array|null
     * @throws Exception
     */
    public function exportTemplate(int $templateId)
    {
        $fileData = $this->prepareTemplateExport($templateId);

        if (!$fileData) {
            return $fileData;
        }

        $this->sendFileHeaders($fileData['name'], strlen($fileData['content']));

        // Clear buffering just in case.
        // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
        @ob_end_clean();

        flush();

        // Output file contents.
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $fileData['content'];

        // phpcs:ignore Magento2.Security.LanguageConstruct.ExitUsage
        die;
    }

    /**
     * @param $name
     * @param $path
     * @return array
     * @throws Exception
     */
    public function importTemplate($name, $path)
    {
        if (empty($path)) {
            throw new BuilderException(
                'Please upload a file to import'
            );
        }

        $items = [];

        $importResult = $this->importSingleTemplate($path);

        if (!$importResult) {
            return $importResult;
        }

        $items[] = $importResult;

        return $items;
    }


    /**
     * Import single template.
     *
     * Import template from a file to the database.
     *
     *
     * @param string $fileName File name.
     *
     * @return array Local template array, or template ID
     * @throws Exception
     */
    private function importSingleTemplate($fileName)
    {
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $data = DataHelper::decode(file_get_contents($fileName));

        if (empty($data)) {
            throw new BuilderException('Invalid file');
        }

        $content = $data['content'];

        if (! is_array($content)) {
            throw new BuilderException('Invalid File');
        }

        $content = $this->processImportContent($content);

        $pageSettings = [];

        if (!empty($data['page_settings'])) {
            $page = ObjectManagerHelper::create(Page::class, [
                'data' => [
                    'id' => 0,
                    'settings' => $data['page_settings'],
                ]
            ]);

            $pageSettingsData = $this->processImportElement($page);

            if (!empty($pageSettingsData['settings'])) {
                $pageSettings = $pageSettingsData['settings'];
            }
        }

        $contentId = (int) $this->saveItem([
            'content' => $content,
            'title' => $data['title'] ?? '',
            'type' => $data['type'],
            'label' => (string) __('Imported content.'),
            'page_settings' => $pageSettings,
        ]);

        if (!$contentId) {
            throw new BuilderException(
                'Import error'
            );
        }

        $content = BuildableContentHelper::getContent($contentId);
        $currentRevision = $content->getCurrentRevision(true);
        if ($currentRevision instanceof RevisionInterface) {
            $currentRevision->setLabel((string) __('Imported content'));
            BuildableContentHelper::saveBuildableContent($currentRevision);
        }

        return $this->getItem($contentId);
    }

    /**
     * Prepare template to export.
     *
     * Retrieve the relevant template data and return them as an array.
     *
     *
     * @param int $templateId The template ID.
     *
     * @return array
     * @throws Exception
     */
    private function prepareTemplateExport(int $templateId)
    {
        $content = BuildableContentHelper::getContent($templateId);

        $templateData = $this->getData([
            'template_id' => $templateId,
        ]);

        if (empty($templateData['content'])) {
            throw new BuilderException('The template is empty');
        }

        $templateData['content'] = $this->processExportContent($templateData['content']);

        if ($settings = $this->getExportSettings($templateId)) {
            $templateData['page_settings'] = $settings;
        }

        $exportData = [
            'version' => Developer::version(),
            'title' => $content->getTitle(),
            'type' => self::getTemplateType($templateId),
        ];

        $exportData += $templateData;

        return [
            'name' => $this->getJsonName($exportData),
            'content' => DataHelper::encode($exportData),
        ];
    }

    /**
     * Get page settings which are use for setup in other sites
     *
     * @param int $templateId
     * @return array|mixed
     */
    private function getExportSettings(int $templateId)
    {
        $pageSettingsManager = ObjectManagerHelper::getSettingsManager()->getSettingsManagers(PageSettings::NAME);
        $template = BuildableContentHelper::getContent($templateId);
        $page = $pageSettingsManager->getSettingModel($template);
        $pageData = $page->getData();
        $newPageData = $this->processExportElement($page);

        if ($newPageData['settings'] !== $pageData['settings']) {
            return $newPageData['settings'];
        }

        return [];
    }

    /**
     * @param $exportData
     * @return string
     */
    private function getJsonName($exportData)
    {
        $title = $exportData['title'];
        $time  = date('\[Y-m-d_h.iA\]');
        return sprintf('pagebuilder_%s_%s.json', EscaperHelper::slugify($title, '-'), $time);
    }

    /**
     * Send file headers.
     *
     * Set the file header when export template data to a file.
     *
     *
     * @param string $fileName File name.
     * @param int    $fileSize File size.
     */
    private function sendFileHeaders(string $fileName, int $fileSize)
    {
        // phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $fileName);
        header('Expires: 0');
        header('Cache-AbstractControl: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $fileSize);
    }

    /**
     * Add template library actions.
     *
     * Register filters and actions for the template library.
     *
     */
    private function addActions()
    {
    }

    /**
     * Local constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addActions();
    }
}
