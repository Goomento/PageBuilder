<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Common;

use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\Base\App as BaseApp;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticTemplate;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\Theme;

/**
 * Class App
 * @package Goomento\PageBuilder\Core\Common
 */
class App extends BaseApp
{
    private $templates = [];

    /**
     * App constructor.
     *
     */
    public function __construct()
    {
        $this->addDefaultTemplates();
        Hooks::addAction('pagebuilder/editor/before_enqueue_scripts', [ $this, 'registerScripts' ]);
        Hooks::addAction('pagebuilder/editor/before_enqueue_styles', [ $this, 'registerStyles' ]);

        Hooks::addAction('admin_enqueue_scripts', [ $this, 'registerStyles' ]);
        Hooks::addAction('pagebuilder/footer', [ $this, 'registerStyles' ]);

        Hooks::addAction('pagebuilder/editor/footer', [ $this, 'printTemplates' ]);
    }

    /**
     * Init components
     *
     * Initializing common components.
     *
     */
    public function initComponents()
    {
        $this->addComponent('ajax', StaticObjectManager::get(Ajax::class));
    }

    /**
     * Get name.
     *
     * Retrieve the app name.
     *
     *
     * @return string Common app name.
     */
    public function getName()
    {
        return 'common';
    }

    /**
     * Register scripts.
     *
     * Register common scripts.
     *
     */
    public function registerScripts()
    {
        $min_suffix = Utils::isScriptDebug() ? '' : '.min';

        Theme::registerScript(
            'goomento-common-modules',
            'Goomento_PageBuilder/js/common-modules' . $min_suffix,
            [
                'jquery',
                'jquery/ui',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
            ],
            '',
            true
        );

        Theme::registerScript(
            'imagesloaded',
            'Goomento_PageBuilder/lib/imagesloaded/imagesloaded' . $min_suffix,
            [],
            '4.1.0'
        );

        Theme::registerScript(
            'goomento-dialog',
            'Goomento_PageBuilder/lib/dialog/dialog' . $min_suffix,
            [
                'jquery/ui',
            ],
            '4.7.3'
        );

        Theme::enqueueScript(
            'goomento-common',
            'Goomento_PageBuilder/js/common' . $min_suffix,
            [
                'goomento-common-modules',
                'goomento-dialog',
            ],
            null,
            true
        );

        $this->printConfig();
    }

    /**
     * Register styles.
     *
     * Register common styles.
     *
     */
    public function registerStyles()
    {
        $min_suffix = Utils::isScriptDebug() ? '' : '.min';

        Theme::enqueueStyle(
            'goomento-common',
            'Goomento_PageBuilder::css/common' . $min_suffix . '.css',
            [],
            Configuration::VERSION
        );
    }

    /**
     * Add template.
     *
     *
     * @param string $template Can be either a link to template file or template
     *                         HTML content.
     */
    public function addTemplate(string $template)
    {
        $this->templates[] = $template;
    }

    /**
     * Print Templates
     *
     * Prints all registered templates.
     *
     */
    public function printTemplates()
    {
        foreach ($this->templates as $template) {
            if (strpos($template, '::') === false) {
                echo $template;
            } else {
                echo StaticTemplate::getHtml($template);
            }
        }
    }

    /**
     * Get init settings.
     *
     * Define the default/initial settings of the common app.
     *
     * @return array
     */
    protected function getInitSettings()
    {
        return [
            'version' => Configuration::VERSION,
            'isRTL' => StaticData::isRtl(),
            'activeModules' => array_keys($this->getComponents()),
            'urls' => [
                'assets' => StaticUrlBuilder::urlStaticBuilder('Goomento_PageBuilder') . '/',
            ],
        ];
    }

    /**
     * Add default templates.
     *
     * Register common app default templates.
     */
    private function addDefaultTemplates()
    {
        $default_templates = [
            'Goomento_PageBuilder::templates/library-layout.phtml',
        ];

        foreach ($default_templates as $template) {
            $this->addTemplate($template);
        }
    }
}
