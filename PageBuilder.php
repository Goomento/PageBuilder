<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Goomento\Core\SubSystemInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Magento\Framework\ObjectManagerInterface;

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

            HooksHelper::doAction('pagebuilder/construct');

            $this->initComponents($buildSubject);

            HooksHelper::addAction('init', function () {
                self::registerHooks();
            });
        }
    }

    /**
     * Initialize the page builder
     */
    public static function initialize()
    {
        ObjectManagerHelper::get(__CLASS__)->init();
    }

    /**
     * Register default hook
     */
    private static function registerHooks()
    {
        $areaCode = StateHelper::getAreaCode();

        self::header();
        self::footer();

        HooksHelper::doAction('pagebuilder/init');
        HooksHelper::doAction("pagebuilder/{$areaCode}/init");

        HooksHelper::doAction('pagebuilder/register_styles');
        HooksHelper::doAction('pagebuilder/register_scripts');


        HooksHelper::doAction("pagebuilder/{$areaCode}/register_styles");
        HooksHelper::doAction("pagebuilder/{$areaCode}/register_scripts");
    }

    /**
     * Register header hooks
     */
    private static function header()
    {
        $areaCode = StateHelper::getAreaCode();

        HooksHelper::addAction('header', function () {
            HooksHelper::doAction('pagebuilder/header');
        });

        HooksHelper::addAction('pagebuilder/header', function () use ($areaCode) {
            HooksHelper::doAction("pagebuilder/{$areaCode}/header");

            HooksHelper::doAction('pagebuilder/enqueue_scripts');
            HooksHelper::doAction("pagebuilder/{$areaCode}/enqueue_scripts");
        });
    }

    /**
     * Register footer hooks.
     */
    private static function footer()
    {
        $areaCode = StateHelper::getAreaCode();

        HooksHelper::addAction('footer', function () {
            HooksHelper::doAction('pagebuilder/footer');
        });

        HooksHelper::addAction('pagebuilder/footer', function () use ($areaCode) {
            HooksHelper::doAction("pagebuilder/{$areaCode}/footer");

            HooksHelper::doAction('pagebuilder/enqueue_scripts');
            HooksHelper::doAction("pagebuilder/{$areaCode}/enqueue_scripts");
        });
    }

    /**
     * Initialize components
     */
    private function initComponents(array $buildSubject = [])
    {
        foreach ($this->components as $class) {
            $component = $this->objectManager->get($class);
            if ($component instanceof BuildableInterface) {
                $component->init($buildSubject);
            }
        }

        $this->regisDefaultHook();
        $this->regisDefaultScripts();
    }

    /**
     * @return $this
     */
    private function regisDefaultHook()
    {
        HooksHelper::addAction('pagebuilder/header', function () {
            ThemeHelper::getStylesManager()->doItems(false);
            ThemeHelper::getScriptsManager()->doHeadItems();
        }, 11);

        HooksHelper::addAction('pagebuilder/footer', function () {
            ThemeHelper::getStylesManager()->doItems(false);
            ThemeHelper::getScriptsManager()->doFooterItems();
        }, 11);


        HooksHelper::addAction('style_loader_src',function ($src = '') {
            if (strpos($src, 'http') === false) {
                $src = Helper\UrlBuilderHelper::urlStaticBuilder($src);
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
        ThemeHelper::registerScript(
            'pagebuilderRegister',
            'Goomento_PageBuilder/js/action/pagebuilderRegister'
        );

        ThemeHelper::registerScript(
            'backbone',
            'Goomento_PageBuilder/lib/backbone/backbone.min'
        );

        ThemeHelper::registerScript(
            'backbone.radio',
            'Goomento_PageBuilder/lib/backbone/backbone.radio.min',
            [
                'backbone'
            ]
        );

        ThemeHelper::registerScript(
            'backbone.marionette',
            'Goomento_PageBuilder/lib/backbone/backbone.marionette.min',
            [
                'backbone',
                'backbone.radio'
            ]
        );

        ThemeHelper::registerStyle(
            'fontawesome',
            'Goomento_PageBuilder/lib/font-awesome/css/all.min.css',
            [],
            '5.9.0'
        );

        ThemeHelper::registerScript(
            'jquery-numerator',
            'Goomento_PageBuilder/lib/jquery-numerator/jquery-numerator.min',
            [
                'jquery',
                'jquery/ui'
            ]
        );

        ThemeHelper::registerScript(
            'swiper',
            'Goomento_PageBuilder/lib/swiper/swiper.min',
            [],
            '4.4.6'
        );

        return $this;
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
        HooksHelper::doAction('pagebuilder/destruct');
    }
}
