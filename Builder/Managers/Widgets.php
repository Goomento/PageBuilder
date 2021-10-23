<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Core\Common\Modules\Ajax\Module as Ajax;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Widgets\Accordion;
use Goomento\PageBuilder\Widgets\Alert;
use Goomento\PageBuilder\Widgets\Button;
use Goomento\PageBuilder\Widgets\Common;
use Goomento\PageBuilder\Widgets\Counter;
use Goomento\PageBuilder\Widgets\Heading;
use Goomento\PageBuilder\Widgets\Html;
use Goomento\PageBuilder\Widgets\Icon;
use Goomento\PageBuilder\Widgets\IconBox;
use Goomento\PageBuilder\Widgets\IconList;
use Goomento\PageBuilder\Widgets\Image;
use Goomento\PageBuilder\Widgets\ImageBox;
use Goomento\PageBuilder\Widgets\Progress;
use Goomento\PageBuilder\Widgets\SocialIcons;
use Goomento\PageBuilder\Widgets\Spacer;
use Goomento\PageBuilder\Widgets\Tabs;
use Goomento\PageBuilder\Widgets\TextEditor;
use Goomento\PageBuilder\Widgets\Toggle;
use Goomento\PageBuilder\Widgets\Video;
use Goomento\PageBuilder\Widgets\Block;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;

/**
 * Class Widgets
 * @package Goomento\PageBuilder\Builder\Managers
 */
class Widgets
{

    /**
     * Widget types.
     *
     * Holds the list of all the widget types.
     *
     *
     * @var Widget[]
     */
    private $_widget_types = null;

    /**
     * Init widgets.
     */
    private function initWidgets()
    {
        $widgets = [
            Common::class,
            Heading::class,
            Image::class,
            TextEditor::class,
            Spacer::class,
            ImageBox::class,
            Icon::class,
            IconBox::class,
            IconList::class,
            Counter::class,
            Progress::class,
            Tabs::class,
            Accordion::class,
            Toggle::class,
            SocialIcons::class,
            Alert::class,
            Html::class,
            Button::class,
            Video::class,
            Block::class,
        ];

        $this->_widget_types = [];

        foreach ($widgets as $widget) {
            $this->registerWidgetType($widget);
        }

        /**
         * After widgets registered.
         *
         * Fires after SagoTheme widgets are registered.
         *
             *
         * @param Widgets $this The widgets manager.
         */
        Hooks::doAction('pagebuilder/widgets/widgets_registered', $this);
    }

    /**
     * Register widget type.
     *
     * Add a new widget type to the list of registered widget types.
     *
     *
     * @param Widget|string $widget widget.
     *
     * @return Widgets True if the widget was registered.
     */
    public function registerWidgetType($widget)
    {
        if (is_null($this->_widget_types)) {
            $this->initWidgets();
        }

        if (is_string($widget) && class_exists($widget)) {
            $widget = StaticObjectManager::get($widget);
        }

        if ($widget->isActive()) {
            $this->_widget_types[ $widget->getName() ] = $widget;
        }

        return $this;
    }

    /**
     * Unregister widget type.
     *
     * Removes widget type from the list of registered widget types.
     *
     *
     * @param string $name Widget name.
     *
     * @return bool True if the widget was unregistered, False otherwise.
     */
    public function unregisterWidgetType($name)
    {
        if (! isset($this->_widget_types[ $name ])) {
            return false;
        }

        unset($this->_widget_types[ $name ]);

        return true;
    }

    /**
     * Get widget types.
     *
     * Retrieve the registered widget types list.
     *
     *
     * @param string $widget_name Optional. Widget name. Default is null.
     *
     * @return Widget|Widget[]|null Registered widget types.
     */
    public function getWidgetTypes($widget_name = null)
    {
        if (is_null($this->_widget_types)) {
            $this->initWidgets();
        }

        if (null !== $widget_name) {
            return $this->_widget_types[$widget_name] ?? null;
        }

        return $this->_widget_types;
    }

    /**
     * Get widget types config.
     *
     * Retrieve all the registered widgets with config for each widgets.
     *
     *
     * @return array Registered widget types with each widget config.
     */
    public function getWidgetTypesConfig()
    {
        $config = [];

        foreach ($this->getWidgetTypes() as $widget_key => $widget) {
            $config[ $widget_key ] = $widget->getConfig();
        }

        return $config;
    }

