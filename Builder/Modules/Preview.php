<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

class Preview
{

    /**
     * @var null|BuildableContentInterface
     */
    private $contentModel;

    /**
     * Init the preview
     */
    public function constructByContent(BuildableContentInterface  $buildableContent)
    {
        $this->contentModel = $buildableContent;

        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$this, 'enqueueScripts']);
        HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$this, 'enqueueStyles']);
        HooksHelper::addFilter('pagebuilder/content/html', [ $this,'builderWrapper' ], 2022);

        /**
         * Do action `pagebuilder/preview/init`
         */
        HooksHelper::doAction('pagebuilder/preview/init', $this);
    }

    /**
     * Builder wrapper.
     *
     * Used to add an empty HTML wrapper for the builder, the javascript will add
     * the content later.
     *
     * @param BuildableContentInterface $buildableContent The content of the builder.
     *
     * @return string with HTML wrapper for the builder.
     */
    public function builderWrapper(BuildableContentInterface $buildableContent)
    {
        $content = '';

        if ($buildableContent->getId()) {

            $documentManager = ObjectManagerHelper::getDocumentsManager();

            $document = $documentManager->getByContent($this->contentModel);

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
    public function enqueueStyles()
    {
        ObjectManagerHelper::getWidgetsManager()->enqueueWidgetsStyles();

        ThemeHelper::registerStyle(
            'editor-preview',
            'Goomento_PageBuilder/build/editor-preview.css',
            [
                'jquery-select2',
                'pen'
            ]
        );

        ThemeHelper::enqueueStyle('editor-preview');
        ThemeHelper::enqueueStyle('goomento-animations');
        ThemeHelper::enqueueStyle('fontawesome');

        /**
         * Preview enqueue styles.
         *
         * Fires after Goomento preview styles are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_styles');
    }

    /**
     * Enqueue scripts
     */
    public function enqueueScripts()
    {
        // For inline editor
        ThemeHelper::enqueueScript('pen');
        ThemeHelper::enqueueScript('jquery-tipsy');
        /**
         * Preview enqueue scripts.
         *
         * Fires after Goomento preview scripts are enqueued.
         *
         */
        HooksHelper::doAction('pagebuilder/preview/enqueue_scripts');
    }

    /**
     * Footer
     */
    public function footer()
    {
        ObjectManagerHelper::getFrontend()->printFontsLinks();
    }

    /**
     * Preview constructor.
     */
    public function __construct()
    {
        /**
         * Ready to catch the custom trigger by controller
         */
        HooksHelper::addAction('pagebuilder/content/canvas', [ $this, 'constructByContent']);
    }
}
