<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\AbstractWidget;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Builder\Widgets\Common;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

class Widgets
{
    use TraitComponentsLoader;

    /**
     * Widget types.
     *
     * Holds the list of all the widget types.
     *
     *
     * @var AbstractWidget[]|null
     */
    private $components = null;

    /**
     * Init widgets.
     */
    private function initWidgets()
    {
        $this->components = [
            Common::NAME => Common::class,
        ];

        HooksHelper::doAction('pagebuilder/widgets/widgets_registered', $this);
    }

    /**
     * Register widget type.
     *
     * Add a new widget type to the list of registered widget types.
     *
     *
     * @param AbstractWidget|string $widget widget.
     *
     * @return Widgets True if the widget was registered.
     */
    public function registerWidgetType($widget)
    {
        if (null === $this->components) {
            $this->initWidgets();
        }

        if ($widget::ENABLED) {
            $this->components[ $widget::NAME ] = $widget;
        }

        return $this;
    }

    /**
     * Unregister widget type.
     *
     * Removes widget type from the list of registered widget types.
     *
     *
     * @param string $name AbstractWidget name.
     *
     * @return bool True if the widget was unregistered, False otherwise.
     */
    public function unregisterWidgetType($name)
    {
        if (!isset($this->components[ $name ])) {
            return false;
        }

        unset($this->components[ $name ]);

        return true;
    }

    /**
     * Get widget types.
     *
     * Retrieve the registered widget types list.
     *
     *
     * @param string|null $name Optional. AbstractWidget name. Default is null.
     *
     * @return AbstractWidget|AbstractWidget[]|null Registered widget types.
     */
    public function getWidgetTypes(?string $name = null)
    {
        if (is_null($this->components)) {
            $this->initWidgets();
        }

        if (null !== $name) {
            return $this->getComponent($name);
        }

        return $this->getComponents();
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

    /**
     * @param array $data
     * @return array
     */
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
     * Widgets manager constructor.
     *
     * Initializing SagoTheme widgets manager.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);
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
        $ajax_manager->registerAjaxAction('get_widgets_config', [ $this, 'ajaxGetWidgetTypesControlsConfig' ]);
    }
}
