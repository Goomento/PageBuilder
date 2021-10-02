<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\TemplateLibrary\Sources\Local;
use Goomento\PageBuilder\Core\Base\Document;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Core\DocumentTypes\Page;
use Goomento\PageBuilder\Core\DocumentTypes\Section;
use Goomento\PageBuilder\Core\DocumentTypes\Template;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class DocumentsManager
 * @package Goomento\PageBuilder\Core
 */
class DocumentsManager
{

    /**
     * Registered types.
     *
     * Holds the list of all the registered types.
     *
     *
     * @var Document[]
     */
    protected $types = [];

    /**
     * Registered documents.
     *
     * Holds the list of all the registered documents.
     *
     *
     * @var Document[]
     */
    protected $documents = [];

    /**
     * Current document.
     *
     * Holds the current document.
     *
     *
     * @var Document
     */
    protected $current_doc;

    /**
     * Switched data.
     *
     * Holds the current document when changing to the requested post.
     *
     *
     * @var array
     */
    protected $switched_data = [];

    protected $cpt = [];

    /**
     * Documents manager constructor.
     *
     * Initializing the SagoTheme documents manager.
     *
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/documents/register', [ $this, 'registerDefaultTypes' ], 0);
        Hooks::addAction('pagebuilder/ajax/register_actions', [ $this, 'registerAjaxActions' ]);
        Hooks::addFilter('pagebuilder/current/document', [ $this, 'getCurrentDocument' ]);
    }

    /**
     * @return Document
     */
    public function getCurrentDocument(): ?Document
    {
        return $this->current_doc;
    }

    /**
     * Register ajax actions.
     *
     * Process ajax action handles when saving data and discarding changes.
     *
     * @param Ajax $ajaxManager An instance of the ajax manager.
     * @throws LocalizedException
     * @throws Exception
     */
    public function registerAjaxActions($ajaxManager)
    {
        $ajaxManager->registerAjaxAction('save_builder', [ $this, 'ajaxSave' ]);
        $ajaxManager->registerAjaxAction('discard_changes', [ $this, 'ajaxDiscardChanges' ]);
    }

    /**
     * Register default types.
     *
     * Registers the default document types.
     *
     */
    public function registerDefaultTypes()
    {
        $default_types = [
            'page' => Page::class,
            'template' => Template::class,
            'section' => Section::class,
        ];

        foreach ($default_types as $type => $class) {
            $this->registerDocumentType($type, $class);
        }
    }

    /**
     * @param $type
     * @param $class
     * @return $this
     */
    public function registerDocumentType($type, $class)
    {
        $this->types[ $type ] = $class;

        if ($class::getProperty('register_type')) {
            Local::addTemplateType($type);
        }

        return $this;
    }

    /**
     * Get document.
     *
     * Retrieve the document data based on a post ID.
     *
     *
     * @param $id
     * @param bool $from_cache Optional. Whether to retrieve cached data. Default is true.
     *
     * @return false|Document Document data or false if post ID was not entered.
     */
    public function get($id, $from_cache = true)
    {
        $this->registerTypes();

        $model = StaticContent::get($id);

        if (! $id || ! $model) {
            return false;
        }

        if (! $from_cache || ! isset($this->documents[$id])) {
            $doc_type_class = $this->getDocumentType($model->getType());
            $this->documents[$id] = StaticObjectManager::create($doc_type_class, ['data' => ['id' => $id]]);
        }

        /** TODO check the array over here */
        return $this->documents[$id];
    }


    /**
     * Get document for frontend.
     *
     * Retrieve the document for frontend use.
     *
     *
     * @param int $post_id Optional. ContentCss ID. Default is `0`.
     *
     * @return false|Document The document if it exists, False otherwise.
     * @deprecated
     */
    public function getDocForFrontend($post_id)
    {
        return $this->get($post_id);
    }

    /**
     * @param $type
     * @return false|Document
     */
    public function getDocumentType($type)
    {
        $types = $this->getDocumentTypes();
        if (isset($types[ $type ])) {
            return $types[ $type ];
        }

        return false;
    }

