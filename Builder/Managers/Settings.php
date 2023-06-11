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
        $this->setComponent($manager->getName(), $manager);
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
     * @param string|null $name Optional. Settings manager name. Default is
     *                             null.
     *
     * @return AbstractSettingsManager|AbstractSettingsManager[] Single settings manager, if it exists,
     *                                     null if it doesn't exists, or the all
     *                                     the settings managers if no parameter
     *                                     defined.
     */
    public function getSettingsManagers(?string $name = null)
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
        /**
         * @var string $name
         * @var AbstractSettingsManager $manager
         */
        foreach ($this->getComponents() as $name => $manager) {
            $settingsModel = $manager->getModelForConfig();

            $tabs = $settingsModel->getTabsControls();

            $config[ $name ] = [
                'name' => $manager::NAME,
                'panelPage' => $settingsModel->getPanelPageSettings(),
                'cssWrapperSelector' => $settingsModel->getCssWrapperSelector(),
                'controls' => $settingsModel->getControls(),
                'tabs' => $tabs,
                'settings' => $settingsModel->getSettings(),
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
            $settingsModel = $manager->getModelForConfig();

            if ($settingsModel) {
                $config[ $name ] = $settingsModel->getFrontendSettings();
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

        $this->setComponent([
            GeneralSettings::NAME => GeneralSettings::class,
            PageSettings::NAME => PageSettings::class,
        ]);

        // Construct it to enable the hook
        $this->getComponents();
    }
}
