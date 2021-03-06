<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Sources;

use Exception;
use Goomento\PageBuilder\Api\ContentManagementInterface;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractSource;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Builder\Managers\PageSettings;
use Goomento\PageBuilder\Builder\Managers\Settings as SettingsManager;
use Goomento\PageBuilder\Builder\Settings\Page;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Model\ContentManagement;
use Magento\Framework\Exception\LocalizedException;
use Zend_Json;

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
    private static $template_types = [];

    /**
     * @return array
     */
    public static function getTemplateTypes()
    {
        return self::$template_types;
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
        $content = ContentHelper::get($templateId);
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
        self::$template_types[ $type ] = $type;
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
        if (isset(self::$template_types[ $type ])) {
            unset(self::$template_types[ $type ]);
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
     */
    public function getItems(array $args = [])
    {
        /** @var ContentManagementInterface $contentManager */
        $contentManager = ObjectManagerHelper::get(ContentManagementInterface::class);
        $contents = $contentManager->getBuildableContents();
        $templates = [];

        if ($contents) {
            foreach ($contents->getItems() as $content) {
                $templates[] = $this->getItem( (int) $content->getId() );
            }
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
            'page_settings' => [],
            'status' => BuildableContentInterface::STATUS_PENDING,
        ];

        $templateData = array_merge($defaults, $templateData);

        $documentManager = ObjectManagerHelper::getDocumentsManager();

        $document = $documentManager->create(
            $templateData['type'],
            [
                'title' => $templateData['title'],
                'status' => BuildableContentInterface::STATUS_PENDING,
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
         * Fires after SagoTheme template library was saved.
         *
         *
         * @param int   $templateId   The ID of the template.
         * @param array $templateData The template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_save_template', $templateId, $templateData);

        /**
         * After template library update.
         *
         * Fires after SagoTheme template library was updated.
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
     * @throws LocalizedException
     */
    public function updateItem(array $newData)
    {
        $document = ObjectManagerHelper::getDocumentsManager()->getByContent(
            ContentHelper::get((int) $newData['id'])
        );

        if (!$document) {
            throw new Exception(
                'Template not exist.'
            );
        }

        $document->save([
            'elements' => $newData['content'],
        ]);

        /**
         * After template library update.
         *
         * Fires after SagoTheme template library was updated.
         *
         *
         * @param int   $new_data_id The ID of the new template.
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
        $buildableContent = ContentHelper::get( $templateId );

        $pageSettings = $buildableContent->getSettings();

        $author = $buildableContent->getAuthor();

        return [
            'template_id' => $buildableContent->getId(),
            'content_id' => $buildableContent->getId(),
            'source' => $this->getName(),
            'type' => $buildableContent->getType(),
            'title' => $buildableContent->getTitle(),
            'date' => DataHelper::timeElapsedString($buildableContent->getCreationTime(), false),
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

        $buildableContent = ContentHelper::get($templateId);

        $document = $documentManager->getByContent( $buildableContent );

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

            $page = $pageSettingsManager->getSettingModel( $buildableContent );

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
        $content = ContentHelper::get($templateId);
        if ($content) {
            ContentHelper::delete($templateId);
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
        $file_data = $this->prepareTemplateExport($templateId);

        if (!$file_data) {
            return $file_data;
        }

        $this->sendFileHeaders($file_data['name'], strlen($file_data['content']));

        // Clear buffering just in case.
        @ob_end_clean();

        flush();

        // Output file contents.
        echo $file_data['content'];

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
            throw new Exception(
                'Please upload a file to import'
            );
        }

        $items = [];

        $import_result = $this->importSingleTemplate($path);

        if (!$import_result) {
            return $import_result;
        }

        $items[] = $import_result;

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
        $data = \Zend_Json::decode(file_get_contents($fileName));

        if (empty($data)) {
            throw new Exception('Invalid file');
        }

        $content = $data['content'];

        if (! is_array($content)) {
            throw new Exception('Invalid File');
        }

        $content = $this->processImportContent($content);

        $page_settings = [];

        if (!empty($data['page_settings'])) {
            $page = new Page([
                'id' => 0,
                'settings' => $data['page_settings'],
            ]);

            $page_settings_data = $this->processImportElement($page);

            if (!empty($page_settings_data['settings'])) {
                $page_settings = $page_settings_data['settings'];
            }
        }

        $contentId = $this->saveItem([
            'content' => $content,
            'title' => $data['title'] ?? '',
            'type' => $data['type'],
            'page_settings' => $page_settings,
        ]);

        if (!$contentId) {
            throw new Exception(
                'Import error'
            );
        }

        return $this->getItem((int) $contentId);
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
        $content = ContentHelper::get($templateId);

        $templateData = $this->getData([
            'template_id' => $templateId,
        ]);

        if (empty($templateData['content'])) {
            throw new Exception('The template is empty');
        }

        $templateData['content'] = $this->processExportContent($templateData['content']);

        if ($settings = $this->getExportSettings($templateId)) {
            $templateData['page_settings'] = $settings;
        }

        $exportData = [
            'version' => Configuration::version(),
            'title' => $content->getTitle(),
            'type' => self::getTemplateType($templateId),
        ];

        $exportData += $templateData;

        return [
            'name' => $this->getJsonName($exportData),
            'content' => Zend_Json::encode($exportData),
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
        $template = ContentHelper::get( $templateId );
        $page = $pageSettingsManager->getSettingModel( $template );
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
        $time  = date('Y-m-d-H-i-s');
        return sprintf('Goomento-Pagebuilder-%s-%s.json', EscaperHelper::slugify($title, '-'), $time);
    }

    /**
     * Send file headers.
     *
     * Set the file header when export template data to a file.
     *
     *
     * @param string $file_name File name.
     * @param int    $file_size File size.
     */
    private function sendFileHeaders($file_name, $file_size)
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file_name);
        header('Expires: 0');
        header('Cache-AbstractControl: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $file_size);
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
