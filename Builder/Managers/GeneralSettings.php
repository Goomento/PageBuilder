<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Managers\Controls as ControlsManager;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Builder\Base\AbstractSettingsManager as BaseManager;
use Goomento\PageBuilder\Builder\Base\AbstractSettings as BaseModel;
use Goomento\PageBuilder\Builder\Settings\General;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class GeneralSettings extends BaseManager
{
    const NAME = 'general';

    /**
     * Lightbox panel tab.
     */
    const PANEL_TAB_LIGHTBOX = 'lightbox';

    /**
     * Meta key for the general settings.
     */
    const META_KEY = 'general_settings';

    /**
     * General settings manager constructor.
     *
     * Initializing SagoTheme general settings manager.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->addPanelTabs();
    }

    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     *
     * @return BaseModel The model object.
     */
    public function getModelForConfig()
    {
        return $this->getSettingModel();
    }

    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the site options.
     *
     *
     * @param int $id ContentCss ID.
     *
     * @return array Saved settings.
     */
    protected function getSavedSettings($id)
    {
        $model_controls = General::getControlsList();

        $settings = [];

        foreach ($model_controls as $tab_name => $sections) {
            foreach ($sections as $section_name => $section_data) {
                foreach ($section_data['controls'] as $control_name => $control_data) {
                    $saved_setting = ConfigHelper::getOption($control_name, null);

                    if (null !== $saved_setting) {
                        $settings[ $control_name ] = ConfigHelper::getOption($control_name);
                    }
                }
            }
        }

        return $settings;
    }

    /**
     * Get CSS file name.
     *
     * Retrieve CSS file name for the general settings manager.
     *
     * @return string
     *
     * @return string CSS file name.
     */
    protected function getCssFileName()
    {
        return 'global';
    }

    /**
     * Save settings to DB.
     *
     * Save general settings to the database, as site options.
     *
     *
     * @param array $settings Settings.
     * @param int $id ContentCss ID.
     */
    protected function saveSettingsToDb(array $settings, $id)
    {
        $model_controls = General::getControlsList();

        $one_list_settings = [];

        foreach ($model_controls as $tab_name => $sections) {
            foreach ($sections as $section_name => $section_data) {
                foreach ($section_data['controls'] as $control_name => $control_data) {
                    if (isset($settings[ $control_name ])) {
                        $one_list_control_name = str_replace('goomento_', '', $control_name);

                        $one_list_settings[ $one_list_control_name ] = $settings[ $control_name ];
                        ConfigHelper::setOption($control_name, $settings[$control_name]);
                    } else {
                        ConfigHelper::delOption($control_name);
                    }
                }
            }
        }

        // Save all settings in one list for a future usage
        if (!empty($one_list_settings)) {
            ConfigHelper::setOption(self::META_KEY, $one_list_settings);
        } else {
            ConfigHelper::delOption(self::META_KEY);
        }
    }

    /**
     * Get model for CSS file.
     *
     * Retrieve the model for the CSS file.
     *
     *
     * @param AbstractCss $cssFile The requested CSS file.
     *
     * @return BaseModel The model object.
     */
    protected function getModelForCssFile(AbstractCss $cssFile)
    {
        return $this->getSettingModel();
    }

    /**
     * Get CSS file for update.
     *
     * Retrieve the CSS file before updating the it.
     *
     *
     * @param int $id ContentCss ID.
     *
     * @return GlobalCss The global CSS file object.
     */
    protected function getCssFileForUpdate($id)
    {
        return ObjectManagerHelper::create(GlobalCss::class);
    }

    /**
     * Add panel tabs.
     *
     * Register new panel tab for the lightbox settings.
     *
     */
    private function addPanelTabs()
    {
        ControlsManager::addTab(self::PANEL_TAB_LIGHTBOX, __('Lightbox'));
    }

    /**
     * @inheirtDoc
     */
    public function createModel(?int $id): BaseModel
    {
        return ObjectManagerHelper::create(
            General::class,
            [
                'data' => [
                    'id' => $id,
                    'settings' => $this->getSavedSettings($id),
                ]
            ]
        );
    }
}
