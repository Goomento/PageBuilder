<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Base\AbstractSettingsManager;
use Goomento\PageBuilder\Traits\TraitComponentsLoader;

class Settings
{
    use TraitComponentsLoader;

    private $components = [
        GeneralSettings::NAME => GeneralSettings::class,
        PageSettings::NAME => PageSettings::class,
    ];

    /**
     * Add settings manager.
     *
     * Register a single settings manager to the registered settings managers.
     *
     *
     * @param AbstractSettingsManager $manager Settings manager.
     */
    public function addSettingsManager(AbstractSettingsManager $manager)
    {
        $this->components[$manager->getName()] = $manager;
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
     * @param string $name Optional. Settings manager name. Default is
     *                             null.
     *
     * @return AbstractSettingsManager|AbstractSettingsManager[] Single settings manager, if it exists,
     *                                     null if it doesn't exists, or the all
     *                                     the settings managers if no parameter
     *                                     defined.
     */
    public function getSettingsManagers($name = null)
    {
        return $this->getComponent($name);
    }

    /**
     * Register default settings managers.
     */
    private function registerDefaultSettingsManagers()
    {
        $this->getComponents();
    }

    /**
     * Get settings managers config.
     *
     * Retrieve the settings managers configuration.
     *
     *
     * @return array The settings managers configuration.
     */
    public function getSettingsManagersConfig()
    {
        $config = [];

        foreach ($this->getComponents() as $name => $manager) {
            $settings_model = $manager->getModelForConfig();

            $tabs = $settings_model->getTabsControls();

            $config[ $name ] = [
                'name' => $manager::NAME,
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
    public function getSettingsFrontendConfig()
    {
        $config = [];

        foreach ($this->getComponents() as $name => $manager) {
            $settings_model = $manager->getModelForConfig();

            if ($settings_model) {
                $config[ $name ] = $settings_model->getFrontendSettings();
            }
        }

        return $config;
    }

    /**
     * Construct the program
     */
    public function __construct()
    {
        $this->registerDefaultSettingsManagers();
    }
}
