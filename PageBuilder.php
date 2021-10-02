<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Goomento\Core\SubSystemInterface;
use Goomento\PageBuilder\Core\Common\App as CommonApp;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticState;
use Goomento\PageBuilder\Helper\Theme;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class BuilderController
 * @package Goomento\PageBuilder
 */
class PageBuilder implements SubSystemInterface
{
    /**
     * @var bool
     */
    private $init = false;

    /**
     * @var Helper\Data
     */
    protected $dataHelper;

    /**
     * @var array
     */
    private $components;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * BuilderController constructor.
     * @param Helper\Data $dataHelper
     * @param ObjectManagerInterface $objectManager
     * @param array $components
     */
    public function __construct(
        Helper\Data $dataHelper,
        ObjectManagerInterface $objectManager,
        array $components = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->components = $components;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function init(array $buildSubject = [])
    {
        if (false === $this->init) {
            $this->init = true;

            Hooks::doAction('pagebuilder/construct');

            $this->initComponents($buildSubject);

            Hooks::addAction('init', function () {
                self::registerHooks();
            });
        }
    }

    /**
     * Initialize the page builder
     */
    public static function initialize()
    {
        StaticObjectManager::get(__CLASS__)->init();
    }

    /**
     * Register default hook
     */
    private static function registerHooks()
    {
        $areaCode = StaticState::getAreaCode();

        self::header();
        self::footer();

        Hooks::doAction('pagebuilder/init');
        Hooks::doAction("pagebuilder/{$areaCode}/init");
    }

    /**
     * Register header hooks
     */
    private static function header()
    {
        $areaCode = StaticState::getAreaCode();

        Hooks::addAction('header', function () {
            Hooks::doAction('pagebuilder/header');
        });

        Hooks::addAction('pagebuilder/header', function () use ($areaCode) {

            Hooks::doAction('pagebuilder/register_styles');
            Hooks::doAction('pagebuilder/register_scripts');
            Hooks::doAction('pagebuilder/enqueue_scripts');

            Hooks::doAction("pagebuilder/{$areaCode}/register_styles");
            Hooks::doAction("pagebuilder/{$areaCode}/register_scripts");
            Hooks::doAction("pagebuilder/{$areaCode}/enqueue_scripts");

            Hooks::doAction("pagebuilder/{$areaCode}/header");
        });
    }

    /**
     * Register footer hooks.
     */
    private static function footer()
    {
        $areaCode = StaticState::getAreaCode();

        Hooks::addAction('footer', function () {
            Hooks::doAction('pagebuilder/footer');
        });

        Hooks::addAction('pagebuilder/footer', function () use ($areaCode) {
            Hooks::doAction("pagebuilder/{$areaCode}/footer");
        });
    }

    /**
     * Initialize components
     */
    private function initComponents(array $buildSubject = [])
    {
        SettingsManager::run();

        foreach ($this->components as $class) {
            $component = $this->objectManager->get($class);
            if ($component instanceof BuildableInterface) {
                $component->init($buildSubject);
            }
        }

        if (true === StaticState::isAdminhtml()) {
            $this->initCommon();
        }

        $this->regisDefaultHook();
        $this->regisDefaultScripts();
    }

    /**
     * @return $this
     */
    private function regisDefaultHook()
    {
        Hooks::addAction('pagebuilder/header', function () {
            Theme::getStylesManager()->doItems(false);
            Theme::getScriptsManager()->doHeadItems();
        }, 11);

        Hooks::addAction('pagebuilder/footer', function () {
            Theme::getStylesManager()->doItems(false);
            Theme::getScriptsManager()->doFooterItems();
        }, 11);


        Hooks::addAction('style_loader_src',function ($src = '') {
            if (strpos($src, 'http') === false) {
                $src = Helper\StaticUrlBuilder::urlStaticBuilder($src);
            }

            return $src;
        });

        return $this;
    }

    /**
     * Regis default scripts
     *
     * @return $this
     */
    private function regisDefaultScripts()
    {
        /**
         * Use `pagebuilderWidgetRegister` to register js handling which responsible for specify widget
         */
        Theme::registerScript(
            'pagebuilderRegister',
            'Goomento_PageBuilder/js/action/pagebuilderRegister'
        );

        Theme::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone.min'
        );

        Theme::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio.min',
            [
                'backbone'
            ]
        );

        Theme::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette.min',
            [
                'backbone',
                'backbone.radio'
            ]
        );

        Theme::registerStyle(
            'fontawesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all.min.css',
            [],
            '5.9.0'
        );

        Theme::registerScript(
            'jquery-numerator',
            'Goomento_PageBuilder/lib/jquery-numerator/jquery-numerator.min',
            [
                'jquery',
                'jquery/ui'
            ]
        );

        Theme::registerScript(
            'swiper',
            'Goomento_PageBuilder/lib/swiper/swiper.min',
            [],
            '4.4.6'
        );

        return $this;
    }

    public function initCommon()
    {
        /** @var CommonApp $common */
        $common = StaticObjectManager::get(CommonApp::class);

        $common->initComponents();
    }

    /**
     * @inheritdoc
     */
    public function getAreaScopes()
    {
        $scopes = [
            'pagebuilder_content_editor',
            'pagebuilder_content_export',
            'pagebuilder_content_importer',
            'pagebuilder_content_preview',
            'pagebuilder_content_view',
            'pagebuilder_ajax_json',
            'pagebuilder_content_massRefresh',
        ];

        if ($this->dataHelper->isActive()) {
            $scopes[] = 'frontend';
        }

        return $scopes;
    }

    /**
     * Destruct
     */
    public function __destruct()
    {
        Hooks::doAction('pagebuilder/destruct');
    }
}
