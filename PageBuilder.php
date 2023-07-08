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

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction
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

            $this->initComponents($buildSubject);

            // Wait for all components loaded
            HooksHelper::addAction('init', function () {
                $this->registerDefaultHook();
            });
        }
    }

    /**
     * Initialize the page builder
     */
    public static function initialize()
    {
        ObjectManagerHelper::get(PageBuilder::class)->init();
    }

    /**
     * Register default hook
     */
    private function registerDefaultHook()
    {
        $areaCode = StateHelper::getAreaCode();

        // HEADER
        HooksHelper::addAction('header', function () {
            HooksHelper::doAction('pagebuilder/header');
        });

        HooksHelper::addAction('pagebuilder/header', function () use ($areaCode) {
            HooksHelper::doAction('pagebuilder/register_styles');
            HooksHelper::doAction('pagebuilder/register_scripts');
            HooksHelper::doAction("pagebuilder/{$areaCode}/register_styles");
            HooksHelper::doAction("pagebuilder/{$areaCode}/register_scripts");

            HooksHelper::doAction("pagebuilder/{$areaCode}/header");

            HooksHelper::doAction('pagebuilder/enqueue_scripts');
            HooksHelper::doAction("pagebuilder/{$areaCode}/enqueue_scripts");

            ThemeHelper::getStylesManager()->doItems();
            ThemeHelper::getScriptsManager()->doHeadItems();
        });

        // FOOTER
        HooksHelper::addAction('footer', function () {
            HooksHelper::doAction('pagebuilder/footer');
        });

        HooksHelper::addAction('pagebuilder/footer', function () use ($areaCode) {
            HooksHelper::doAction("pagebuilder/{$areaCode}/footer");

            ThemeHelper::getStylesManager()->doItems();
            ThemeHelper::getScriptsManager()->doFooterItems();
        });

        // DEFAULT
        HooksHelper::doAction('pagebuilder/init');
        HooksHelper::doAction("pagebuilder/{$areaCode}/init");

        HooksHelper::addFilter('body_classes', [ThemeHelper::class, 'getBodyClass']);
        HooksHelper::addFilter('style_loader_src', [ThemeHelper::class, 'onStyleLoaderSource']);
    }

    /**
     * Initialize components
     */
    private function initComponents(array $buildSubject = [])
    {
        foreach ($this->components as $component) {
            if (is_string($component) && class_exists($component)) {
                $component = $this->objectManager->get($component);
            }

            if ($component instanceof BuildableInterface) {
                $component->init($buildSubject);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAreaScopes()
    {
        $scopes = [
            'pagebuilder_content_editor',
            'pagebuilder_ajax_json',
            'pagebuilder_content_canvas',
            'pagebuilder_content_view',
        ];

        if ($this->dataHelper->isActive()) {
            $scopes[] = 'frontend';
        }

        return $scopes;
    }
}
