<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Settings;

/**
 * Class Manager
 * @package Goomento\PageBuilder\Core\Settings
 */
class Manager
{

    /**
     * Settings managers.
     *
     * Holds all the registered settings managers.
     *
     *
     * @var Base\Manager[]
     */
    private static $settings_managers = [];

    /**
     * Builtin settings managers names.
     *
     * Holds the names for builtin SagoTheme settings managers.
     *
     *
     * @var array
     */
    private static $builtin_settings_managers_names = [ 'page', 'general' ];

    /**
     * Add settings manager.
     *
     * Register a single settings manager to the registered settings managers.
     *
     *
     * @param Base\Manager $manager Settings manager.
     */
    public static function addSettingsManager(Base\Manager $manager)
    {
        self::$settings_managers[ $manager->getName() ] = $manager;
    }

    /**
     * Get settings managers.
     *
     * Retrieve registered settings manager(s).
     *
     * If no parameter passed, it will retrieve all the settings managers. For
     * any given parameter it will retrieve a single settings manager if one
     * exist, or `null` otherwise.
     *
     *
     * @param string $manager_name Optional. Settings manager name. Default is
     *                             null.
     *
     * @return Base\Manager|Base\Manager[] Single settings manager, if it exists,
     *                                     null if it doesn't exists, or the all
     *                                     the settings managers if no parameter
     *                                     defined.
     */
    public static function getSettingsManagers($manager_name = null)
    {
        if ($manager_name) {
            if (isset(self::$settings_managers[ $manager_name ])) {
                return self::$settings_managers[ $manager_name ];
            }

            return null;
        }

        return self::$settings_managers;
    }

    /**
     * Register default settings managers.
     *
     * Register builtin SagoTheme settings managers.
     *
     */
    private static function registerDefaultSettingsManagers()
    {
        foreach (self::$builtin_settings_managers_names as $manager_name) {
            $manager_class = __NAMESPACE__ . '\\' . ucfirst($manager_name) . '\Manager';
            self::addSettingsManager(\Goomento\PageBuilder\Helper\StaticObjectManager::get($manager_class));
        }
    }

    /**
     * Get settings managers config.
     *
     * Retrieve the settings managers configuration.
     *
     *
     * @return array The settings managers configuration.
     */
    public static function getSettingsManagersConfig()
    {
        $config = [];

        foreach (self::$settings_managers as $name => $manager) {
            $settings_model = $manager->getModelForConfig();

            $tabs = $settings_model->getTabsControls();

            $config[ $name ] = [
                'name' => $manager->getName(),
                'panelPage' => $settings_model->getPanelPageSettings(),
                'cssWrapperSelector' => $settings_model->getCssWrapperSelector(),
                'controls' => $settings_model->getControls(),
                'tabs' => $tabs,
                'settings' => $settings_model->getSettings(),
            ];
        }

        return $config;
    }

    /**
     * Get settings frontend config.
     *
     * Retrieve the settings managers frontend configuration.
     *
     *
     * @return array The settings managers frontend configuration.
     */
    public static function getSettingsFrontendConfig()
    {
        $config = [];

        foreach (self::$settings_managers as $name => $manager) {
            $settings_model = $manager->getModelForConfig();

            if ($settings_model) {
                $config[ $name ] = $settings_model->getFrontendSettings();
            }
        }

        return $config;
    }

    /**
     * Run settings managers.
     *
     * Register builtin SagoTheme settings managers.
     *
     */
    public static function run()
    {
        self::registerDefaultSettingsManagers();
    }
}
