<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Sources;

use Exception;
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
     * @param int $template_id The template ID.
     *
     * @return mixed The value of meta data field.
     */
    public static function getTemplateType($template_id)
    {
        $content = ContentHelper::get($template_id);
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
    public static function addTemplateType($type)
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
    public static function removeTemplateType($type)
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
    public function getItems($args = [])
    {
        /** @var ContentManagement $contentManager */
        $contentManager = ObjectManagerHelper::get(ContentManagement::class);
        $contents = $contentManager->getBuildableContents();
        $templates = [];

        if ($contents) {
            foreach ($contents->getItems() as $content) {
                $templates[] = $this->getItem($content);
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
     * @param array $template_data Local template data.
     *
     * @return mixed
     * @throws Exception
     */
    public function saveItem($template_data)
    {
        $defaults = [
            'title' => __('(no title)'),
            'page_settings' => [],
            'status' => ContentInterface::STATUS_PENDING,
        ];

        $template_data = array_merge($defaults, $template_data);

        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);

        $document = $documentManager->create(
            $template_data['type'],
            [
                'title' => $template_data['title'],
                'status' => ContentInterface::STATUS_PENDING,
            ]
        );

        $document->save([
            'elements' => $template_data['content'],
            'settings' => $template_data['page_settings'],
        ]);

        $template_id = $document->getId();

        /**
         * After template library save.
         *
         * Fires after SagoTheme template library was saved.
         *
         *
         * @param int   $template_id   The ID of the template.
         * @param array $template_data The template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_save_template', $template_id, $template_data);

        /**
         * After template library update.
         *
         * Fires after SagoTheme template library was updated.
         *
         *
         * @param int   $template_id   The ID of the template.
         * @param array $template_data The template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_update_template', $template_id, $template_data);

        return $template_id;
    }

    /**
     * Update local template.
     *
     * Update template on the database.
     *
     *
     * @param array $new_data New template data.
     *
     * @return true
     */
    public function updateItem($new_data)
    {
        $document = ObjectManagerHelper::get(Documents::class)->get($new_data['id']);

        if (!$document) {
            throw new Exception(
                'Template not exist.'
            );
        }

        $document->save([
            'elements' => $new_data['content'],
        ]);

        /**
         * After template library update.
         *
         * Fires after SagoTheme template library was updated.
         *
         *
         * @param int   $new_data_id The ID of the new template.
         * @param array $new_data    The new template data.
         */
        HooksHelper::doAction('pagebuilder/template-library/after_update_template', $new_data['id'], $new_data);

        return true;
    }

    /**
     * @param ContentInterface $model
     * @return array
     * @throws Exception
     */
    public function getItem($model)
    {
        $page_settings = $model->getSettings();

        $author = $model->getAuthor();

        return [
            'template_id' => $model->getId(),
            'content_id' => $model->getId(),
            'source' => $this->getName(),
            'type' => $model->getType(),
            'title' => $model->getTitle(),
            'date' => DataHelper::timeElapsedString($model->getCreationTime(), false),
            'author' => $author ? $author->getName() : null,
            'hasPageSettings' => ! empty($page_settings),
            'export_link' => UrlBuilderHelper::getContentExportUrl($model),
            'url' => UrlBuilderHelper::getContentViewUrl($model),
            'edit_url' => UrlBuilderHelper::getContentEditUrl($model),
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
        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);

        $template_id = $args['template_id'];
        $document = $documentManager->get($template_id);
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
            /** @var SettingsManager $settingsManager */
            $settingsManager = ObjectManagerHelper::get(SettingsManager::class);
            /** @var PageSettings $pageSettingsManager */
            $pageSettingsManager = $settingsManager->getSettingsManagers(PageSettings::NAME);
            $page = $pageSettingsManager->getSettingModel((int) $args['template_id']);

            $data['page_settings'] = $page->getData('settings');
        }

        return $data;
    }

    /**
     * @param int $template_id
     * @return bool
     * @throws Exception
     */
    public function deleteTemplate($template_id)
    {
        $content = ContentHelper::get($template_id);
        if ($content) {
            ContentHelper::delete($template_id);
        }
        return true;
    }

    /**
     * @param int $template_id
     * @return array|null
     * @throws Exception
     */
    public function exportTemplate(int $template_id)
    {
        $file_data = $this->prepareTemplateExport($template_id);

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
     * @param string $file_name File name.
     *
     * @return array Local template array, or template ID
     * @throws Exception
     */
    private function importSingleTemplate($file_name)
    {
        $data = json_decode(file_get_contents($file_name), true);

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

        $content_id = $this->saveItem([
            'content' => $content,
            'title' => $data['title'] ?? '',
            'type' => $data['type'],
            'page_settings' => $page_settings,
        ]);

        if (!$content_id) {
            throw new Exception(
                'Import error'
            );
        }

        $template = ContentHelper::get($content_id);

        return $this->getItem($template);
    }

    /**
     * Prepare template to export.
     *
     * Retrieve the relevant template data and return them as an array.
     *
     *
     * @param int $template_id The template ID.
     *
     * @return array
     * @throws Exception
     */
    private function prepareTemplateExport(int $template_id)
    {
        $content = ContentHelper::get($template_id);
        $templateData = $this->getData([
            'template_id' => $template_id,
        ]);

        if (empty($templateData['content'])) {
            throw new Exception('The template is empty');
        }

        $templateData['content'] = $this->processExportContent($templateData['content']);

        if ($settings = $this->getExportSettings($template_id)) {
            $templateData['page_settings'] = $settings;
        }

        $export_data = [
            'version' => Configuration::version(),
            'title' => $content->getTitle(),
            'type' => self::getTemplateType($template_id),
        ];

        $export_data += $templateData;

        return [
            'name' => $this->getJsonName($export_data),
            'content' => Zend_Json::encode($export_data),
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
        /** @var SettingsManager $settingsManager */
        $settingsManager = ObjectManagerHelper::get(SettingsManager::class);
        /** @var PageSettings $pageSettingsManager */
        $pageSettingsManager = $settingsManager->getSettingsManagers(PageSettings::NAME);

        $page = $pageSettingsManager->getSettingModel($templateId);
        $pageData = $page->getData();
        $newPageData = $this->processExportElement($page);

        if ($newPageData['settings'] !== $pageData['settings']) {
            return $newPageData['settings'];
        }

        return [];
    }

    /**
     * @param $export_data
     * @return string
     */
    private function getJsonName($export_data)
    {
        $title = $export_data['title'];
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