    /**
     * Get document types.
     *
     * Retrieve the all the registered document types.
     *
     * @return Document[] All the registered document types.
     *
     */
    public function getDocumentTypes()
    {
        $this->registerTypes();

        return $this->types;
    }

    /**
     * Get document types with their properties.
     *
     * @return array A list of properties arrays indexed by the type.
     */
    public function getTypesProperties()
    {
        $types_properties = [];

        foreach ($this->getDocumentTypes() as $type => $class) {
            $types_properties[ $type ] = $class::getProperties();
        }
        return $types_properties;
    }

    /**
     * Create a document.
     *
     * Create a new document using any given parameters.
     *
     *
     * @param string $type Document type.
     * @param array $data An array containing the post data.
     * @return Document The type of the document.
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function create($type, $data = [])
    {
        $class = $this->getDocumentType($type);

        if (!$class) {
            throw new Exception(
                __('Type %1 does not exist.', $type)
            );
        }

        if (!isset($data['status'])) {
            $data['status'] = ContentInterface::STATUS_PENDING;
        }

        $data['type'] = $type;

        $content = StaticContent::create($data);

        /** @var Document $document */
        $document = StaticObjectManager::create($class, ['data' => ['id' => $content->getId()]]);

        $document->save([]);

        return $document;
    }

    /**
     * Save document data using ajax.
     *
     * Save the document on the builder using ajax, when saving the changes, and refresh the editor.
     *
     *
     *
     * @throws Exception If current user don't have permissions to edit the post or the post is not using SagoTheme.
     *
     * @return array The document data after saving.
     */
    public function ajaxSave($request)
    {
        $document = $this->get($request['editor_post_id']);

        $data = [
            'elements' => $request['elements'],
            'settings' => $request['settings'],
        ];

        $document->save($data);

        $return_data = [
            'config' => [
                'document' => [
                    'last_edited' => $document->getLastEdited(),
                    'urls' => [
                        'system_preview' => $document->getSystemPreviewUrl(),
                    ],
                ],
            ],
        ];

        /**
         * Returned documents ajax saved data.
         *
         * Filters the ajax data returned when saving the post on the builder.
         *
         *
         * @param array    $return_data The returned data.
         * @param Document $document    The document instance.
         */
        return Hooks::applyFilters('pagebuilder/documents/ajax_save/return_data', $return_data, $document);
    }

    /**
     * Ajax discard changes.
     *
     * Load the document data from an autosave, deleting unsaved changes.
     *
     *
     * @param $request
     *
     * @return bool True if changes discarded, False otherwise.
     */
    public function ajaxDiscardChanges($request)
    {
        return $success = false;
    }

    /**
     * Switch to document.
     *
     * Change the document to any new given document type.
     *
     *
     * @param Document $document The document to switch to.
     * @deprecated
     */
    public function switchToDocument($document)
    {
        // If is already switched, or is the same post, return.
        if ($this->current_doc === $document) {
            $this->switched_data[] = false;
            return;
        }

        $this->switched_data[] = [
            'switched_doc' => $document,
            'original_doc' => $this->current_doc, // Note, it can be null if the global isn't set
        ];

        $this->current_doc = $document;
    }

    /**
     * Restore document.
     *
     * Rollback to the original document.
     *
     * @deprecated
     */
    public function restoreDocument()
    {
        $data = array_pop($this->switched_data);

        // If not switched, return.
        if (! $data) {
            return;
        }

        $this->current_doc = $data['original_doc'];
    }

    /**
     * Get current document.
     *
     * Retrieve the current document.
     *
     *
     * @return Document The current document.
     */
    public function getCurrent()
    {
        return $this->current_doc;
    }

    private function registerTypes()
    {
        if (! Hooks::didAction('pagebuilder/documents/register')) {
            /**
             * Register SagoTheme documents.
             *
             *
             * @param DocumentsManager $this The document manager instance.
             */
            Hooks::doAction('pagebuilder/documents/register', $this);
        }
    }
}
