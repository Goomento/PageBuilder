<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\TemplateLibrary\Sources;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Data;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Core\Settings\Page\Model;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\StaticUtils;
use Goomento\PageBuilder\Model\ContentManagement;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Json;
use Zend_Json_Exception;

/**
 * Class Local
 * @package Goomento\PageBuilder\Builder\TemplateLibrary\Sources
 */
class Local extends Base
{

    /**
     * SagoTheme template-library post-type slug.
     */
    const CPT = 'goomento_library';

    /**
     * SagoTheme template-library taxonomy slug.
     */
    const TAXONOMY_TYPE_SLUG = 'goomento_library_type';


    /**
     * SagoTheme template-library temporary files folder.
     */
    const TEMP_FILES_DIR = 'goomento/tmp';


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
     * ContentCss type object.
     *
     * Holds the post type object of the current post.
     *
     *
     * @var \Goomento\PageBuilder\Api\Data\ContentInterface
     */
    private $post_type_object;

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
        $content = StaticContent::get($template_id);
        return $content->getType();
    }

    /**
     * Is base templates screen.
     *
     * Whether the current screen base is edit and the post type is template.
     *
     *
     * @return bool True on base templates screen, False otherwise.
     */
    public static function isBaseTemplatesScreen()
    {
        global $current_screen;

        if (! $current_screen) {
            return false;
        }

        return 'edit' === $current_screen->base && self::CPT === $current_screen->post_type;
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
     * Get local template ID.
     *
     * Retrieve the local template ID.
     *
     *
     * @return string The local template ID.
     */
    public function getId()
    {
        return 'local';
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
        $template_types = array_values(self::$template_types);

        if (! empty($args['type'])) {
            $template_types = $args['type'];
        }

        /** @var ContentManagement $contentManager */
        $contentManager = StaticObjectManager::get(ContentManagement::class);
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
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function saveItem($template_data)
    {
        $defaults = [
            'title' => __('(no title)'),
            'page_settings' => [],
            'status' => 'pending',
        ];

        $template_data = array_merge($defaults, $template_data);

        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);

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

        $template_id = $document->getMainId();

        /**
         * After template library save.
         *
         * Fires after SagoTheme template library was saved.
         *
         *
         * @param int   $template_id   The ID of the template.
         * @param array $template_data The template data.
         */
        Hooks::doAction('pagebuilder/template-library/after_save_template', $template_id, $template_data);

        /**
         * After template library update.
         *
         * Fires after SagoTheme template library was updated.
         *
         *
         * @param int   $template_id   The ID of the template.
         * @param array $template_data The template data.
         */
        Hooks::doAction('pagebuilder/template-library/after_update_template', $template_id, $template_data);

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
        $document = StaticObjectManager::get(DocumentsManager::class)->get($new_data['id']);

        if (! $document) {
            new Exception(
                __('Template not exist.')
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
        Hooks::doAction('pagebuilder/template-library/after_update_template', $new_data['id'], $new_data);

        return true;
    }

    /**
     * @param ContentInterface $model
     * @return array
     * @throws Zend_Json_Exception
     */
    public function getItem($model)
    {
        $page_settings = $model->getSettings();

        $author = $model->getAuthor();

        return [
            'template_id' => $model->getId(),
            'source' => $this->getId(),
            'type' => $model->getType(),
            'title' => $model->getTitle(),
            'date' => StaticUtils::timeElapsedString($model->getCreationTime(), false),
            'author' => $author ? $author->getName() : null,
            'hasPageSettings' => ! empty($page_settings),
            'export_link' => StaticUrlBuilder::getContentExportUrl($model),
            'url' => StaticUrlBuilder::getContentViewUrl($model),
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
        $db = StaticObjectManager::get(\Goomento\PageBuilder\Builder\Data::class);

        $template_id = $args['template_id'];

        // TODO: Validate the data (in JS too!).
        if (! empty($args['display'])) {
            $content = $db->getBuilder($template_id);
        } else {
            $document = StaticObjectManager::get(DocumentsManager::class)->get($template_id);
            $content = $document ? $document->getElementsData() : [];
        }

        if (! empty($content)) {
            $content = $this->replaceElementsIds($content);
        }

        $data = [
            'content' => $content,
        ];

        if (! empty($args['page_settings'])) {
            $page = SettingsManager::getSettingsManagers('page')->getSettingModel($args['template_id']);

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
        $content = StaticContent::get($template_id);
        if ($content) {
            $content->delete();
        }
        return true;
    }

    /**
     * @param int $template_id
     * @return array|Exception|null
     * @throws NoSuchEntityException
     */
    public function exportTemplate($template_id)
    {
        $file_data = $this->prepareTemplateExport($template_id);

        if (! $file_data) {
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
     * @return array|Exception
     * @throws NoSuchEntityException
     * @throws Zend_Json_Exception|LocalizedException
     */
    public function importTemplate($name, $path)
    {
        if (empty($path)) {
            return new Exception('Please upload a file to import');
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
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws Exception
     * @throws Zend_Json_Exception
     */
    private function importSingleTemplate($file_name)
    {
        $data = json_decode(file_get_contents($file_name), true);

        if (empty($data)) {
            throw new Exception('Invalid File');
        }

        $content = $data['content'];

        if (! is_array($content)) {
            throw new Exception('Invalid File');
        }

        $content = $this->processExportImportContent($content, 'onImport');

        $page_settings = [];

        if (! empty($data['page_settings'])) {
            $page = new Model([
                'id' => 0,
                'settings' => $data['page_settings'],
            ]);

            $page_settings_data = $this->processElementExportImportContent($page, 'onImport');

            if (! empty($page_settings_data['settings'])) {
                $page_settings = $page_settings_data['settings'];
            }
        }

        $template_id = $this->saveItem([
            'content' => $content,
            'title' => $data['title'] ?? '(no title)',
            'type' => $data['type'],
            'page_settings' => $page_settings,
        ]);

        if (!$template_id) {
            throw new LocalizedException(
                __('Import error')
            );
        }

        $template = StaticContent::get($template_id);

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
    private function prepareTemplateExport($template_id)
    {
        $content = StaticContent::get($template_id);
        $template_data = $this->getData([
            'template_id' => $template_id,
        ]);

        if (empty($template_data['content'])) {
            throw new Exception('The template is empty');
        }

        $template_data['content'] = $this->processExportImportContent($template_data['content'], 'onExport');

        if ($content->hasSetting('_goomento_page_settings')) {
            $page = SettingsManager::getSettingsManagers('page')->getSettingModel($template_id);

            $page_settings_data = $this->processElementExportImportContent($page, 'onExport');

            if (! empty($page_settings_data['settings'])) {
                $template_data['page_settings'] = $page_settings_data['settings'];
            }
        }

        $export_data = [
            'version' => Data::DB_VERSION,
            'title' => $content->getTitle(),
            'type' => self::getTemplateType($template_id),
        ];

        $export_data += $template_data;

        return [
            'name' => $this->getJsonName($export_data),
            'content' => Zend_Json::encode($export_data),
        ];
    }

    /**
     * @param $export_data
     * @return string
     */
    private function getJsonName($export_data)
    {
        $export_data = json_encode($export_data['content']);
        $export_key = md5($export_data);
        return sprintf('pagebuilder_%s.json', substr($export_key, 0, 6));
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
        header('Cache-Control: must-revalidate');
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
