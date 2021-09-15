<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\Base\App;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Core\Files\Css\ContentCss as PostCss;
use Goomento\PageBuilder\Core\Files\Css\GlobalCss;
use Goomento\PageBuilder\Core\Responsive\Files\Frontend as FrontendFile;
use Goomento\PageBuilder\Core\Responsive\Responsive;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\StaticUtils;
use Goomento\PageBuilder\Helper\Theme;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Frontend
 * @package Goomento\PageBuilder\Builder
 */
class Frontend extends App
{

    /**
     * The priority of the content filter.
     */
    const THE_CONTENT_FILTER_PRIORITY = 9;

    /**
     * ContentCss ID.
     *
     * Holds the ID of the current post.
     *
     * @var int ContentCss ID.
     */
    private $post_id;

    /**
     * Fonts to enqueue
     *
     * Holds the list of fonts that are being used in the current page.
     *
     *
     * @var array Used fonts. Default is an empty array.
     */
    public $fonts_to_enqueue = [];

    /**
     * Registered fonts.
     *
     * Holds the list of enqueued fonts in the current page.
     *
     * @var array Registered fonts. Default is an empty array.
     */
    private $registered_fonts = [];

    /**
     * Icon Fonts to enqueue
     *
     * Holds the list of Icon fonts that are being used in the current page.
     *
     * @var array Used icon fonts. Default is an empty array.
     */
    private $icon_fonts_to_enqueue = [];

    /**
     * Enqueue Icon Fonts
     *
     * Holds the list of Icon fonts already enqueued  in the current page.
     *
     *
     * @var array enqueued icon fonts. Default is an empty array.
     */
    private $enqueued_icon_fonts = [];


    /**
     * Filters removed from the content.
     *
     * @var array Filters removed from the content. Default is an empty array.
     */
    private $content_removed_filters = [];

