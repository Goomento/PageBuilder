<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Editor;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Shapes;
use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\Base\Document;
use Goomento\PageBuilder\Core\Common\App;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\DynamicTags\Manager;
use Goomento\PageBuilder\Core\Responsive\Responsive;
use Goomento\PageBuilder\Core\RevisionsManager;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAuthorization;
use Goomento\PageBuilder\Helper\StaticConfig;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticRequest;
use Goomento\PageBuilder\Helper\Theme;
use Magento\Framework\Exception\LocalizedException;
use Zend_Json_Exception;

/**
 * Class Editor
 * @package Goomento\PageBuilder\Core\Editor
 */
class Editor
{

    /**
     * @var int
     */
    private $_id = null;

    /**
     * Whether the edit mode is active.
     *
     * Used to determine whether we are in edit mode.
     *
     *
     * @var bool Whether the edit mode is active.
     */
    private $_is_edit_mode;

    /**
     * Init the editor
     */
    public function init()
    {
        $this->_id = StaticRequest::getParam('content_id');

        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);

        /** @var Document $document */
        $document = $documentManager->get($this->_id);

        StaticObjectManager::get(DocumentsManager::class)->switchToDocument($document);

        Hooks::addAction('pagebuilder/adminhtml/header', [ $this,'editorHeaderTrigger' ]);

        Hooks::addAction('pagebuilder/adminhtml/footer', [ $this,'editorFooterTrigger' ]);

        Hooks::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueScripts' ]);
        Hooks::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueStyles' ]);

