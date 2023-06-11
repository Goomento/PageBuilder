<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Helper\Fonts;
use Goomento\PageBuilder\Builder\Base\AbstractApp;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
// phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
class Frontend extends AbstractApp
{
    const NAME = 'frontend';

    /**
     * Fonts to enqueue
     *
     * Holds the list of fonts that are being used in the current page.
     *
     *
     * @var array Used fonts. Default is an empty array.
     */
    public $fontsToEnqueue = [];

    /**
     * Registered fonts.
     *
     * Holds the list of enqueued fonts in the current page.
     *
     * @var array Registered fonts. Default is an empty array.
     */
    private $registeredFonts = [];

    /**
     *
     * @var array
     */
    private $stylesToEnqueue = ['goomento-frontend'];

    /**
     * Frontend constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/frontend/init', [$this, 'init']);
        HooksHelper::addFilter('pagebuilder/content/html', [ $this, 'applyBuilderInContent' ], 9);
    }

    /**
     * Init.
     *
     * Initialize Goomento front end. Hooks the needed actions to run Goomento
     * in the front end, including script and style registration.
     *
     *
     */
    public function init()
    {
        HooksHelper::addAction('pagebuilder/frontend/register_scripts', [$this, 'registerScripts'], 9);
        HooksHelper::addAction('pagebuilder/frontend/register_styles', [$this, 'registerStyles'], 9);
        HooksHelper::addAction('pagebuilder/frontend/header', [ $this, 'header' ], 9);
        HooksHelper::addAction('pagebuilder/frontend/footer', [ $this, 'footer' ], 9);
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
        /**
         * Before frontend register scripts.
         *
         * Fires before Goomento frontend scripts are registered.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/before_register_scripts');

        ThemeHelper::registerScript(
            'goomento-frontend-modules',
            'Goomento_PageBuilder/build/frontend-modules',
            [
                'jquery',
                'jquery/ui',
            ]
        );

        ThemeHelper::registerScript(
            'goomento-frontend-engine',
            'Goomento_PageBuilder/build/frontend',
            [
                'jquery',
                'dialogs-manager',
                'jquery-waypoints',
                'goomento-frontend-modules',
            ]
        );

        ThemeHelper::registerScript(
            'goomento-frontend',
            'Goomento_PageBuilder/js/frontend-entry',
            [
                'underscore'
            ]
        );

        $this->printConfig();

        /**
         * After frontend register scripts.
         *
         * Fires after Goomento frontend scripts are registered.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/after_register_scripts');
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
        /**
         * Before frontend register styles.
         *
         * Fires before Goomento frontend styles are registered.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/before_register_styles');

        ThemeHelper::registerStyle(
            'goomento-frontend',
            'Goomento_PageBuilder/build/frontend.css'
        );

        /**
         * After frontend register styles.
         *
         * Fires after Goomento frontend styles are registered.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/after_register_styles');
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
         * Fires before Goomento frontend scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/before_enqueue_scripts');

        ThemeHelper::enqueueScript('goomento-frontend');

        /**
         * After frontend enqueue scripts.
         *
         * Fires after Goomento frontend scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/after_enqueue_scripts');
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
         * Fires before Goomento frontend styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/before_enqueue_styles');


        foreach ($this->stylesToEnqueue as $style) {
            ThemeHelper::enqueueStyle($style);
        }

        /**
         * After frontend styles enqueued.
         *
         * Fires after Goomento frontend styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/frontend/after_enqueue_styles');

        if (!StateHelper::isCanvasMode()) {
            $this->parseGlobalCssCode();
        }
    }

    /**
     * Run header hook
     */
    public function header()
    {
        $this->enqueueStyles();
    }

    /**
     * Run footer hook
     */
    public function footer()
    {
        $this->enqueueScripts();
        if (!DataHelper::isLocalFont()) {
            $this->printFontsLinks();
        }
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
        $googleFonts = [
            'google' => [],
            'early' => [],
        ];

        foreach ($this->fontsToEnqueue as $key => $font) {
            $fontType = Fonts::getFontType($font);

            switch ($fontType) {
                case Fonts::GOOGLE:
                    $googleFonts['google'][] = $font;
                    break;

                case Fonts::EARLYACCESS:
                    $googleFonts['early'][] = $font;
                    break;

                case false:
                    break;
                default:
                    /**
                     * Print font links.
                     *
                     * Fires when Goomento frontend fonts are printed on the HEAD tag.
                     *
                     * The dynamic portion of the hook name, `$fontType`, refers to the font type.
                     *
                     *
                     * @param string $font Font name.
                     */
                    HooksHelper::doAction("pagebuilder/fonts/print_font_links/{$fontType}", $font);
            }
        }
        $this->fontsToEnqueue = [];

        $this->enqueueGoogleFonts($googleFonts);
    }

    /**
     * Print Google fonts.
     *
     * Enqueue all the frontend Google fonts.
     *
     *
     * @param array $googleFonts Optional. Google fonts to print in the frontend.
     *                            Default is an empty array.
     */
    private function enqueueGoogleFonts($googleFonts = [])
    {
        static $googleFontsIndex = 0;

        /**
         * Print frontend google fonts.
         *
         * Filters whether to enqueue Google fonts in the frontend.
         *
         *
         * @param bool $printGoogleFonts Whether to enqueue Google fonts. Default is true.
         */
        $printGoogleFonts = HooksHelper::applyFilters('pagebuilder/frontend/print_google_fonts', true)->getResult();

        if (!$printGoogleFonts) {
            return;
        }

        // Print used fonts
        if (!empty($googleFonts['google'])) {
            $googleFontsIndex++;

            foreach ($googleFonts['google'] as &$font) {
                $font = str_replace(' ', '+', $font) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
            }

            $fontsUrl = sprintf('https://fonts.googleapis.com/css?family=%s', implode(rawurlencode('|'), $googleFonts['google']));

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
                $fontsUrl .= '&subset=' . $subsets[ $locale ];
            }

            $fontsUrl .= '&display=swap';

            ThemeHelper::registerStyle('google-fonts-' . $googleFontsIndex, $fontsUrl);
            ThemeHelper::enqueueStyle('google-fonts-' . $googleFontsIndex);
        }

        if (!empty($googleFonts['early'])) {
            foreach ($googleFonts['early'] as $currentFont) {
                $googleFontsIndex++;

                $fontUrl = sprintf('https://fonts.googleapis.com/earlyaccess/%s.css', strtolower(str_replace(' ', '', $currentFont)));

                ThemeHelper::registerStyle('google-earlyaccess-' . $googleFontsIndex, $fontUrl);
                ThemeHelper::enqueueStyle('google-earlyaccess-' . $googleFontsIndex);
            }
        }
    }