    /**
     * @var string[]
     */
    private $body_classes = [
        'goomento-default',
    ];

    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        if (!StaticUtils::isAdminhtml() || StaticUtils::isAjax()) {
            Hooks::addAction('pagebuilder/frontend/init', [$this, 'init']);
            Hooks::addAction('pagebuilder/frontend/register_scripts', [$this, 'registerScripts']);
            Hooks::addAction('pagebuilder/frontend/register_styles', [$this, 'registerStyles']);

            $this->addContentFilter();
        }
    }

    /**
     * Get module name.
     *
     * Retrieve the module name.
     *
     *
     * @return string Module name.
     */
    public function getName()
    {
        return 'frontend';
    }

    /**
     * Init.
     *
     * Initialize SagoTheme front end. Hooks the needed actions to run SagoTheme
     * in the front end, including script and style registration.
     *
     *
     */
    public function init()
    {
        /** @var Editor $editor */
        $editor = StaticObjectManager::get(Editor::class);
        if ($editor->isEditMode()) {
            return;
        }

        Hooks::addFilter('body_class', [ $this, 'bodyClass' ]);

        /** @var Preview $preview */
        $preview = StaticObjectManager::get(Preview::class);
        if ($preview->isPreviewMode()) {
            return;
        }

        /** @var PageBuilder $builderController */
        $builderController = StaticObjectManager::get(PageBuilder::class);
        $builderController->initCommon();

        Hooks::addAction('pagebuilder/frontend/enqueue_scripts', [ $this, 'enqueueStyles' ]);

        // Priority 7 to allow google fonts in header template to load in <head> tag
        Hooks::addAction('pagebuilder/frontend/footer', [ $this, 'printFontsLinks' ], 7);
        Hooks::addAction('pagebuilder/frontend/footer', [ $this, 'footer' ]);
    }

    /**
     * @param string|array $class
     */
    public function addBodyClass($class)
    {
        if (is_array($class)) {
            $this->body_classes = array_merge($this->body_classes, $class);
        } else {
            $this->body_classes[] = $class;
        }
    }

    /**
     * Body tag classes.
     *
     *
     * Fired by `body_class` filter.
     *
     * @param array $classes Optional. One or more classes to add to the body tag class list.
     *                       Default is an empty array.
     *
     * @return array Body tag classes.
     *
     */
    public function bodyClass($classes = [])
    {
        $classes = array_merge($classes, $this->body_classes);

        $id = '5';

        $classes[] = 'gmt-page';
        $classes[] = 'gmt-page-' . $id;

        return $classes;
    }

    /**
     * Add content filter.
     *
     * Remove plain content and render the content generated by SagoTheme.
     *
     */
    public function addContentFilter()
    {
        Hooks::addFilter('pagebuilder/content/html', [ $this, 'applyBuilderInContent' ], self::THE_CONTENT_FILTER_PRIORITY);
    }

    /**
     * Remove content filter.
     *
     * When the SagoTheme generated content rendered, we remove the filter to prevent multiple
     * accuracies. This way we make sure SagoTheme renders the content only once.
     *
     */
    public function removeContentFilter()
    {
        Hooks::removeFilter('pagebuilder/content/html', [ $this, 'applyBuilderInContent' ], self::THE_CONTENT_FILTER_PRIORITY);
    }

    /**
     * Registers scripts.
     *
     * Registers all the frontend scripts.
     *
     * Fired by `pagebuilder/footer` action.
     *
     */
    public function registerScripts()
    {
        $min_suffix = Utils::isScriptDebug() ? '' : '.min';

        /**
         * Before frontend register scripts.
         *
         * Fires before SagoTheme frontend scripts are registered.
         *
         */
        Hooks::doAction('pagebuilder/frontend/before_register_scripts');

        Theme::registerScript(
            'goomento-frontend-modules',
            'Goomento_PageBuilder/js/frontend-modules' . $min_suffix,
            [
                'jquery',
                'jquery/ui',
            ]
        );

        Theme::registerScript(
            'goomento-waypoints',
            'Goomento_PageBuilder/lib/waypoints/waypoints' . $min_suffix,
            [],
            '4.0.2'
        );

        Theme::registerScript(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr' . $min_suffix,
            [],
            '4.1.4'
        );

        Theme::registerScript(
            'goomento-dialog',
            'Goomento_PageBuilder/lib/dialog/dialog' . $min_suffix,
            [
            ],
            '4.7.3'
        );

        Theme::registerScript(
            'goomento-gallery',
            'Goomento_PageBuilder/lib/e-gallery/js/e-gallery' . $min_suffix,
            [],
            '1.0.2'
        );

        Theme::registerScript(
            'underscore',
            'Goomento_PageBuilder/lib/underscore/underscore'
        );

        Theme::registerStyle(
            'hover-animation',
            'Goomento_PageBuilder/lib/hover/hover.min.css'
        );

        Theme::registerScript(
            'goomento-frontend-engine',
            'Goomento_PageBuilder/js/frontend' . $min_suffix,
            [
                'underscore',
                'jquery',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
                'goomento-frontend-modules',
                'goomento-dialog',
                'goomento-waypoints',
                'pagebuilderRegister',
            ]
        );

        Theme::registerScript(
            'goomento-frontend',
            'Goomento_PageBuilder/js/frontend-entry',
            [],
            null,
            true
        );

        /**
         * After frontend register scripts.
         *
         * Fires after SagoTheme frontend scripts are registered.
         *
         */
        Hooks::doAction('pagebuilder/frontend/after_register_scripts');
    }

    /**
     * Registers styles.
     *
     * Registers all the frontend styles.
     *
     * Fired by `pagebuilder/footer` action.
     *
     */
    public function registerStyles()
    {

        $min_suffix = Configuration::DEBUG ? '' : '.min';

        /**
         * Before frontend register styles.
         *
         * Fires before SagoTheme frontend styles are registered.
         *
         */
        Hooks::doAction('pagebuilder/frontend/before_register_styles');


        Theme::registerStyle(
            'font-awesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all' . $min_suffix . '.css',
            []
        );

        Theme::registerStyle(
            'goomento-animations',
            'Goomento_PageBuilder/lib/animations/animations.min.css',
            []
        );

        Theme::registerStyle(
            'flatpickr',
            'Goomento_PageBuilder/lib/flatpickr/flatpickr'  . $min_suffix . '.css',
            [],
            '4.1.4'
        );

        Theme::registerStyle(
            'goomento-gallery',
            'Goomento_PageBuilder/lib/e-gallery/css/e-gallery'  . $min_suffix . '.css',
            [],
            '1.0.2'
        );

        $direction_suffix = StaticData::isRtl() ? '-rtl' : '';

        $frontend_file_name = 'frontend' . $direction_suffix . $min_suffix . '.css';

        Theme::registerStyle(
            'goomento-widgets',
            'Goomento_PageBuilder/css/widgets' . $direction_suffix . $min_suffix . '.css',
            ['goomento-frontend']
        );

        $has_custom_file = Responsive::hasCustomBreakpoints();
        if ($has_custom_file) {
            $frontend_file = new FrontendFile('custom-' . $frontend_file_name, Responsive::getStylesheetTemplatesPath() . $frontend_file_name);

            $time = $frontend_file->getMeta('time');

            if (! $time) {
                $frontend_file->update();
            }

            $frontend_file_url = $frontend_file->getUrl();
        } else {
            $frontend_file_url = 'Goomento_PageBuilder::css/' . $frontend_file_name;
        }

        Theme::registerStyle(
            'goomento-frontend',
            $frontend_file_url,
            [],
            $has_custom_file ? null : Configuration::VERSION
        );

        /**
         * After frontend register styles.
         *
         * Fires after SagoTheme frontend styles are registered.
         *
         */
        Hooks::doAction('pagebuilder/frontend/after_register_styles');
    }

    /**
     * Enqueue scripts.
     *
     * Enqueue all the frontend scripts.
     *
     */
    public function enqueueScripts()
    {
        /**
         * Before frontend enqueue scripts.
         *
         * Fires before SagoTheme frontend scripts are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/frontend/before_enqueue_scripts');

        Theme::enqueueScript('goomento-frontend');

        $this->printConfig();

        /**
         * After frontend enqueue scripts.
         *
         * Fires after SagoTheme frontend scripts are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/frontend/after_enqueue_scripts');
    }

    /**
     * Enqueue styles.
     *
     * Enqueue all the frontend styles.
     *
     * Fired by `pagebuilder/footer` action.
     *
     */
    public function enqueueStyles()
    {
        /**
         * Before frontend styles enqueued.
         *
         * Fires before SagoTheme frontend styles are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/frontend/before_enqueue_styles');


        Theme::enqueueStyle('goomento-animations');
        Theme::enqueueStyle('goomento-frontend');

        /**
         * After frontend styles enqueued.
         *
         * Fires after SagoTheme frontend styles are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/frontend/after_enqueue_styles');
        /** @var Preview $preview */
        $preview = StaticObjectManager::get(Preview::class);
        if (! $preview->isPreviewMode()) {
            $this->parseGlobalCssCode();

            //			$post_id = get_the_ID();
            // Check $post_id for virtual pages. check is singular because the $post_id is set to the first post on archive pages.
            // TODO check this
//			if ( Request::getPageData() ) {
//				$css_file = \Goomento\PageBuilder\Core\Files\Css\ContentCss::create( Request::getPageData() );
//				$css_file->enqueue();
//			}
        }
    }

    /**
     * Run footer hook
     */
    public function footer()
    {
        $this->enqueueStyles();
        $this->enqueueScripts();

        $this->printFontsLinks();
    }

    /**
     * Print fonts links.
     *
     * Enqueue all the frontend fonts by url.
     *
     *
     */
    public function printFontsLinks()
    {
        $google_fonts = [
            'google' => [],
            'early' => [],
        ];

        foreach ($this->fonts_to_enqueue as $key => $font) {
            $font_type = Fonts::getFontType($font);

            switch ($font_type) {
                case Fonts::GOOGLE:
                    $google_fonts['google'][] = $font;
                    break;

                case Fonts::EARLYACCESS:
                    $google_fonts['early'][] = $font;
                    break;

                case false:
                    break;
                default:
                    /**
                     * Print font links.
                     *
                     * Fires when SagoTheme frontend fonts are printed on the HEAD tag.
                     *
                     * The dynamic portion of the hook name, `$font_type`, refers to the font type.
                     *
                     *
                     * @param string $font Font name.
                     */
                    Hooks::doAction("pagebuilder/fonts/print_font_links/{$font_type}", $font);
            }
        }
        $this->fonts_to_enqueue = [];

        $this->enqueueGoogleFonts($google_fonts);
        $this->enqueueIconFonts();
    }

    /**
     *
     */
    private function enqueueIconFonts()
    {
        if (empty($this->icon_fonts_to_enqueue)) {
            return;
        }

        foreach ($this->icon_fonts_to_enqueue as $icon_type => $css_url) {
            Theme::enqueueStyle('goomento-icons-' . $icon_type);
            $this->enqueued_icon_fonts[] = $css_url;
        }

        //clear enqueued icons
        $this->icon_fonts_to_enqueue = [];
    }

    /**
     * Print Google fonts.
     *
     * Enqueue all the frontend Google fonts.
     *
     *
     * @param array $google_fonts Optional. Google fonts to print in the frontend.
     *                            Default is an empty array.
     */
    private function enqueueGoogleFonts($google_fonts = [])
    {
        static $google_fonts_index = 0;

        $print_google_fonts = true;

        /**
         * Print frontend google fonts.
         *
         * Filters whether to enqueue Google fonts in the frontend.
         *
         *
         * @param bool $print_google_fonts Whether to enqueue Google fonts. Default is true.
         */
        $print_google_fonts = Hooks::applyFilters('pagebuilder/frontend/print_google_fonts', $print_google_fonts);

        if (! $print_google_fonts) {
            return;
        }

        // Print used fonts
        if (! empty($google_fonts['google'])) {
            $google_fonts_index++;

            foreach ($google_fonts['google'] as &$font) {
                $font = str_replace(' ', '+', $font) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
            }

            $fonts_url = sprintf('https://fonts.googleapis.com/css?family=%s', implode(rawurlencode('|'), $google_fonts['google']));

            $subsets = [
                'ru_RU' => 'cyrillic',
                'bg_BG' => 'cyrillic',
                'he_IL' => 'hebrew',
                'el' => 'greek',
                'vi' => 'vietnamese',
                'uk' => 'cyrillic',
                'cs_CZ' => 'latin-ext',
                'ro_RO' => 'latin-ext',
                'pl_PL' => 'latin-ext',
            ];
            $locale = 'EN-en';

            if (isset($subsets[ $locale ])) {
                $fonts_url .= '&subset=' . $subsets[ $locale ];
            }

            $fonts_url .= '&display=swap';

            Theme::enqueueStyle('google-fonts-' . $google_fonts_index, $fonts_url);
        }

        if (! empty($google_fonts['early'])) {
            foreach ($google_fonts['early'] as $current_font) {
                $google_fonts_index++;

                $font_url = sprintf('https://fonts.googleapis.com/earlyaccess/%s.css', strtolower(str_replace(' ', '', $current_font)));

                Theme::enqueueStyle('google-earlyaccess-' . $google_fonts_index, $font_url);
            }
        }
    }

    /**
     * Enqueue fonts.
     *
     * Enqueue all the frontend fonts.
     *
     *
     * @param array $font Fonts to enqueue in the frontend.
     */
    public function enqueueFont($font)
    {
        if (in_array($font, $this->registered_fonts)) {
            return;
        }

        $this->fonts_to_enqueue[] = $font;
        $this->registered_fonts[] = $font;
    }

    /**
     * Parse global CSS.
     *
     * Enqueue the global CSS file.
     *
     */
    protected function parseGlobalCssCode()
    {
        /** @var GlobalCss $scheme_css_file */
        $scheme_css_file = StaticObjectManager::create(GlobalCss::class);
        $scheme_css_file->enqueue();
    }

    /**
     * Apply builder in content.
     *
     * Used to apply the SagoTheme page editor on the post content.
     *
     * @param string $content The post content.
     *
     * @return string The post content.
     * @throws LocalizedException
     */
    public function applyBuilderInContent($content)
    {
        $this->restoreContentFilters();
        /** @var Preview $preview */
        $preview = StaticObjectManager::get(Preview::class);
        if ($preview->isPreviewMode()) {
            return $content;
        }

        $this->removeContentFilter();


        $contentId = Hooks::applyFilters('pagebuilder/current/content_id');
        $builderContent = $this->getBuilderContent($contentId);

        if (!empty($builderContent)) {
            $content = $builderContent;
        }

        $this->addContentFilter();

        return $content;
    }

    /**
     * Retrieve builder content.
     *
     * Used to render and return the post content with all the SagoTheme elements.
     *
     * Note that this method is an internal method, please use `get_builder_content_for_display()`.
     *
     * @param int $contentId The content ID.
     * @param bool $with_css Optional. Whether to retrieve the content with CSS
     *                       or not. Default is false.
     *
     * @return string The post content.
     */
    public function getBuilderContent($contentId, $with_css = false)
    {
        /** @var DocumentsManager $documentsManager */
        $documentsManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentsManager->get($contentId);

        $data = $document->getElementsData();

        /**
         * Frontend builder content data.
         *
         * Filters the builder content in the frontend.
         *
         *
         * @param array $data    The builder content.
         * @param int   $contentId The post ID.
         */
        $data = Hooks::applyFilters('pagebuilder/frontend/builder_content_data', $data, $contentId);

        if (empty($data)) {
            return '';
        }

        /** @var PostCss $css_file */
        $css_file = StaticObjectManager::create(PostCss::class, ['contentId' => $document->getContentModel()->getId()]);

        ob_start();

        $css_file->enqueue();

        // Handle JS and Customizer requests, with CSS inline.
        if (StaticUtils::isAjax()) {
            $with_css = true;
        }

        if (! empty($css_file) && $with_css) {
            $css_file->printCss();
        }

        $document->printElementsWithWrapper($data);

        $content = ob_get_clean();

        /**
         * Frontend content.
         *
         * Filters the content in the frontend.
         *
         *
         * @param string $content The content.
         */
        $content = Hooks::applyFilters('pagebuilder/the_content', $content);

        if (!empty($content)) {
            $this->_has_goomento_in_page = true;
        }

        $documentsManager->restoreDocument();

        return $content;
    }

    /**
     * Get Init Settings
     *
     * Used to define the default/initial settings of the object. Inheriting classes may implement this method to define
     * their own default/initial settings.
     *
     * @return array
     */
    protected function getInitSettings()
    {
        /** @var Preview $preview */
        $preview = StaticObjectManager::get(Preview::class);
        $is_preview_mode = $preview->isPreviewMode();

        $settings = [
            'environmentMode' => [
                'edit' => $is_preview_mode,
            ],
            'is_rtl' => StaticData::isRtl(),
            'breakpoints' => Responsive::getBreakpoints(),
            'version' => Configuration::VERSION,
            'urls' => [
                'assets' => StaticUrlBuilder::urlStaticBuilder('Goomento_PageBuilder') . '/',
            ],
        ];

        $settings['settings'] = SettingsManager::getSettingsFrontendConfig();

        $empty_object = (object) [];

        if ($is_preview_mode) {
            $settings['elements'] = [
                'data' => $empty_object,
                'editSettings' => $empty_object,
                'keys' => $empty_object,
            ];
        }

        return $settings;
    }

    /**
     * Restore content filters.
     */
    private function restoreContentFilters()
    {
        foreach ($this->content_removed_filters as $filter) {
            Hooks::addFilter('pagebuilder/content/html', $filter);
        }

        $this->content_removed_filters = [];
    }
}