        Hooks::doAction('pagebuilder/editor/init');
    }

    /**
     * Retrieve post ID.
     *
     * Get the ID of the current post.
     *
     *
     * @return int ContentCss ID.
     */
    public function getContentId()
    {
        return $this->_id;
    }

    /**
     * Whether the edit mode is active.
     *
     * Used to determine whether we are in the edit mode.
     *
     *
     * @return bool Whether the edit mode is active.
     */
    public function isEditMode()
    {
        return Hooks::didAction('pagebuilder/editor/index');
    }

    /**
     * @throws LocalizedException
     */
    public function enqueueScripts()
    {
        $suffix = Configuration::DEBUG ? '' : '.min';

        Theme::registerScript(
            'nouislider',
            'Goomento_PageBuilder/lib/nouislider/nouislider.min',
            [],
            '13.0.0'
        );

        Theme::registerScript(
            'goomento-editor-modules',
            'Goomento_PageBuilder/js/editor-modules' . $suffix,
            [
                'goomento-common-modules',
            ]
        );

        Theme::registerScript(
            'goomento-waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints-for-editor' . $suffix,
            [],
            '4.0.2'
        );

        Theme::registerScript(
            'perfect-scrollbar',
            'Goomento_PageBuilder/lib/perfect-scrollbar/js/perfect-scrollbar' . $suffix,
            [],
            '1.4.0'
        );

        Theme::registerScript(
            'jquery-easing',
            'Goomento_PageBuilder/lib/jquery-easing/jquery-easing' . $suffix,
            ['jquery'],
            '1.3.2'
        );

        Theme::registerScript(
            'nprogress',
            'Goomento_PageBuilder/lib/nprogress/nprogress' . $suffix,
            [],
            '0.2.0'
        );

        Theme::registerScript(
            'tipsy',
            'Goomento_PageBuilder/lib/tipsy/tipsy' . $suffix,
            [],
            '1.0.0'
        );

        Theme::registerScript(
            'jquery-goomento-select2',
            'Goomento_PageBuilder/lib/e-select2/js/e-select2.full' . $suffix,
            ['jquery'],
            '4.0.6-rc.1'
        );

        Theme::registerScript(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr' . $suffix,
            [],
            '1.12.0'
        );

        Theme::registerScript(
            'ace',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace',
            [],
            '1.2.5'
        );

        Theme::registerScript(
            'ace-language-tools',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ext-language_tools',
            [
                'ace',
            ],
            '1.2.5'
        );

        Theme::registerScript(
            'jquery-hover-intent',
            'Goomento_PageBuilder/lib/jquery-hover-intent/jquery-hover-intent' . $suffix,
            ['jquery'],
            '1.0.0'
        );

        Theme::registerScript(
            'iris',
            'Goomento_PageBuilder/lib/color-picker/iris.min',
            [
                'jquery',
                'jquery/ui',
            ]
        );

        Theme::registerScript(
            'wysiwygAdapter',
            'wysiwygAdapter',
            []
        );

        Theme::registerScript(
            'mage/translate',
            'mage/translate',
            ['jquery']
        );

        Theme::registerScript(
            'goomento-editor-engine',
            'Goomento_PageBuilder/js/editor' . $suffix,
            [
                'underscore',
                'jquery',
                'jquery/ui',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
                'mage/translate',
                'nouislider',
                'goomento-common',
                'goomento-editor-modules',
                'perfect-scrollbar',
                'nprogress',
                'wysiwygAdapter',
                'tipsy',
                'color-picker-alpha',
                'jquery-goomento-select2',
                'flatpickr',
                'ace',
                'ace-language-tools',
                'jquery-hover-intent',
                'imagesloaded',
            ]
        );

        Theme::registerScript(
            'goomento-editor',
            'Goomento_PageBuilder/js/editor-entry',
            [],
            null,
            true
        );

        /**
         * Before editor enqueue scripts.
         *
         * Fires before SagoTheme editor scripts are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/editor/before_enqueue_scripts');

        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentManager->get($this->_id);

        // Get document data *after* the scripts hook - so plugins can run compatibility before get data, but *before* enqueue the editor script - so elements can enqueue their own scripts that depended in editor script.
        $editor_data = $document->getElementsRawData(null, true);

        $page_title_selector = StaticConfig::getOption('page_title_selector');

        if (empty($page_title_selector)) {
            $page_title_selector = 'h1.entry-title';
        }
        /** @var Schemes $schemeManager */
        $schemeManager = StaticObjectManager::get(Schemes::class);

        $revisions = RevisionsManager::getRevisions((int) $document->getId());
        foreach ($revisions as $revision) {
            if ($revision['type'] === 'revision') {
                break;
            }
        }
        /** @var Controls $controlsManager */
        $controlsManager = StaticObjectManager::get(Controls::class);
        /** @var Elements $elementsManager */
        $elementsManager = StaticObjectManager::get(Elements::class);
        /** @var Widgets $widgetsManager */
        $widgetsManager = StaticObjectManager::get(Widgets::class);
        /** @var Schemes $schemasManager */
        $schemasManager = StaticObjectManager::get(Schemes::class);
        $config = [
            'version' => Configuration::VERSION,
            'data' => $editor_data,
            'document' => $document->getConfig(),
            'current_revision_id' => $revision['id'] ?? null,
            'autosave_interval' => StaticData::getPageBuilderConfig('editor/autosave_interval') ?: 60,
            'tabs' => $controlsManager->getTabs(),
            'controls' => $controlsManager->getControlsData(),
            'elements' => $elementsManager->getElementTypesConfig(),
            'widgets' => $widgetsManager->getWidgetTypesConfig(),
            'schemes' => [
                'items' => $schemeManager->getRegisteredSchemesData(),
                'enabled_schemes' => Schemes::getEnabledSchemes(),
            ],
            'icons' => [
                'libraries' => Icons::getIconManagerTabsConfig(),
            ],
            'default_schemes' => $schemeManager->getSchemesDefaults(),
            'settings' => SettingsManager::getSettingsManagersConfig(),
            'system_schemes' => $schemasManager->getSystemSchemes(),
            'goomento_editor' => $this->getEditorConfig(),
            'tinymce_pre_init' => $this->getTinyMCEPreInit(),
            'additional_shapes' => Shapes::getAdditionalShapesForConfig(),
            'user' => [
                'roles' => [
                    'view' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('view')),
                    'save' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('save')),
                    'design' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('save')),
                    'publish' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('publish')),
                    'export' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('export')),
                    'delete' => StaticAuthorization::isCurrentUserCan(
                        $document->getContentModel()->getRoleName('delete')),
                ],
                'is_administrator' => true,
            ],
            'rich_editing_enabled' => true,
            'page_title_selector' => $page_title_selector,
            'inlineEditing' => StaticObjectManager::get(Widgets::class)->getInlineEditingConfig(),
            'dynamicTags' => StaticObjectManager::get(Manager::class)->getConfig(),
            'editButtons' => StaticConfig::getOption('edit_buttons'),
        ];

        Utils::printJsConfig('goomento-editor', 'goomentoConfig', $config);

        Theme::enqueueScript('goomento-editor');

        StaticObjectManager::get(Controls::class)->enqueueControlScripts();

        /**
         * After editor enqueue scripts.
         *
         * Fires after SagoTheme editor scripts are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/editor/after_enqueue_scripts');
    }

    /**
     * Enqueue styles.
     *
     * Registers all the editor styles and enqueues them.
     *
     */
    public function enqueueStyles()
    {
        /**
         * Before editor enqueue styles.
         *
         * Fires before SagoTheme editor styles are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/editor/before_enqueue_styles');

        $suffix = Utils::isScriptDebug() ? '' : '.min';

        $direction_suffix = StaticData::isRtl() ? '-rtl' : '';

        Theme::registerStyle(
            'font-awesome',
            'Goomento_PageBuilder/lib/font-awesome/css/font-awesome' . $suffix . '.css',
            [],
            '4.7.0'
        );

        Theme::registerStyle(
            'gmt-select2',
            'Goomento_PageBuilder/lib/e-select2/css/e-select2' . $suffix . '.css',
            [],
            '4.0.6-rc.1'
        );

        Theme::registerStyle(
            'google-font-roboto',
            'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700',
            [],
            ''
        );

        Theme::registerStyle(
            'google-font-inter',
            'https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap',
            [],
            ''
        );

        Theme::registerStyle(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr' . $suffix . '.css',
            [],
            '1.12.0'
        );

        Theme::registerStyle(
            'goomento-select2',
            'Goomento_PageBuilder/lib/e-select2/css/e-select2' . $suffix . '.css'
        );

        Theme::registerStyle(
            'goomento-editor',
            'Goomento_PageBuilder/css/editor' . $direction_suffix . $suffix . '.css',
            [
                'goomento-common',
                'goomento-select2',
                'google-font-inter',
                'flatpickr',
                'fontawesome',
            ]
        );

        Theme::enqueueStyle('goomento-editor');

        if (Responsive::hasCustomBreakpoints()) {
            $breakpoints = Responsive::getBreakpoints();

            Theme::inlineStyle('goomento-editor', '.gmt-device-tablet #gmt-preview-responsive-wrapper { width: ' . $breakpoints['md'] . 'px; }');
        }

        /**
         * After editor enqueue styles.
         *
         * Fires after SagoTheme editor styles are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/editor/after_enqueue_styles');
    }

    private function getTinyMCEPreInit()
    {
        return [
            'toolbar' => 'undo redo | formatselect | styleselect | fontsizeselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table charmap | forecolor backcolor',
            'plugins' => 'advlist autolink lists link charmap media noneditable table contextmenu paste code help table textcolor colorpicker',
            'fontsize_formats' => '8px 9px 10px 11px 12px 14px 18px 24px 30px 36px 48px 60px 72px 96px',
        ];
    }

    /**
     * Get default editor config.
     *
     *
     */
    private function getEditorConfig()
    {
        $addMedia = (string) __('Add Media');
        return <<<EDITOR_WAPPER
<div id="goomentoEditor-wrap" class="gmt-editor-wrap tmce-active">
    <div id="goomentoEditor-editor-tools" class="gmt-editor-tools hide-if-no-js">
        <div id="goomentoEditor-media-buttons" class="gmt-media-buttons">
            <button type="button" id="insert-media-button" class="gmt-button gmt-button-default insert-media add_media" data-editor="goomentoEditor">
            <i class="fas fa-picture-o" aria-hidden="true"></i> {$addMedia}</button>
        </div>
    </div>
    <div id="goomentoEditor-editor-container" class="gmt-editor-container">
        <div id="qt_goomentoEditor_toolbar" class="quicktags-toolbar"></div>
        <textarea class="gmt-editor gmt-editor-area" autocomplete="off" cols="40" name="goomentoEditor" id="goomentoEditor">%%EDITORCONTENT%%</textarea>
    </div>
</div>
EDITOR_WAPPER;
    }

    /**
     *
     */
    public function editorHeaderTrigger()
    {
        /**
         * SagoTheme editor head.
         *
         * Fires on SagoTheme editor head tag.
         *
         * Used to prints scripts or any other data in the head tag.
         *
         */
        Hooks::doAction('pagebuilder/editor/header');
    }

    /**
     *
     */
    public function editorFooterTrigger()
    {
        StaticObjectManager::get(Controls::class)->renderControls();
        StaticObjectManager::get(Widgets::class)->renderWidgetsContent();
        StaticObjectManager::get(Elements::class)->renderElementsContent();

        StaticObjectManager::get(Schemes::class)->printSchemesTemplates();

        StaticObjectManager::get(Manager::class)->printTemplates();

        $this->initEditorTemplates();

        /**
         * SagoTheme editor footer.
         *
         * Fires on SagoTheme editor before closing the body tag.
         *
         * Used to prints scripts or any other HTML before closing the body tag.
         *
         */
        Hooks::doAction('pagebuilder/editor/footer');
    }

    /**
     * Set edit mode.
     *
     * Used to update the edit mode.
     *
     *
     * @param bool $edit_mode Whether the edit mode is active.
     */
    public function setEditMode($edit_mode)
    {
        $this->_is_edit_mode = $edit_mode;
    }

    /**
     * Editor constructor.
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/editor/index', [ $this, 'init' ]);
    }

    /**
     * Init editor templates.
     *
     *
     */
    private function initEditorTemplates()
    {
        $template_names = [
            'global',
            'panel',
            'panel-elements',
            'repeater',
            'templates',
            'navigator',
            'hotkeys',
        ];

        foreach ($template_names as $template_name) {
            StaticObjectManager::get(App::class)->addTemplate("Goomento_PageBuilder::templates/$template_name.phtml");
        }
    }
}
