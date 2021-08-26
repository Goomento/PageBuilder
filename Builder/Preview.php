<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticData;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticRegistry;
use Goomento\PageBuilder\Helper\StaticUtils;
use Goomento\PageBuilder\Helper\Theme;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Preview
 * @package Goomento\PageBuilder\Builder
 */
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
        if (StaticUtils::isAdminhtml()) {
            return;
        }

        $model = StaticRegistry::registry('current_preview_content');
        $this->contentId = $model ? $model->getId() : 0;

        Hooks::addAction('pagebuilder/frontend/enqueue_scripts', function () {
            $this->enqueueScripts();
            $this->enqueueStyles();
        });

        Hooks::addFilter('pagebuilder/content/html', [ $this,'builderWrapper' ], 999999);

        Hooks::addAction('pagebuilder/frontend/footer', [ $this, 'footer']);

        /**
         * Do action `pagebuilder/preview/init`
         */
        Hooks::doAction('pagebuilder/preview/init', $this);
    }

    /**
     * @return int
     */
    public function getContentId()
    {
        return $this->contentId;
    }

    /**
     * @param int $contentId
     * @return bool
     * @throws LocalizedException
     */
    public function isPreviewMode($contentId = 0)
    {
        if ($this->getContentId() && $this->getContentId() === $contentId) {
            return true;
        }

        $action = StaticRegistry::registry('current_action');
        $model = null;
        if ($action instanceof \Goomento\PageBuilder\Controller\Content\Preview) {
            $model = $action->getContent();
        }
        if (!StaticUtils::isAjax() && $model && $model->getId()) {
            $this->contentId = $model->getId();
            if ($contentId !== 0) {
                return $this->contentId === $contentId;
            }
            return true;
        }

        return false;
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
            /** @var DocumentsManager $documentManager */
            $documentManager = StaticObjectManager::get(DocumentsManager::class);
            $document = $documentManager->get($this->getContentId());

            $attributes = $document->getContainerAttributes();

            $attributes['id'] = 'gmt';

            $attributes['class'] .= ' gmt-edit-mode';

            $content = '<div ' . \Goomento\PageBuilder\Builder\Utils::renderHtmlAttributes($attributes) . '></div>';
        }

        return $content;
    }

    /**
     * Enqueue Styles
     */
    private function enqueueStyles()
    {
        StaticObjectManager::get(Frontend::class)->enqueueStyles();

        StaticObjectManager::get(Widgets::class)->enqueueWidgetsStyles();

        $suffix = Configuration::DEBUG ? '' : '.min';

        $direction_suffix = StaticData::isRtl() ? '-rtl' : '';

        Theme::registerStyle(
            'goomento-select2',
            'Goomento_PageBuilder::lib/e-select2/css/e-select2' . $suffix . '.css',
            [],
            '4.0.6-rc.1'
        );

        Theme::registerStyle(
            'editor-preview',
            'Goomento_PageBuilder::css/editor-preview' . $direction_suffix . $suffix . '.css',
            [
                'goomento-select2',
            ],
            Configuration::VERSION
        );

        Theme::enqueueStyle('editor-preview');

        /**
         * Preview enqueue styles.
         *
         * Fires after SagoTheme preview styles are enqueued.
         *
         */
        Hooks::doAction('pagebuilder/preview/enqueue_styles');
    }

    /**
     *
     */
    private function enqueueScripts()
    {
        /** @var Frontend $frontend */
        $frontend = StaticObjectManager::get(Frontend::class);
        $frontend->registerScripts();

        /** @var \Goomento\PageBuilder\Builder\Managers\Widgets $widgetManager */
        $widgetManager = StaticObjectManager::get(\Goomento\PageBuilder\Builder\Managers\Widgets::class);
        $widgetManager->enqueueWidgetsScripts();
        $suffix = Configuration::DEBUG ? '' : '.min';

        Theme::enqueueScript(
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
        Hooks::doAction('pagebuilder/preview/enqueue_scripts');
    }

    /**
     * Footer
     */
    public function footer()
    {
        /** @var Frontend $frontend */
        $frontend = StaticObjectManager::get(Frontend::class);
        $frontend->footer();
    }

    /**
     * Preview constructor.
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/content/preview', [ $this, 'init' ], 0);
    }
}
