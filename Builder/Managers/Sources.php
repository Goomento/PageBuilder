<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\AbstractSource;
use Goomento\PageBuilder\Builder\Sources\Local;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Builder\Managers\Settings as SettingsManager;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;
use Magento\Framework\Exception\LocalizedException;

class Sources
{
    use TraitComponentsLoader;

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
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }

    /**
     * Get template source.
     *
     * Retrieve single template sources for a given template ID.
     *
     *
     * @param string $id The source ID.
     *
     * @return false|AbstractSource Template sources if one exist, False otherwise.
     */
    public function getSource($id)
    {
        return $this->getComponent($id);
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
        /** @var AbstractSource $source */
        foreach ($this->getComponents() as $source) {
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
     * @throws Exception
     */
    public function getLibraryData(array $args)
    {
        $content = ContentHelper::get($args['content_id']);
        if (!AuthorizationHelper::isCurrentUserCan($content->getRoleName('view'))) {
            throw new Exception(
                'Sorry, you need permissions to view this content'
            );
        }

        // Ensure all document are registered.
        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);
        $documentManager->get($content->getId());

        $templates = $this->getTemplates();

        foreach ($templates as $index => $template) {
            if ($template['template_id'] == $args['content_id']) {
                unset($templates[$index]);
                break;
            }
        }

        return [
            'templates' => array_values($templates),
        ];
    }

    /**
     * @param array $args
     * @return mixed
     * @throws LocalizedException
     * @throws Exception
     */
    public function saveTemplate(array $args)
    {
        $content = ContentHelper::get($args['content_id']);
        if (!AuthorizationHelper::isCurrentUserCan($content->getRoleName('save'))) {
            throw new Exception(
                'Sorry, you need permissions to save this content'
            );
        }

        $validate_args = $this->ensureArgs([ 'content_id', 'source', 'content', 'type' ], $args);

        if (!$validate_args) {
            throw new Exception('Invalid template');
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new Exception('Template source not found');
        }

        $args['content'] = json_decode($args['content'], true);

        /** @var SettingsManager $settingsManager */
        $settingsManager = ObjectManagerHelper::get(SettingsManager::class);
        /** @var PageSettings $pageSettingsManager */
        $pageSettingsManager = $settingsManager->getSettingsManagers(PageSettings::NAME);

        $page = $pageSettingsManager->getSettingModel($args['content_id']);

        $args['page_settings'] = $page->getData('settings');

        $template_id = $source->saveItem($args);

        $template = ContentHelper::get($template_id);

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
     * @throws Exception
     */
    public function updateTemplate(array $template_data)
    {
        $content = ContentHelper::get($template_data['content_id']);
        if (!AuthorizationHelper::isCurrentUserCan($content->getRoleName('delete'))) {
            throw new Exception(
                'Sorry, you need permissions to update this template'
            );
        }

        $this->ensureArgs([ 'source', 'content', 'type' ], $template_data);

        $source = $this->getSource($template_data['source']);

        if (!$source) {
            throw new Exception(
                'Template source not found.'
            );
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
     * @throws Exception
     */
    public function updateTemplates(array $args)
    {
        foreach ($args['templates'] as $template_data) {
            $this->updateTemplate($template_data);
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
     * @throws Exception
     */
    public function getTemplateData(array $args)
    {
        $content = ContentHelper::get($args['content_id']);
        if (!AuthorizationHelper::isCurrentUserCan($content->getType() . '_view')) {
            throw new Exception(
                'Sorry, you need permissions to view this content'
            );
        }

        $this->ensureArgs([ 'source', 'template_id' ], $args);

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new Exception(
                'Template source not found.'
            );
        }

        HooksHelper::doAction('pagebuilder/template-library/before_get_source_data', $args, $source);

        $data = $source->getData($args);

        HooksHelper::doAction('pagebuilder/template-library/after_get_source_data', $args, $source);

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
     * @return bool|null
     * @throws Exception
     */
    public function deleteTemplate(array $args)
    {
        $content = ContentHelper::get($args['content_id']);
        if (!AuthorizationHelper::isCurrentUserCan($content->getRoleName('delete'))) {
            throw new Exception(
                'Sorry, you need permissions to delete template'
            );
        }

        $validate_args = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validate_args) {
            return $validate_args;
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new Exception(
                'Template source not found.'
            );
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
     * @throws Exception
     */
    public function exportTemplate(array $args)
    {
        $validate_args = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validate_args) {
            return $validate_args;
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new Exception(
                'Template source not found'
            );
        }

        return $source->exportTemplate((int) $args['template_id']);
    }

    /**
     * @param string $filename
     * @return array|null
     * @throws Exception
     */
    public function directImportTemplate($filename = 'file')
    {
        /** @var Local $source */
        $source = $this->getSource(Local::NAME);

        return $source->importTemplate($_FILES[$filename]['name'], $_FILES[$filename]['tmp_name']);
    }

    /**
     * @return Local
     */
    public function getLocalSource() : Local
    {
        return $this->getSource(Local::NAME);
    }

    /**
     * Import template.
     *
     * Import template from a file.
     *
     *
     * @param array $data
     *
     * @return array|null Whether the export succeeded or failed.
     * @throws Exception
     */
    public function importTemplate(array $data)
    {
        if (!AuthorizationHelper::isCurrentUserCan('import')) {
            throw new Exception(
                'Sorry, you need permissions to import template'
            );
        }

        $file_content = base64_decode($data['fileData']);

        $tmp_file = tmpfile();

        fwrite($tmp_file, $file_content);

        $source = $this->getSource(Local::NAME);

        $result = $source->importTemplate($data['fileName'], stream_get_meta_data($tmp_file)['uri']);

        fclose($tmp_file);

        return $result;
    }

    /**
     * Register default template sources.
     *
     * Register the 'local' template sources that Goomento use by
     * default.
     *
     */
    private function registerDefaultSources()
    {
        $sources = [
            Local::NAME => Local::class,
        ];
        foreach ($sources as $sourceName => $source) {
            $this->setComponent($sourceName, $source);
        }
    }

    /**
     * Handle ajax request.
     *
     * Fire authenticated ajax actions for any given ajax request.
     *
     *
     * @param callable $ajaxRequest Ajax request.
     *
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    private function handleAjaxRequest($ajaxRequest, array $data)
    {
        if (!empty($data['content_id'])) {
            $contentId = (int) $data['content_id'];

            if (!ContentHelper::get($contentId)) {
                throw new Exception(
                    'Content Not Found.'
                );
            }
        }

        return call_user_func($ajaxRequest, $data);
    }

    /**
     * Init ajax calls.
     *
     * Initialize template library ajax calls for allowed ajax requests.
     *
     *
     * @param Ajax $ajax
     * @throws Exception
     */
    public function registerAjaxActions(Ajax $ajax)
    {
        $library_ajax_requests = [
            'get_library_data' => [$this, 'getLibraryData'],
            'get_template_data' => [$this, 'getTemplateData'],
            'save_template' => [$this, 'saveTemplate'],
            'update_templates' => [$this, 'updateTemplates'],
            'delete_template' => [$this, 'deleteTemplate'],
            'import_template' => [$this, 'importTemplate'],
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
     * @return bool
     * @throws Exception
     */
    private function ensureArgs(array $required_args, array $specified_args)
    {
        $not_specified_args = array_diff($required_args, array_keys(array_filter($specified_args)));

        if ($not_specified_args) {
            throw new Exception(
                sprintf('The required argument(s) "%s" not specified.', implode(', ', $not_specified_args))
            );
        }

        return true;
    }
}
