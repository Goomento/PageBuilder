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

/**
 * Places anywhere for essential packages
 */
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
        HooksHelper::addAction('pagebuilder/editor/before_enqueue_scripts', [ $this, 'registerScriptsEditorBefore']);
        HooksHelper::addAction('pagebuilder/register_styles', [ $this, 'registerStyles' ]);
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
    public function registerScriptsEditorBefore()
    {
        $minSuffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerScript(
            'goomento-common-modules',
            'Goomento_PageBuilder/build/common-modules' . $minSuffix,
            [
                'jquery',
                'jquery/ui',
                'backbone',
                'backbone.radio',
                'backbone.marionette',
            ]
        );

        ThemeHelper::registerScript(
            'goomento-common',
            'Goomento_PageBuilder/build/common' . $minSuffix,
            [
                'goomento-common-modules',
                'dialogs-manager',
            ]
        );

        $this->printConfig('goomento-editor');
    }

    /**
     * Register styles.
     *
     * Register common styles.
     *
     */
    public function registerStyles()
    {
        $min_suffix = Configuration::debug() ? '' : '.min';

        ThemeHelper::registerStyle(
            'goomento-common',
            'Goomento_PageBuilder/build/common' . $min_suffix . '.css',
            [],
            Configuration::version()
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
            'version' => Configuration::version(),
            'isRTL' => DataHelper::isRtl(),
            'activeModules' => array_keys($this->getComponents()),
            'urls' => [
                'assets' => UrlBuilderHelper::urlStaticBuilder('Goomento_PageBuilder') . '/',
            ],
        ];
    }
}
