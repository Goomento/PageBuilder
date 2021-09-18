<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\TemplateLibrary;

use Goomento\Core\Helper\ObjectManager;
use Goomento\PageBuilder\Model\Media;
use Goomento\PageBuilder\Builder\TemplateLibrary\Sources\Local;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAuthorization;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Manager
 * @package Goomento\PageBuilder\Builder\TemplateLibrary
 */
class Manager
{

    /**
     * Registered template sources.
     *
     * Holds a list of all the supported sources with their instances.
     *
     *
     * @var Sources\Base[]
     */
    protected $_registered_sources = [];

    /**
     * Template library manager constructor.
     *
     * Initializing the template library manager by registering default template
     * sources and initializing ajax calls.
     *
     */
    public function __construct()
    {
        $this->registerDefaultSources();

        $this->addActions();
    }


    public function addActions()
    {
        Hooks::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }

    /**
     * @return Media Imported images instance.
     */
    public function getImportImagesInstance()
    {
        return StaticObjectManager::get(Media::class);
    }

    /**
     * Register template source.
     *
     * Used to register new template sources displayed in the template library.
     *
     *
     * @param string $source_class The name of source class.
     * @param array  $args         Optional. Class arguments. Default is an
     *                             empty array.
     *
     * @return true True if the source was registered
     */
    public function registerSource($source_class, $args = [])
    {

        $source_instance = ObjectManager::get($source_class);

        if (! $source_instance instanceof Sources\Base) {
            throw new \Exception(
                __('Wrong Source Base')
            );
        }

        $source_id = $source_instance->getId();

        if (isset($this->_registered_sources[ $source_id ])) {
            throw new \Exception(
                __('Source existed.')
            );
        }

        $this->_registered_sources[ $source_id ] = $source_instance;

        return true;
    }

    /**
     * Unregister template source.
     *
     * Remove an existing template sources from the list of registered template
     * sources.
     *
     * @deprecated 2.7.0
     *
     *
     * @param string $id The source ID.
     *
     * @return bool Whether the source was unregistered.
     */
    public function unregisterSource($id)
    {
        return true;
    }

    /**
     * Get registered template sources.
     *
     * Retrieve registered template sources.
     *
     *
     * @return Sources\Base[] Registered template sources.
     */
    public function getRegisteredSources()
    {
        return $this->_registered_sources;
    }

    /**
     * Get template source.
     *
     * Retrieve single template sources for a given template ID.
     *
     *
     * @param string $id The source ID.
     *
     * @return false|\Goomento\PageBuilder\Builder\TemplateLibrary\Sources\Base Template sources if one exist, False otherwise.
     */
    public function getSource($id)
    {
        $sources = $this->getRegisteredSources();

        if (! isset($sources[ $id ])) {
            return false;
        }

        return $sources[ $id ];
    }

    /**
     * Get templates.
     *
     * Retrieve all the templates from all the registered sources.
     *
     *
     * @return array Templates array.
     */
    public function getTemplates()
    {
        $templates = [];
        foreach ($this->getRegisteredSources() as $source) {
            $templates = array_merge($templates, $source->getItems());
        }

        return $templates;
    }

    /**
     * Get library data.
     *
     * Retrieve the library data.
     *
     *
     * @param array $args Library arguments.
     *
     * @return array Library data.
     * @throws LocalizedException
     */
    public function getLibraryData(array $args)
    {
        $content = StaticContent::get($args['editor_post_id']);
        if (!StaticAuthorization::isCurrentUserCan($content->getRoleName('view'))) {
            throw new LocalizedException(
                __('Sorry, you need permissions to view this content')
            );
        }

        // Ensure all document are registered.
        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $documentManager->get($content->getId());

        $templates = $this->getTemplates();

        foreach ($templates as $index => $template) {
            if ($template['template_id'] == $args['editor_post_id']) {
                unset($templates[$index]);
                break;
            }
        }

        return [
            'templates' => $templates,
        ];
    }

    /**
     * @param array $args
     * @return mixed
     * @throws LocalizedException
     */
    public function saveTemplate(array $args)
    {
        $content = StaticContent::get($args['editor_post_id']);
        if (!StaticAuthorization::isCurrentUserCan($content->getType() . '_save')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to save this content')
            );
        }

        $validate_args = $this->ensureArgs([ 'post_id', 'source', 'content', 'type' ], $args);

        if (!$validate_args) {
            throw new LocalizedException(__('Invalid template'));
        }

        $source = $this->getSource($args['source']);

        if (! $source) {
            throw new \Exception('Template source not found');
        }

        $args['content'] = json_decode($args['content'], true);

        $page = SettingsManager::getSettingsManagers('page')->getSettingModel($args['post_id']);

        $args['page_settings'] = $page->getData('settings');

        $template_id = $source->saveItem($args);

        $template = StaticContent::get($template_id);