    public function ajaxGetWidgetTypesControlsConfig(array $data)
    {
        $config = [];

        foreach ($this->getWidgetTypes() as $widget_key => $widget) {
            if (isset($data['exclude'][ $widget_key ])) {
                continue;
            }

            $config[ $widget_key ] = [
                'controls' => $widget->getStack(false)['controls'],
                'tabs_controls' => $widget->getTabsControls(),
            ];
        }

        return $config;
    }

    /**
     * @param $request
     * @return array
     * @throws Exception
     * @deprecated
     */
    public function ajaxRenderWidget($request)
    {
        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentManager->get($request['editor_post_id']);

        /** @var Editor $editor */
        $editor = StaticObjectManager::get(Editor::class);
        $is_edit_mode = $editor->isEditMode();
        $editor->setEditMode(true);

        /** @var State $state */
        $state = StaticObjectManager::get(State::class);

        $render_html = $state->emulateAreaCode(Area::AREA_FRONTEND, function () use ($document, $request) {
            return $document->renderElement($request['data']);
        });

        $editor->setEditMode($is_edit_mode);

        return [
            'render' => $render_html,
        ];
    }

    /**
     * Render widgets content.
     *
     * Used to generate the widget templates on the editor using Underscore JS
     * template, for all the registered widget types.
     *
     */
    public function renderWidgetsContent()
    {
        foreach ($this->getWidgetTypes() as $widget) {
            $widget->printTemplate();
        }
    }

    /**
     * Get widgets frontend settings keys.
     *
     * Retrieve frontend controls settings keys for all the registered widget
     * types.
     *
     *
     * @return array Registered widget types with settings keys for each widget.
     */
    public function getWidgetsFrontendSettingsKeys()
    {
        $keys = [];

        foreach ($this->getWidgetTypes() as $widget_type_name => $widget_type) {
            $widget_type_keys = $widget_type->getFrontendSettingsKeys();

            if ($widget_type_keys) {
                $keys[ $widget_type_name ] = $widget_type_keys;
            }
        }

        return $keys;
    }

    /**
     * Enqueue widgets scripts.
     *
     * Enqueue all the scripts defined as a dependency for each widget.
     *
     */
    public function enqueueWidgetsScripts()
    {
        foreach ($this->getWidgetTypes() as $widget) {
            $widget->enqueueScripts();
        }
    }

    /**
     * Enqueue widgets styles
     *
     * Enqueue all the styles defined as a dependency for each widget
     *
     */
    public function enqueueWidgetsStyles()
    {
        foreach ($this->getWidgetTypes() as $widget) {
            $widget->enqueueStyles();
        }
    }

    /**
     * Retrieve inline editing configuration.
     *
     * Returns general inline editing configurations like toolbar types etc.
     *
     *
     * @return array {
     *     Inline editing configuration.
     *
     *     @type array $toolbar {
     *         Toolbar types and the actions each toolbar includes.
     *         Note: Wysiwyg controls uses the advanced toolbar, textarea controls
     *         uses the basic toolbar and text controls has no toolbar.
     *
     *         @type array $basic    Basic actions included in the edit tool.
     *         @type array $advanced Advanced actions included in the edit tool.
     *     }
     * }
     */
    public function getInlineEditingConfig()
    {
        $basic_tools = [
            'bold',
            'underline',
            'italic',
        ];

        $advanced_tools = array_merge($basic_tools, [
            'createlink',
            'unlink',
            'h1' => [
                'h1',
                'h2',
                'h3',
                'h4',
                'h5',
                'h6',
                'p',
                'blockquote',
                'pre',
            ],
            'list' => [
                'insertOrderedList',
                'insertUnorderedList',
            ],
        ]);

        return [
            'toolbar' => [
                'basic' => $basic_tools,
                'advanced' => $advanced_tools,
            ],
        ];
    }

    /**
     * Widgets manager constructor.
     *
     * Initializing SagoTheme widgets manager.
     *
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
    }

    /**
     * Register ajax actions.
     *
     * Add new actions to handle data after an ajax requests returned.
     *
     *
     * @param Ajax $ajax_manager
     * @throws Exception
     */
    public function registerAjaxActions(Ajax $ajax_manager)
    {
        $ajax_manager->registerAjaxAction('render_widget', [ $this, 'ajaxRenderWidget' ]);
        $ajax_manager->registerAjaxAction('get_widgets_config', [ $this, 'ajaxGetWidgetTypesControlsConfig' ]);
    }
}
