<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder;

use Goomento\PageBuilder\Builder\Managers\Widgets;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;

// phpcs:disable Magento2.CodeAnalysis.EmptyBlock.DetectedFunction
class BuilderRegister implements BuildableInterface
{
    /**
     * If `PageBuilder` will be initialized for the first time, this method will be executed
     *
     * @param array $buildSubject
     */
    public function init(array $buildSubject = [])
    {
        // Register the widget to be used
        HooksHelper::addAction('pagebuilder/elements/categories_registered', [$this, 'registerWidgetCategories']);
        HooksHelper::addAction('pagebuilder/widgets/widgets_registered', [$this, 'registerWidgets']);

        // Register the default resource css, js files
        HooksHelper::addAction('pagebuilder/register_styles', [$this, 'registerStyles']);
        HooksHelper::addAction('pagebuilder/register_scripts', [$this, 'registerScripts']);

        // Print resource (css, js files) if needed
        HooksHelper::addAction('pagebuilder/enqueue_scripts', [$this, 'enqueueScripts']);
    }

    /**
     * Add widget categories, where is grouping the similar functionality
     * Eg: Elements::addCategory('category-alias', [
     *      'title' => __('Category title'),
     * ])
     *
     *
     * @param Elements $elements
     * @see Elements::addCategory()
     */
    public function registerWidgetCategories(Elements $elements)
    {
    }

    /**
     * Add your widgets to be used here
     *
     * @see Widgets::registerWidgetType()
     */
    public function registerWidgets(Widgets $widgetsManager)
    {
    }

    /**
     * Add to queue your css files, that can be used by widget
     *
     * @see ThemeHelper::registerStyle()
     */
    public function registerStyles()
    {
    }

    /**
     * Add to queue your js files, that can be used by widget
     *
     * @see ThemeHelper::registerScript()
     */
    public function registerScripts()
    {
    }

    /**
     * If you need to add the css, js file into HTML output, now it's the time
     *
     * The css files will be added in <link> tag
     *
     * The js files will be called by requireJs
     *
     * @see ThemeHelper::enqueueScript()
     * @see ThemeHelper::enqueueStyle()
     */
    public function enqueueScripts()
    {
    }
}