    /**
     * Enqueue fonts.
     *
     * Enqueue all the frontend fonts.
     *
     *
     * @param string $font Fonts to enqueue in the frontend.
     */
    public function enqueueFont(string $font)
    {
        if (in_array($font, $this->registeredFonts)) {
            return;
        }

        $this->fontsToEnqueue[] = $font;
        $this->registeredFonts[] = $font;
    }

    /**
     * Parse global CSS.
     *
     * Enqueue the global CSS file.
     *
     */
    protected function parseGlobalCssCode()
    {
        $globalCss = new GlobalCss();
        $globalCss->enqueue();
    }

    /**
     * Apply builder in content.
     *
     * Used to apply the Goomento page editor on the post content.
     *
     * @param BuildableContentInterface $content The content.
     *
     * @return string The content HTML.
     * @throws BuilderException
     */
    public function applyBuilderInContent(BuildableContentInterface $content)
    {
        if (StateHelper::isCanvasMode()) {
            return '';
        }

        return $this->getBuilderContent($content);
    }

    /**
     * Retrieve builder content.
     *
     * Used to render and return the post content with all the Goomento elements.
     *
     * Note that this method is an internal method, please use `get_builder_content_for_display()`.
     *
     * @param BuildableContentInterface $buildableContent
     * @return string The content.
     * @throws BuilderException
     */
    public function getBuilderContent(BuildableContentInterface $buildableContent)
    {
        if ($buildableContent->getFlag('is_rendering_content') === true) {
            throw new BuilderException('Page Builder renderer looping detected');
        }

        $buildableContent->setFlag('is_rendering_content', true);

        $document = ObjectManagerHelper::getDocumentsManager()->getByContent(
            $buildableContent
        );

        $data = $document->getElementsData();

        /**
         * Frontend builder content data.
         *
         * Filters the builder content in the frontend.
         *
         *
         * @param array $data    The builder content.
         * @param int   $contentId The conent ID.
         */
        $data = HooksHelper::applyFilters('pagebuilder/frontend/builder_content_data', $data, $buildableContent)->getResult();

        if (empty($data)) {
            return '';
        }

        $cssFile = new ContentCss($buildableContent);

        ob_start();

        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$cssFile, 'enqueue']);

        if (RequestHelper::isAjax()) {
            $cssFile->printCss();
        }

        $document->printElementsWithWrapper($data);

        $html = ob_get_clean();

        /**
         * Frontend content.
         *
         * Filters the content in the frontend.
         *
         *
         * @param string $content The content.
         */
        $html = HooksHelper::applyFilters('pagebuilder/the_content', $html)->getResult();

        if (strpos($html, 'animation') !== false) {
            $this->stylesToEnqueue['goomento-animations'] = 'goomento-animations';
        }

        if (strpos($html, 'fa-') !== false) {
            $this->stylesToEnqueue['fontawesome'] = 'fontawesome';
        }

        $buildableContent->setFlag('is_rendering_content', false);

        return $html;
    }

    /**
     * Get Init AbstractSettings
     *
     * Used to define the default/initial settings of the object. Inheriting classes may implement this method to define
     * their own default/initial settings.
     *
     * @return array
     */
    protected function getInitSettings()
    {
        $isPreviewMode = StateHelper::isCanvasMode();

        $settings = [
            'environmentMode' => [
                'edit' => $isPreviewMode,
            ],
            'is_rtl' => DataHelper::isRtl(),
            'breakpoints' => Developer::DEFAULT_BREAKPOINTS,
            'version' => Developer::version(),
            'urls' => [
                'assets' => UrlBuilderHelper::getAssetUrlWithParams('Goomento_PageBuilder') . '/',
            ],
        ];

        $settings['settings'] = ObjectManagerHelper::getSettingsManager()->getSettingsFrontendConfig();

        $emptyObject = (object) [];

        if ($isPreviewMode) {
            $settings['elements'] = [
                'data' => $emptyObject,
                'editSettings' => $emptyObject,
                'keys' => $emptyObject,
            ];
        }

        /**
         * Allows to modify this
         */
        return HooksHelper::applyFilters('pagebuilder/frontend/js_variables', $settings)->getResult();
    }
}
