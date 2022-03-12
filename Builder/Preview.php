<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Modules\Frontend;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RegistryHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Preview
{

    /**
     * @var int
     */
    private $contentId;

    /**
     * Init the preview
     */
    public function init()
    {
        if (StateHelper::isAdminhtml()) {
            return;
        }

        $model = RegistryHelper::registry('current_preview_content');
        $this->contentId = $model ? $model->getId() : 0;

        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', function () {
            $this->enqueueScripts();
            $this->enqueueStyles();
        });

        HooksHelper::addFilter('pagebuilder/content/html', [ $this,'builderWrapper' ], 999999);

        HooksHelper::addAction('pagebuilder/frontend/footer', [ $this, 'footer']);

        /**
         * Do action `pagebuilder/preview/init`
         */
        HooksHelper::doAction('pagebuilder/preview/init', $this);
    }

    /**
     * @return int
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * Builder wrapper.
     *
     * Used to add an empty HTML wrapper for the builder, the javascript will add
     * the content later.
     *
     * @param string $content The content of the builder.
     *
     * @return string HTML wrapper for the builder.
     */
    public function builderWrapper($content)
    {
        if ($this->getContentId()) {
            /** @var Documents $documentManager */
            $documentManager = ObjectManagerHelper::get(Documents::class);
            $document = $documentManager->get($this->getContentId());

            $attributes = $document->getContainerAttributes();

            $attributes['id'] = 'gmt';

            $attributes['class'] .= ' gmt-edit-mode';

            $content = '<div ' . DataHelper::renderHtmlAttributes($attributes) . '></div>';
        }

        return $content;
    }

    /**
     * Enqueue Styles
     */
    private function enqueueStyles()
    {
        ObjectManagerHelper::get(Frontend::class)->enqueueStyles();

        ObjectManagerHelper::get(Widgets::class)->enqueueWidgetsStyles();

        $suffix = Configuration::DEBUG ? '' : '.min';

        $direction_suffix = DataHelper::isRtl() ? '-rtl' : '';

        ThemeHelper::registerStyle(
            'goomento-select2',
            'Goomento_PageBuilder::lib/e-select2/css/e-select2' . $suffix . '.css',
            [],
            '4.0.6-rc.1'
        );

        ThemeHelper::registerStyle(
            'editor-preview',
            'Goomento_PageBuilder::css/editor-preview' . $direction_suffix . $suffix . '.css',
            [
                'goomento-select2',
            ],
            Configuration::VERSION
        );

        ThemeHelper::enqueueStyle('editor-preview');

        /**
         * Preview enqueue styles.
         *
         * Fires after SagoTheme preview styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_styles');
    }

    /**
     *
     */
    private function enqueueScripts()
    {
        /** @var Frontend $frontend */
        $frontend = ObjectManagerHelper::get(Frontend::class);
        $frontend->registerScripts();

        /** @var Widgets $widgetManager */
        $widgetManager = ObjectManagerHelper::get(Widgets::class);
        $widgetManager->enqueueWidgetsScripts();
        $suffix = Configuration::DEBUG ? '' : '.min';

        ThemeHelper::enqueueScript(
            'goomento-inline-editor',
            'Goomento_PageBuilder/lib/inline-editor/js/inline-editor' . $suffix,
            [],
            Configuration::VERSION,
            true
        );

        /**
         * Preview enqueue scripts.
         *
         * Fires after SagoTheme preview scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_scripts');
    }

    /**
     * Footer
     */
    public function footer()
    {
        /** @var Frontend $frontend */
        $frontend = ObjectManagerHelper::get(Frontend::class);
        $frontend->footer();
    }

    /**
     * Preview constructor.
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/content/preview', [ $this, 'init' ], 0);
    }
}
