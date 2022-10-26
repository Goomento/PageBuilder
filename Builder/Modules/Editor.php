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
     * Register editor scripts
     *
     * @return void
     */
    public function beforeRegisterScripts()
    {

        $suffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone' . $suffix
        );

        ThemeHelper::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio' . $suffix,
            ['backbone']
        );

        ThemeHelper::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette' . $suffix,
            [
                'backbone',
                'backbone.radio'
            ]
        );

        ThemeHelper::registerScript(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr.min'
        );


        ThemeHelper::registerScript(
            'nouislider',
            'Goomento_PageBuilder/lib/nouislider/nouislider.min',
            ['jquery']
        );

        ThemeHelper::inlineScript('nouislider',
            "require(['nouislider'], nouislider => {window.noUiSlider = window.noUiSlider || nouislider});", 'before');

        ThemeHelper::registerScript(
            'perfect-scrollbar',
            'Goomento_PageBuilder/lib/perfect-scrollbar/js/perfect-scrollbar.min',
            ['jquery']
        );

        ThemeHelper::inlineScript('perfect-scrollbar',
            "require(['perfect-scrollbar'], PerfectScrollbar => {window.PerfectScrollbar = window.PerfectScrollbar || PerfectScrollbar});",
            'before');

        ThemeHelper::registerScript(
            'jquery-easing',
            'Goomento_PageBuilder/lib/jquery-easing/jquery-easing.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'nprogress',
            'Goomento_PageBuilder/lib/nprogress/nprogress.min'
        );

        ThemeHelper::inlineScript('nprogress',
            "require(['nprogress'], NProgress => {window.NProgress = window.NProgress || NProgress});",
            'before');


        ThemeHelper::registerScript(
            'jquery-select2',
            'Goomento_PageBuilder/lib/e-select2/js/e-select2.full.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'ace',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ace'
        );

        ThemeHelper::registerScript(
            'ace-language-tools',
            'https://cdnjs.cloudflare.com/ajax/libs/ace/1.2.5/ext-language_tools',
            ['ace']
        );

        ThemeHelper::registerScript(
            'jquery-hover-intent',
            'Goomento_PageBuilder/lib/jquery-hover-intent/jquery-hover-intent.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'iris',
            'Goomento_PageBuilder/lib/color-picker/iris.min',
            [
                'jquery',
                'jquery/ui',
            ]
        );

        ThemeHelper::registerScript(
            'image-carousel',
            'Goomento_PageBuilder/js/widgets/image-carousel'
        );

        ThemeHelper::registerScript(
            'product-slider',
            'Goomento_PageBuilder/js/widgets/product-slider'
        );
    }

    /**
     * Register the editor scripts
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
                    'loader' => 'mage/loader_old',
                    'loaderAjax' => 'mage/loader_old',
                    'mediabrowser' => 'jquery/jstree/jquery.jstree',
                    'jstree' => 'jquery/jstree/jquery.jstree',
                    'mediaUploader' => 'Magento_Backend/js/media-uploader',
                    'mage/translate' => 'Magento_Backend/js/translate',
                    'folderTree' => 'Magento_Cms/js/folder-tree',
                    'uiElement' => 'Magento_Ui/js/lib/core/element/element',
                    'uiCollection' => 'Magento_Ui/js/lib/core/collection',
                    'uiComponent' => 'Magento_Ui/js/lib/core/collection',
                    'uiClass' => 'Magento_Ui/js/lib/core/class',
                    'uiEvents' => 'Magento_Ui/js/lib/core/events',
                    'uiRegistry' => 'Magento_Ui/js/lib/registry/registry',
                    'consoleLogger' => 'Magento_Ui/js/lib/logger/console-logger',
                    'uiLayout' => 'Magento_Ui/js/core/renderer/layout',
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

        $requirejs = HooksHelper::applyFilters('pagebuilder/editor/requirejs_config', $requirejs)->getResult();

        ThemeHelper::removeScripts('underscore');
        ThemeHelper::registerScript('underscore', 'Goomento_PageBuilder/lib/underscore/underscore.min');

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
                'jquery-tipsy',
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
         * Fires before Goomento editor scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/before_enqueue_scripts');

        $document = $this->getDocument();

        $currentRevisionId = $this->getCurrentRevision() ? $this->getCurrentRevision()->getId() : null;

        $config = [
            'version' => Configuration::version(),
            'debug' => DataHelper::isDebugMode(),
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
        $config = HooksHelper::applyFilters('pagebuilder/editor/js_variables', $config)->getResult();

        DataHelper::printJsConfig('goomento-editor', 'goomentoConfig', $config);

        ObjectManagerHelper::getControlsManager()->enqueueControlScripts();

    }

    /**
     * Print the script to HTML
     *
     * @return void
     */
    public function enqueueScripts()
    {
        ThemeHelper::enqueueScript('goomento-editor');

        /**
         * After editor enqueue scripts.
         *
         * Fires after Goomento editor scripts are enqueued.
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
         * Fires before Goomento editor styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/editor/before_enqueue_styles');

        $suffix = Configuration::debug() ? '' : '.min';

        $directionSuffix = DataHelper::isRtl() ? '-rtl' : '';

        ThemeHelper::registerStyle(
            'google-font-inter',
            'https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap'
        );

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
         * Fires after Goomento editor styles are enqueued.
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
         * Goomento editor head.
         *
         * Fires on Goomento editor head tag.
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
         * Goomento editor footer.
         *
         * Fires on Goomento editor before closing the body tag.
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
        HooksHelper::addAction('pagebuilder/adminhtml/register_scripts', [$this, 'beforeRegisterScripts'], 7);
        HooksHelper::addAction('pagebuilder/editor/index', [ $this, 'initByContent']); // catch trigger in controller
    }
}
