<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Shapes;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Base\AbstractDocument;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\TemplateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Editor
{
    /**
     * @var int
     */
    private $contentId = null;
    /**
     * @var ContentInterface|null
     */
    private $content;

    /**
     * @var false|AbstractDocument
     */
    private $document;

    /**
     * Init the editor
     */
    public function init()
    {
        // Init document
        $this->getDocument();

        HooksHelper::addAction('pagebuilder/adminhtml/header', [ $this,'editorHeaderTrigger' ]);

        HooksHelper::addAction('pagebuilder/adminhtml/footer', [ $this,'editorFooterTrigger' ]);

        HooksHelper::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueScripts' ]);
        HooksHelper::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueStyles' ]);

        HooksHelper::doAction('pagebuilder/editor/init');
    }

    /**
     * Retrieve content ID.
     *
     * Get the ID of the current post.
     *
     *
     * @return int ContentCss ID.
     */
    public function getContentId()
    {
        if ($this->contentId === null) {
            $this->contentId = (int) RequestHelper::getParam('content_id');
        }
        return $this->contentId;
    }

    /**
     * @return ContentInterface|null
     */
    public function getContent()
    {
        if ($this->content === null) {
            $this->content = ContentHelper::get($this->getContentId());
        }
        return $this->content;
    }

    /**
     * @return false|AbstractDocument
     */
    public function getDocument()
    {
        if ($this->document === null) {
            $this->document = ObjectManagerHelper::getDocumentsManager()
                ->get($this->getContentId());
        }

        return $this->document;
    }

    /**
     */
    public function enqueueScripts()
    {
        $suffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerScript(
            'goomento-editor-modules',
            'Goomento_PageBuilder/build/editor-modules' . $suffix,
            ['goomento-common-modules']
        );

        ThemeHelper::registerScript(
            'goomento-editor-engine',
            'Goomento_PageBuilder/build/editor' . $suffix,
            [
                'underscore',
                'jquery',
                'mage/translate',
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

        ThemeHelper::registerScript(
            'goomento-editor',
            'Goomento_PageBuilder/js/editor-entry'
        );

        /**
         * Before editor enqueue scripts.
         *
         * Fires before SagoTheme editor scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/before_enqueue_scripts');

        $document = $this->getDocument();

        $config = [
            'version' => Configuration::version(),
            'data' => $document->getElementsRawData(),
            'document' => $document->getConfig(),
            'current_revision_id' => null,
            'autosave_interval' => ConfigHelper::getValue('autosave_interval') ?: 60,
            'tabs' => ObjectManagerHelper::getControlsManager()->getTabs(),
            'controls' => ObjectManagerHelper::getControlsManager()->getControlsData(),
            'elements' => ObjectManagerHelper::getElementsManager()->getElementTypesConfig(),
            'widgets' => ObjectManagerHelper::getWidgetsManager()->getWidgetTypesConfig(),
            'schemes' => [
                'items' => ObjectManagerHelper::getSchemasManager()->getRegisteredSchemesData(),
                'enabled_schemes' => Schemes::getEnabledSchemes(),
            ],
            'icons' => [
                'libraries' => Icons::getIconManagerTabsConfig(),
            ],
            'styles' => [
                'global_css' => (string) ConfigHelper::getCustomCss(),
            ],
            'default_schemes' => ObjectManagerHelper::getSchemasManager()->getSchemesDefaults(),
            'settings' => ObjectManagerHelper::getSettingsManager()->getSettingsManagersConfig(),
            'system_schemes' => ObjectManagerHelper::getSchemasManager()->getSystemSchemes(),
            'goomento_editor' => $this->getEditorConfig(),
            'tinymce_pre_init' => $this->getTinyMCEPreInit(),
            'additional_shapes' => Shapes::getAdditionalShapesForConfig(),
            'user' => [
                'roles' => [
                    'view' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('view')),
                    'save' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('save')),
                    'design' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('design')),
                    'publish' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('publish')),
                    'export' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('export')),
                    'delete' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getRoleName('delete')),
                ],
                'is_administrator' => AuthorizationHelper::isCurrentUserCan('config'),
            ],
            'rich_editing_enabled' => true,
            'inlineEditing' => ObjectManagerHelper::getWidgetsManager()->getInlineEditingConfig(),
            'dynamicTags' => ObjectManagerHelper::getTagsManager()->getConfig(),
        ];

        DataHelper::printJsConfig('goomento-editor', 'goomentoConfig', $config);

        ThemeHelper::enqueueScript('goomento-editor');

        $html = <<<HTML
    require(['Goomento_PageBuilder/js/moduleResolver'], function (moduleResolver) {
        moduleResolver.resolveJquery(() => {});
    });
HTML;

        ThemeHelper::inlineScript('goomento-editor', $html, 'before');

        ObjectManagerHelper::getControlsManager()->enqueueControlScripts();

        /**
         * After editor enqueue scripts.
         *
         * Fires after SagoTheme editor scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/after_enqueue_scripts');
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
        HooksHelper::doAction('pagebuilder/editor/before_enqueue_styles');

        $suffix = Configuration::debug() ? '' : '.min';

        $directionSuffix = DataHelper::isRtl() ? '-rtl' : '';

        ThemeHelper::registerStyle(
            'goomento-editor',
            'Goomento_PageBuilder/build/editor' . $directionSuffix . $suffix . '.css',
            [
                'goomento-common',
                'goomento-select2',
                'google-font-inter',
                'flatpickr',
                'fontawesome',
            ]
        );

        ThemeHelper::enqueueStyle('goomento-editor');

        /**
         * After editor enqueue styles.
         *
         * Fires after SagoTheme editor styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/after_enqueue_styles');
    }

    /**
     * For TinyMCE setting up
     *
     * @return string[]
     */
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
        HooksHelper::doAction('pagebuilder/editor/header');
    }

    /**
     * Print out the resources in editor
     */
    public function editorFooterTrigger()
    {
        ObjectManagerHelper::getControlsManager()->renderControls();
        ObjectManagerHelper::getWidgetsManager()->renderWidgetsContent();
        ObjectManagerHelper::getElementsManager()->renderElementsContent();
        ObjectManagerHelper::getSchemasManager()->printSchemesTemplates();
        ObjectManagerHelper::getTagsManager()->printTemplates();

        $this->initEditorTemplates();

        /**
         * SagoTheme editor footer.
         *
         * Fires on SagoTheme editor before closing the body tag.
         *
         * Used to prints scripts or any other HTML before closing the body tag.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/footer');
    }

    /**
     * Editor constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/editor/index', [ $this, 'init' ]);
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this, 'initEditorTemplates' ]);
    }

    /**
     * Init editor templates.
     *
     *
     */
    public function initEditorTemplates()
    {
        $template_names = [
            'global',
            'panel',
            'panel_elements',
            'repeater',
            'templates',
            'navigator',
            'hotkeys',
        ];
        foreach ($template_names as $template_name) {
            echo TemplateHelper::getHtml("Goomento_PageBuilder::templates/$template_name.phtml");
        }
    }
}
