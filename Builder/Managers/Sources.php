<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractSource;
use Goomento\PageBuilder\Builder\Sources\Local;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
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

    /**
     * @return void
     */
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
    public function getLibraryData(array $args, BuildableContentInterface $buildableContent)
    {
        if (!AuthorizationHelper::isCurrentUserCan($buildableContent->getRoleName('view'))) {
            throw new BuilderException(
                'Sorry, you need permissions to view this content'
            );
        }

        // Ensure all document are registered.
        $documentManager = ObjectManagerHelper::getDocumentsManager();
        $documentManager->getByContent($buildableContent);

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
     * @param BuildableContentInterface $buildableContent
     * @return mixed
     * @throws Exception
     */
    public function saveTemplate(array $args, BuildableContentInterface $buildableContent)
    {
        if (!AuthorizationHelper::isCurrentUserCan($buildableContent->getRoleName('save'))) {
            throw new BuilderException(
                'Sorry, you need permissions to save this content'
            );
        }

        $validateArgs = $this->ensureArgs([ 'content_id', 'source', 'content', 'type' ], $args);

        if (!$validateArgs) {
            throw new BuilderException('Invalid template');
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new BuilderException('Template source not found');
        }

        try {
            $args['content'] = DataHelper::decode($args['content']);
        } catch (\Exception $e) {
            $args['content'] = [];
        }

        $pageSettingsManager = ObjectManagerHelper::getSettingsManager()
            ->getSettingsManagers(PageSettings::NAME);

        $page = $pageSettingsManager->getSettingModel($buildableContent);

        $args['page_settings'] = $page->getData('settings');

        $templateId = $source->saveItem($args);

        return $source->getItem((int) $templateId);
    }

    /**
     * Update template.
     *
     * Update template on the database.
     *
     *
     * @param array $templateData New template data.
     *
     * @throws Exception
     */
    public function updateTemplate(array $templateData, BuildableContentInterface $buildableContent)
    {
        if (!AuthorizationHelper::isCurrentUserCan($buildableContent->getRoleName('delete'))) {
            throw new BuilderException(
                'Sorry, you need permissions to update this template'
            );
        }

        $this->ensureArgs([ 'source', 'content', 'type' ], $templateData);

        $source = $this->getSource($templateData['source']);

        if (!$source) {
            throw new BuilderException(
                'Template source not found.'
            );
        }

        $templateData['content'] = json_decode($templateData['content'], true);

        $source->updateItem($templateData);

        return $source->getItem($templateData['id']);
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
    public function updateTemplates(array $args, BuildableContentInterface $buildableContent)
    {
        foreach ($args['templates'] as $templateData) {
            $this->updateTemplate($templateData, $buildableContent);
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
    public function getTemplateData(array $args, BuildableContentInterface $buildableContent)
    {
        if (!AuthorizationHelper::isCurrentUserCan($buildableContent->getType() . '_view')) {
            throw new BuilderException(
                'Sorry, you need permissions to view this content'
            );
        }

        $this->ensureArgs([ 'source', 'template_id' ], $args);

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new BuilderException(
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
    public function deleteTemplate(array $args, BuildableContentInterface $buildableContent)
    {
        if (!AuthorizationHelper::isCurrentUserCan($buildableContent->getRoleName('delete'))) {
            throw new BuilderException(
                'Sorry, you need permissions to delete template'
            );
        }

        $validateArgs = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validateArgs) {
            return $validateArgs;
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new BuilderException(
                'Template source not found.'
            );
        }

        return $source->deleteTemplate((int) $args['template_id']);
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
    public function exportTemplate(array $args, BuildableContentInterface $buildableContent)
    {
        $validateArgs = $this->ensureArgs([ 'source', 'template_id' ], $args);

        if (!$validateArgs) {
            return $validateArgs;
        }

        $source = $this->getSource($args['source']);

        if (!$source) {
            throw new BuilderException(
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
    public function directImportTemplate(string $filename = 'file')
    {
        /** @var Local $source */
        $source = $this->getSource(Local::NAME);

        // phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageError
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
            throw new BuilderException(
                'Sorry, you need permissions to import template'
            );
        }

        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $fileContent = base64_decode($data['fileData']);

        $tmpFile = tmpfile();

        fwrite($tmpFile, $fileContent);

        $source = $this->getSource(Local::NAME);

        $result = $source->importTemplate($data['fileName'], stream_get_meta_data($tmpFile)['uri']);

        fclose($tmpFile);

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
        $libraryAjaxRequests = [
            'get_library_data' => [$this, 'getLibraryData'],
            'get_template_data' => [$this, 'getTemplateData'],
            'save_template' => [$this, 'saveTemplate'],
            'update_templates' => [$this, 'updateTemplates'],
            'delete_template' => [$this, 'deleteTemplate'],
            'import_template' => [$this, 'importTemplate'],
        ];

        foreach ($libraryAjaxRequests as $ajaxRequest => $callback) {
            $ajax->registerAjaxAction($ajaxRequest, $callback);
        }
    }

    /**
     * @param array $requiredArgs
     * @param array $specifiedArgs
     * @return bool
     * @throws Exception
     */
    private function ensureArgs(array $requiredArgs, array $specifiedArgs)
    {
        $notSpecifiedArgs = array_diff($requiredArgs, array_keys(array_filter($specifiedArgs)));

        if ($notSpecifiedArgs) {
            throw new BuilderException(
                sprintf('The required argument(s) "%s" not specified.', implode(', ', $notSpecifiedArgs))
            );
        }

        return true;
    }
}