        return $source->getItem($template);
    }

    /**
     * Update template.
     *
     * Update template on the database.
     *
     *
     * @param array $template_data New template data.
     *
     * @return \Exception
     * @throws LocalizedException
     */
    public function updateTemplate(array $template_data)
    {
        $content = StaticContent::get($template_data['editor_post_id']);
        if (!StaticAuthorization::isCurrentUserCan($content->getType() . '_delete')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to update this template')
            );
        }

        $validate_args = $this->ensureArgs([ 'source', 'content', 'type' ], $template_data);

        $source = $this->getSource($template_data['source']);

        if (! $source) {
            return new \Exception('template_error', 'Template source not found.');
        }

        $template_data['content'] = json_decode($template_data['content'], true);

        $source->updateItem($template_data);

        return $source->getItem($template_data['id']);
    }

    /**
     * Update templates.
     *
     * Update template on the database.
     *
     *
     * @param array $args Template arguments.
     * @return bool
     * @throws LocalizedException
     */
    public function updateTemplates(array $args)
    {
        foreach ($args['templates'] as $template_data) {
            $result = $this->updateTemplate($template_data);
        }

        return true;
    }

    /**
     * Get template data.
     *
     * Retrieve the template data.
     *
     *
     * @param array $args Template arguments.
     *
     * @return array|bool|\Exception
     * @throws LocalizedException
     */
    public function getTemplateData(array $args)
    {
        $content = StaticContent::get($args['editor_post_id']);
        if (!StaticAuthorization::isCurrentUserCan($content->getType() . '_view')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to view this content')
            );
        }

        $validate_args = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (isset($args['edit_mode'])) {
            StaticObjectManager::get(\Goomento\PageBuilder\Core\Editor\Editor::class)->setEditMode($args['edit_mode']);
        }

        $source = $this->getSource($args['source']);

        if (! $source) {
            return new \Exception('template_error', 'Template source not found.');
        }

        Hooks::doAction('pagebuilder/template-library/before_get_source_data', $args, $source);

        $data = $source->getData($args);

        Hooks::doAction('pagebuilder/template-library/after_get_source_data', $args, $source);

        return $data;
    }

    /**
     * Delete template.
     *
     * Delete template from the database.
     *
     *
     * @param array $args Template arguments.
     *
     * @return \Exception ContentCss data on success, false or null
     * @throws LocalizedException
     */
    public function deleteTemplate(array $args)
    {
        $content = StaticContent::get($args['editor_post_id']);
        if (!StaticAuthorization::isCurrentUserCan($content->getType() . '_delete')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to import template')
            );
        }

        $validate_args = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validate_args) {
            return $validate_args;
        }

        $source = $this->getSource($args['source']);

        if (! $source) {
            return new \Exception('template_error', 'Template source not found.');
        }

        return $source->deleteTemplate($args['template_id']);
    }

    /**
     * Export template.
     *
     * Export template to a file.
     *
     *
     * @param array $args Template arguments.
     *
     * @return mixed Whether the export succeeded or failed.
     */
    public function exportTemplate(array $args)
    {
        $validate_args = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validate_args) {
            return $validate_args;
        }

        $source = $this->getSource($args['source']);

        if (! $source) {
            return new \Exception('template_error', 'Template source not found');
        }

        return $source->exportTemplate($args['template_id']);
    }

    /**
     * @param string $filename
     * @return array|\Exception|int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    public function directImportTemplate($filename = 'file')
    {
        /** @var Local $source */
        $source = $this->getSource('local');

        return $source->importTemplate($_FILES[$filename]['name'], $_FILES[$filename]['tmp_name']);
    }

    /**
     * Import template.
     *
     * Import template from a file.
     *
     *
     * @param array $data
     *
     * @return mixed Whether the export succeeded or failed.
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws \Zend_Json_Exception
     */
    public function importTemplate(array $data)
    {
        if (!StaticAuthorization::isCurrentUserCan('import')) {
            throw new LocalizedException(
                __('Sorry, you need permissions to import template')
            );
        }

        $file_content = base64_decode($data['fileData']);

        $tmp_file = tmpfile();

        fwrite($tmp_file, $file_content);

        $source = $this->getSource('local');

        $result = $source->importTemplate($data['fileName'], stream_get_meta_data($tmp_file)['uri']);

        fclose($tmp_file);

        return $result;
    }

    /**
     * Register default template sources.
     *
     * Register the 'local' and 'remote' template sources that SagoTheme use by
     * default.
     *
     */
    private function registerDefaultSources()
    {
        $sources = [
            \Goomento\PageBuilder\Builder\TemplateLibrary\Sources\Local::class,
        ];
        foreach ($sources as $sourceName) {
            $this->registerSource($sourceName);
        }
    }

    /**
     * Handle ajax request.
     *
     * Fire authenticated ajax actions for any given ajax request.
     *
     *
     * @param string $ajax_request Ajax request.
     *
     * @param array $data
     *
     * @return mixed
     * @throws \Exception
     */
    private function handleAjaxRequest($ajax_request, array $data)
    {
        if (! empty($data['editor_post_id'])) {
            $editor_post_id = (int) $data['editor_post_id'];

            if (!StaticContent::get($editor_post_id)) {
                throw new \Exception(__('ContentCss not found.'));
            }

        }

        return call_user_func([ $this, $ajax_request ], $data);
    }

    /**
     * Init ajax calls.
     *
     * Initialize template library ajax calls for allowed ajax requests.
     *
     *
     * @param Ajax $ajax
     * @throws \Exception
     */
    public function registerAjaxActions(Ajax $ajax)
    {
        $library_ajax_requests = [
            'get_library_data' => 'getLibraryData',
            'get_template_data' => 'getTemplateData',
            'save_template' => 'saveTemplate',
            'update_templates' => 'updateTemplates',
            'delete_template' => 'deleteTemplate',
            'import_template' => 'importTemplate',
        ];

        foreach ($library_ajax_requests as $ajax_request => $callback) {
            $ajax->registerAjaxAction($ajax_request, function ($data) use ($callback) {
                return $this->handleAjaxRequest($callback, $data);
            });
        }
    }

    /**
     * @param array $required_args
     * @param array $specified_args
     * @return bool|\Exception
     */
    private function ensureArgs(array $required_args, array $specified_args)
    {
        $not_specified_args = array_diff($required_args, array_keys(array_filter($specified_args)));

        if ($not_specified_args) {
            return new \Exception(
                __('The required argument(s) "%1" not specified.', implode(', ', $not_specified_args))
            );
        }

        return true;
    }
}
