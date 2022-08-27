<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Api\Data\RevisionInterface;
use Goomento\PageBuilder\Builder\Managers\Icons;
use Goomento\PageBuilder\Builder\Managers\Schemes;
use Goomento\PageBuilder\Builder\Shapes;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Base\AbstractDocument;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Magento\Framework\RequireJs\Config;

class Editor
{
    /**
     * @var BuildableContentInterface|null
     */
    private $content;

    /**
     * @var false|AbstractDocument
     */
    private $document;

    /**
     * @var RevisionInterface|null
     */
    private $lastRevision;
    /**
     * @var BuildableContentInterface|null
     */
    private $currentRevision;

    /**
     * Init the editor
     * This function trigger in editor only, otherwise, will make confused system
     */
    public function initByContent(BuildableContentInterface $buildableContent)
    {
        $this->content = $buildableContent;

        // Init document
        $this->getDocument();

        HooksHelper::addAction('pagebuilder/adminhtml/header', [ $this,'editorHeaderTrigger' ]);

        HooksHelper::addAction('pagebuilder/adminhtml/footer', [ $this,'editorFooterTrigger' ]);

        HooksHelper::addAction('pagebuilder/adminhtml/register_scripts', [ $this,'registerScripts' ], 11);
        HooksHelper::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueStyles' ]);

        // Print out the script in footer
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this,'enqueueScripts' ]);

        HooksHelper::addFilter('pagebuilder/settings/module/ajax', [ $this,'getAjaxUrls' ]);

        HooksHelper::addAction('header', function () {
            if (DataHelper::isJsMinifyFilesEnabled() && StateHelper::isProductionMode()) {
                /** @var Config $requireJsConfig */
                $requireJsConfig = ObjectManagerHelper::get(\Magento\Framework\RequireJs\Config::class);
                echo '<script>' . $requireJsConfig->getMinResolverCode() . '</script>';
            }
        }, 9); // Should print out at first

        HooksHelper::doAction('pagebuilder/editor/init');
    }

    /**
     * Modify Ajax URLs setting
     *
     * @param $settings
     * @return mixed
     */
    public function getAjaxUrls($settings)
    {
        if (isset($settings['actions'])) {
            $storeId = (int) RequestHelper::getParam('store');
            $settings['actions']['render_widget'] = UrlBuilderHelper::getFrontendUrl(
                'pagebuilder/actions/actions',
                [
                    '_query' => [
                        EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken(
                            $this->getContent()
                        ),
                    ],
                    'store_id' => $storeId
                ]
            );
        }
        return $settings;
    }

    /**
     * @return BuildableContentInterface|null
     */
    private function getContent()
    {
        return $this->content;
    }

    /**
     * @return BuildableContentInterface|null
     */
    private function getLastRevision()
    {
        if (!$this->lastRevision && $this->getContent()) {
            $lastRevision = $this->getContent()->getLastRevision(true);
            $this->lastRevision = $lastRevision ?: false;
        }

        return $this->lastRevision ?: null;
    }

    /**
     * @return BuildableContentInterface|null
     */
    private function getCurrentRevision()
    {
        if (!$this->currentRevision && $this->getContent()) {
            $currentRevision = $this->getContent()->getCurrentRevision(true);
            $this->currentRevision = $currentRevision ?: false;
        }

        return $this->currentRevision ?: null;
    }

    /**
     * @return BuildableContentInterface|null
     */
    public function getBuildableContent()
    {
        return $this->getContent();
    }

    /**
     * @return false|AbstractDocument
     */
    public function getDocument()
    {
        if ($this->document === null) {
            $this->document = ObjectManagerHelper::getDocumentsManager()
                ->getByContent( $this->getBuildableContent() );
        }

        return $this->document;
    }

    /**
     */
    public function registerScripts()
    {
        $suffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerScript(
            'goomento-editor-modules',
            'Goomento_PageBuilder/build/editor-modules' . $suffix,
            ['goomento-common-modules']
        );

        ThemeHelper::removeScripts('jquery/ui');

        ThemeHelper::registerScript(
            'jquery/ui',
            'Goomento_PageBuilder/lib/jquery/jquery-ui.min',
            ['jquery'],
            [
                'requirejs' => [
                    'map' => [
                        '*' => [
                            'jquery-ui-modules/widget' => 'jquery/ui',
                            'jquery-ui-modules/core' => 'jquery/ui',
                            'jquery-ui-modules/accordion' => 'jquery/ui',
                            'jquery-ui-modules/autocomplete' => 'jquery/ui',
                            'jquery-ui-modules/button' => 'jquery/ui',
                            'jquery-ui-modules/datepicker' => 'jquery/ui',
                            'jquery-ui-modules/dialog' => 'jquery/ui',
                            'jquery-ui-modules/draggable' => 'jquery/ui',
                            'jquery-ui-modules/droppable' => 'jquery/ui',
                            'jquery-ui-modules/effect-blind' => 'jquery/ui',
                            'jquery-ui-modules/effect-bounce' => 'jquery/ui',
                            'jquery-ui-modules/effect-clip' => 'jquery/ui',
                            'jquery-ui-modules/effect-drop' => 'jquery/ui',
                            'jquery-ui-modules/effect-explode' => 'jquery/ui',
                            'jquery-ui-modules/effect-fade' => 'jquery/ui',
                            'jquery-ui-modules/effect-fold' => 'jquery/ui',
                            'jquery-ui-modules/effect-highlight' => 'jquery/ui',
                            'jquery-ui-modules/effect-scale' => 'jquery/ui',
                            'jquery-ui-modules/effect-pulsate' => 'jquery/ui',
                            'jquery-ui-modules/effect-shake' => 'jquery/ui',
                            'jquery-ui-modules/effect-slide' => 'jquery/ui',
                            'jquery-ui-modules/effect-transfer' => 'jquery/ui',
                            'jquery-ui-modules/effect' => 'jquery/ui',
                            'jquery-ui-modules/menu' => 'jquery/ui',
                            'jquery-ui-modules/mouse' => 'jquery/ui',
                            'jquery-ui-modules/position' => 'jquery/ui',
                            'jquery-ui-modules/progressbar' => 'jquery/ui',
                            'jquery-ui-modules/resizable' => 'jquery/ui',
                            'jquery-ui-modules/selectable' => 'jquery/ui',
                            'jquery-ui-modules/slider' => 'jquery/ui',
                            'jquery-ui-modules/sortable' => 'jquery/ui',
                            'jquery-ui-modules/spinner' => 'jquery/ui',
                            'jquery-ui-modules/tabs' => 'jquery/ui',
                            'jquery-ui-modules/tooltip' => 'jquery/ui',
                        ]
                    ]
                ]
            ]
        );

        $requirejs = [
            'map' => [
                '*' => [
                    'ko' => 'knockoutjs/knockout',
                    'knockout' => 'knockoutjs/knockout',
                    'mageUtils' => 'mage/utils/main',
                    'rjsResolver' => 'mage/requirejs/resolver',
                    'tinymce4' => 'tiny_mce_4/tinymce.min',
                    'wysiwygAdapter' => 'mage/adminhtml/wysiwyg/tiny_mce/tinymce4Adapter',
                    'translateInline' => 'mage/translate-inline',
                    'form' => 'mage/backend/form',
                    'button' => 'mage/backend/button',
                    'accordion' => 'mage/accordion',
                    'actionLink' => 'mage/backend/action-link',
                    'validation' => 'mage/backend/validation',
                    'notification' => 'mage/backend/notification',
                    'loader' => 'mage/loader_old',
                    'loaderAjax' => 'mage/loader_old',
                    'floatingHeader' => 'mage/backend/floating-header',
                    'suggest' => 'mage/backend/suggest',
                    'mediabrowser' => 'jquery/jstree/jquery.jstree',
                    'tabs' => 'mage/backend/tabs',
                    'treeSuggest' => 'mage/backend/tree-suggest',
                    'calendar' => 'mage/calendar',
                    'dropdown' => 'mage/dropdown_old',
                    'collapsible' => 'mage/collapsible',
                    'menu' => 'mage/backend/menu',
                    'jstree' => 'jquery/jstree/jquery.jstree',
                    'details' => 'jquery/jquery.details',
                    'mediaUploader' => 'Magento_Backend/js/media-uploader',
                    'mage/translate' => 'Magento_Backend/js/translate',
                    'eavInputTypes' => 'Magento_Eav/js/input-types',
                    'folderTree' => 'Magento_Cms/js/folder-tree',
                    'uiElement' => 'Magento_Ui/js/lib/core/element/element',
                    'uiCollection' => 'Magento_Ui/js/lib/core/collection',
                    'uiComponent' => 'Magento_Ui/js/lib/core/collection',
                    'uiClass' => 'Magento_Ui/js/lib/core/class',
                    'uiEvents' => 'Magento_Ui/js/lib/core/events',
                    'uiRegistry' => 'Magento_Ui/js/lib/registry/registry',
                    'consoleLogger' => 'Magento_Ui/js/lib/logger/console-logger',
                    'uiLayout' => 'Magento_Ui/js/core/renderer/layout',
                    'buttonAdapter' => 'Magento_Ui/js/form/button-adapter',
                    'tinymceDeprecated' => 'Magento_Tinymce3/tiny_mce/tiny_mce_src',
                    'escaper' =>  'Magento_Security/js/escaper',
                ]
            ],
            'shim' => [
                'extjs/ext-tree' => [
                    'prototype'
                ],
                'extjs/ext-tree-checkbox' => [
                    'extjs/ext-tree',
                    'extjs/defaults'
                ],
                'jquery/editableMultiselect/js/jquery.editable' => [
                    'jquery'
                ],
                'tiny_mce_4/tinymce.min' => [
                    'exports' => 'tinyMCE'
                ],
                'jquery/jquery-migrate' => ['jquery'],
                'jquery/jstree/jquery.hotkeys' => ['jquery'],
                'jquery/hover-intent' => ['jquery'],
                'mage/adminhtml/backup' => ['prototype'],
                'mage/new-gallery' => ['jquery'],
                'mage/webapi' => ['jquery'],
                'jquery/ui' => ['jquery'],
                'MutationObserver' => ['es6-collections'],
                'matchMedia' => [
                    'exports' => 'mediaCheck'
                ],
                'magnifier/magnifier' => ['jquery'],
                'Magento_Tinymce3/tiny_mce/tiny_mce_src' => [
                    'exports' => 'tinymce'
                ],
            ],
            'bundles' => [
                'js/theme' => [
                    'globalNavigation',
                    'globalSearch',
                    'modalPopup',
                    'useDefault',
                    'loadingPopup',
                    'collapsable',
                ]
            ],
            'deps' => [
                'js/theme',
                'mage/backend/bootstrap',
                'mage/adminhtml/globals',
                'jquery/jquery-migrate',
            ],
            'paths' => [
                'jquery/validate' => 'jquery/jquery.validate',
                'jquery/hover-intent' => 'jquery/jquery.hoverIntent',
                'jquery/file-uploader' => 'jquery/fileUploader/jquery.fileupload-fp',
                'prototype' => 'legacy-build.min',
                'jquery/jquery-storageapi' => 'Magento_Cookie/js/jquery.storageapi.extended',
                'text' => 'mage/requirejs/text',
                'domReady' => 'requirejs/domReady',
                'spectrum' => 'jquery/spectrum/spectrum',
                'tinycolor' => 'jquery/spectrum/tinycolor',
                'jquery-ui-modules' => 'jquery/ui-modules',
                'ui/template' => 'Magento_Ui/templates'
            ],
            'config' => [
                'mixins' => [
                    'jquery/jstree/jquery.jstree' => [
                        'mage/backend/jstree-mixin' => true
                    ],
                    'jquery' => [
                        'jquery/patches/jquery' => true
                    ],
                ],
                'text' => [
                    'headers' => [
                        'X-Requested-With' => 'XMLHttpRequest'
                    ]
                ]
            ]
        ];

        $magentoVersion = Configuration::magentoVersion();
        if ($magentoVersion) {
            if (version_compare($magentoVersion, '2.3.6', '>=')) {
                $requirejs['paths']['jquery/file-uploader'] = 'jquery/fileUploader/jquery.fileuploader';
            }
            if (version_compare($magentoVersion, '2.4.4', '>=')) {
                $requirejs['shim']['tiny_mce_5/tinymce.min'] = [
                    'exports' => 'tinyMCE'
                ];
                $requirejs['map']['*']['tinymce'] = 'tiny_mce_5/tinymce.min';
                $requirejs['map']['*']['wysiwygAdapter'] = 'mage/adminhtml/wysiwyg/tiny_mce/tinymce5Adapter';
            }
        }

        ThemeHelper::registerScript(
            'goomento-editor-engine',
            'Goomento_PageBuilder/build/editor' . $suffix,
            [
                'underscore',
                'jquery',
                'jquery/ui',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
                'mage/adminhtml/wysiwyg/tiny_mce/setup',
                'nouislider',
                'goomento-common',
                'goomento-editor-modules',
                'perfect-scrollbar',
                'nprogress',
                'tipsy',
                'color-picker-alpha',
                'jquery-select2',
                'flatpickr',
                'ace',
                'ace-language-tools',
                'jquery-hover-intent',
                'imagesloaded',
            ],
            [
                'requirejs' => $requirejs
            ]
        );

        ThemeHelper::registerScript(
            'goomento-editor',
            'Goomento_PageBuilder/js/editor-entry',
            [
                'nprogress',
                'nouislider',
                'swiper',
                'perfect-scrollbar'
            ]
        );

        /**
         * Before editor enqueue scripts.
         *
         * Fires before SagoTheme editor scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/before_enqueue_scripts');

        $document = $this->getDocument();

        $currentRevisionId = $this->getCurrentRevision() ? $this->getCurrentRevision()->getId() : null;

        $config = [
            'version' => Configuration::version(),
            'debug' => DataHelper::isJsDebugMode(),
            'data' => $document->getElementsRawData(),
            'document' => $document->getConfig(),
            'current_revision_id' => $currentRevisionId,
            'last_revision_id' => $currentRevisionId, // Display the current version instead of last
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
                        $document->getModel()->getOriginContent()->getRoleName('view')),
                    'save' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('save')),
                    'publish' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('publish')),
                    'export' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('export')),
                    'delete' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('delete')),
                    'import' => AuthorizationHelper::isCurrentUserCan('import'),
                    'manage_config' => AuthorizationHelper::isCurrentUserCan('manage_config')
                ]
            ],
            'rich_editing_enabled' => true,
            'dynamicTags' => ObjectManagerHelper::getTagsManager()->getConfig(),
        ];

        /**
         * Allows to modify this
         */
        $config = HooksHelper::applyFilters('pagebuilder/editor/js_variables', $config);

        DataHelper::printJsConfig('goomento-editor', 'goomentoConfig', $config);

        ObjectManagerHelper::getControlsManager()->enqueueControlScripts();

    }

    public function enqueueScripts()
    {
        ThemeHelper::enqueueScript('goomento-editor');

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
                'jquery-select2',
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
     * Trigger the header
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
        HooksHelper::addAction('pagebuilder/editor/index', [ $this, 'initByContent']); // catch trigger in controller
    }
}
