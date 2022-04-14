<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Base\AbstractApp;
use Goomento\PageBuilder\Builder\Modules\Ajax as Ajax;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\TemplateHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Common extends AbstractApp
{
    /**
     * @inheirtDoc
     */
    const NAME = 'common';

    /**
     * App constructor.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/init', [$this, 'initComponents']);
        HooksHelper::addAction('pagebuilder/editor/before_enqueue_scripts', [ $this, 'registerScripts' ]);
        HooksHelper::addAction('pagebuilder/editor/before_enqueue_styles', [ $this, 'registerStyles' ]);
        HooksHelper::addAction('pagebuilder/footer', [ $this, 'registerStyles' ]);
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this, 'printTemplates' ]);
    }

    /**
     * Init components
     *
     * Initializing common components.
     *
     */
    public function initComponents()
    {
        $this->addComponent('ajax', ObjectManagerHelper::get(Ajax::class));
    }

    /**
     * Register scripts.
     *
     * Register common scripts.
     *
     */
    public function registerScripts()
    {
        $min_suffix = Configuration::DEBUG ? '' : '.min';

        ThemeHelper::registerScript(
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

        ThemeHelper::registerScript(
            'imagesloaded',
            'Goomento_PageBuilder/lib/imagesloaded/imagesloaded' . $min_suffix,
            [],
            '4.1.0'
        );

        ThemeHelper::registerScript(
            'goomento-dialog',
            'Goomento_PageBuilder/lib/dialog/dialog' . $min_suffix,
            [
                'jquery/ui',
            ],
            '4.7.3'
        );

        ThemeHelper::enqueueScript(
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
        $min_suffix = Configuration::DEBUG ? '' : '.min';

        ThemeHelper::enqueueStyle(
            'goomento-common',
            'Goomento_PageBuilder::css/common' . $min_suffix . '.css',
            [],
            Configuration::VERSION
        );
    }

    /**
     * Print Templates
     *
     * Prints all registered templates.
     *
     */
    public function printTemplates()
    {
        echo TemplateHelper::getHtml('Goomento_PageBuilder::templates/library_layout.phtml');
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
            'isRTL' => DataHelper::isRtl(),
            'activeModules' => array_keys($this->getComponents()),
            'urls' => [
                'assets' => UrlBuilderHelper::urlStaticBuilder('Goomento_PageBuilder') . '/',
            ],
        ];
    }
}
