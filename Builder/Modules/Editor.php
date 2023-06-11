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
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\Shapes;
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

// phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
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
     * Stores the template variables
     * @var array
     */
    private $templateVariables;

    /**
     * Init the editor
     * This function trigger in editor only, otherwise, will make confused system
     */
    public function constructByContent(BuildableContentInterface $buildableContent)
    {
        $this->content = $buildableContent;

        // Init document
        $this->getDocument();

        HooksHelper::addAction('header', [$this, 'editorHeader'], 0);

        HooksHelper::addAction('pagebuilder/adminhtml/header', [ $this,'editorHeaderTrigger' ]);
        HooksHelper::addAction('pagebuilder/adminhtml/footer', [ $this,'editorFooterTrigger' ]);
        HooksHelper::addAction('pagebuilder/adminhtml/register_scripts', [ $this,'registerScripts' ], 11);
        HooksHelper::addAction('pagebuilder/adminhtml/enqueue_scripts', [ $this,'enqueueStyles' ]);
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this,'enqueueScripts' ]);
        HooksHelper::addFilter('pagebuilder/settings/module/ajax', [ $this,'getAjaxUrls' ]);
        HooksHelper::doAction('pagebuilder/editor/init');
    }

    /**
     * Prepare initial resources
     *
     * @return void
     */
    public function editorHeader() : void
    {
        $params = array_merge(['_secure' => RequestHelper::isSecure()]);
        $cssPrefix = DataHelper::isCssMinifyFilesEnabled() ? '.min' : '';
        // Requirejs
        echo '<script src="' . UrlBuilderHelper::getAssetUrlWithParams('Goomento_PageBuilder/lib/requirejs/require.min.js', $params) . '"></script>';
        // Global style
        echo '<link rel="stylesheet" href="' . UrlBuilderHelper::getAssetUrlWithParams('Goomento_Core::css/style-m' . $cssPrefix . '.css', $params) . '" />';
        // Icon
        echo '<link rel="shortcut icon" type="image/x-icon" href="' . UrlBuilderHelper::getAssetUrlWithParams('Goomento_Core/images/goomento.ico', $params) . '" />';

        if (DataHelper::isJsMinifyFilesEnabled() && StateHelper::isProductionMode()) {
            /** @var Config $requireJsConfig */
            $requireJsConfig = ObjectManagerHelper::get(\Magento\Framework\RequireJs\Config::class);

            echo '<script>' . $requireJsConfig->getMinResolverCode() . '</script>';
        }
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
                    EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken(
                        $this->getContent()
                    ),
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
            $this->document = ObjectManagerHelper::getDocumentsManager()->getByContent($this->getBuildableContent());
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
        ThemeHelper::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone'
        );

        ThemeHelper::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio',
            ['backbone']
        );

        ThemeHelper::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette',
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
            'Goomento_PageBuilder/js/view/nouislider-wrapper',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'perfect-scrollbar',
            'Goomento_PageBuilder/js/view/perfect-scrollbar-wrapper',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'jquery-easing',
            'Goomento_PageBuilder/lib/jquery-easing/jquery-easing.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'nprogress',
            'Goomento_PageBuilder/js/view/nprogress-wrapper'
        );

        ThemeHelper::registerScript(
            'jquery-select2',
            'Goomento_PageBuilder/lib/e-select2/js/e-select2.full.min',
            ['jquery']
        );

        ThemeHelper::registerScript(
            'ace',
            'Goomento_PageBuilder/js/view/ace-wrapper'
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
            'introjs',
            'Goomento_PageBuilder/js/view/intro-wrapper'
        );

        ThemeHelper::registerScript(
            'editor-introduction',
            'Goomento_PageBuilder/js/editor-introduction'
        );
    }

    /**
     * Register the editor scripts
     */
    public function registerScripts()
    {
        ThemeHelper::registerScript(
            'goomento-editor-modules',
            'Goomento_PageBuilder/build/editor-modules',
            ['goomento-common-modules']
        );

        ThemeHelper::removeScripts('underscore');
        ThemeHelper::registerScript('underscore', 'Goomento_PageBuilder/lib/underscore/underscore.1.8.3.min');

        ThemeHelper::removeScripts('jquery');
        ThemeHelper::registerScript('jquery', 'Goomento_PageBuilder/lib/jquery/jquery-3.6.0.min');

        ThemeHelper::registerScript('tinymce', '//cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min');

        ThemeHelper::removeScripts('jquery/ui');
        ThemeHelper::registerScript(
            'jquery/ui',
            'Goomento_PageBuilder/lib/jquery/jquery-ui.1.13.2.min',
            [
                'jquery',
                'tinymce'
            ]
        );

        ThemeHelper::registerScript(
            'goomento-editor-engine',
            'Goomento_PageBuilder/build/editor',
            [
                'underscore',
                'jquery',
                'jquery/ui',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
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
                'jquery-hover-intent',
                'imagesloaded',
                'editor-introduction',
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
            'version' => Developer::version(),
            'debug' => Developer::debug(),
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
            'additional_shapes' => Shapes::getAdditionalShapesForConfig(),
            'user' => [
                'roles' => [
                    'view' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('view')
                    ),
                    'save' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('save')
                    ),
                    'publish' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('publish')
                    ),
                    'export' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('export')
                    ),
                    'delete' => AuthorizationHelper::isCurrentUserCan(
                        $document->getModel()->getOriginContent()->getRoleName('delete')
                    ),
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

        ThemeHelper::registerStyle(
            'google-font-inter',
            'https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700&display=swap'
        );

        ThemeHelper::registerStyle(
            'goomento-editor',
            'Goomento_PageBuilder/build/editor.css',
            [
                'goomento-common',
                'jquery-select2',
                'google-font-inter',
                'flatpickr',
                'fontawesome',
                'introjs',
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
     * @return void
     */
    private function initTemplateVariables() : void
    {
        if (null !== $this->templateVariables) {
            return;
        }

        $this->templateVariables = [
            'is_editor' => StateHelper::isEditorMode(),
            'is_buildable' => StateHelper::isBuildable(),
            'is_adminhtml' => StateHelper::isAdminhtml(),
            'is_frontend' => StateHelper::isFrontend(),
        ];

        $this->templateVariables['is_live'] = !$this->templateVariables['is_buildable'];
    }

    /**
     * @param string $content
     * @return string
     */
    public function filterTemplate(string $content) : string
    {
        $pattern = '/\s*<!--\s(gmt_state):(\S+)\s-->(.*)?<!--\s\/(gmt_state):\2\s-->/sU';

        $result = preg_replace_callback($pattern, function ($matches) {
            $this->initTemplateVariables();
            list(, $tag, $type, $snippet) = $matches;
            $test = $this->templateVariables[$type] ?? false;
            if (true === $test) {
                return $snippet;
            } else {
                return '';
            }
        }, $content);

        if (null !== $result) {
            $content = $result;
        }
        return $content;
    }

    /**
     * Editor constructor.
     */
    public function __construct()
    {
        /**
         * Ready to catch the custom trigger by controller
         */
        HooksHelper::addAction('pagebuilder/editor/index', [ $this, 'constructByContent']);

        HooksHelper::addAction('pagebuilder/adminhtml/register_scripts', [$this, 'beforeRegisterScripts'], 7);
        HooksHelper::addFilter("pagebuilder/widget/render_content", [$this, 'filterTemplate'], 9);
    }
}
