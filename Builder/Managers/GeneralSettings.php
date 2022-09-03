<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Managers\Controls as ControlsManager;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Css\GlobalCss;
use Goomento\PageBuilder\Builder\Base\AbstractSettingsManager;
use Goomento\PageBuilder\Builder\Base\AbstractSettings;
use Goomento\PageBuilder\Builder\Settings\General;
use Goomento\PageBuilder\Helper\ConfigHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class GeneralSettings extends AbstractSettingsManager
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
     * Initializing Goomento general settings manager.
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
     * @return AbstractSettings The model object.
     */
    public function getModelForConfig()
    {
        return $this->getSettingModel(null);
    }

    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the site options.
     *
     *
     * @param BuildableContentInterface|null $buildableContent
     * @return array Saved settings.
     */
    protected function getSavedSettings(?BuildableContentInterface $buildableContent = null)
    {
        $modelControls = General::getControlsList();

        $settings = [];

        foreach ($modelControls as $sections) {
            foreach ($sections as $sectionData) {
                foreach ($sectionData['controls'] as $controlName => $controlData) {
                    $savedSetting = ConfigHelper::getValue($controlName);

                    if (null !== $savedSetting) {
                        $settings[ $controlName ] = ConfigHelper::getValue($controlName);
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
     * @param BuildableContentInterface|null $buildableContent
     */
    protected function saveSettingsToDb(array $settings, ?BuildableContentInterface $buildableContent = null)
    {
        $modelControls = General::getControlsList();

        $oneListSettings = [];

        foreach ($modelControls as $sections) {
            foreach ($sections as $sectionData) {
                foreach ($sectionData['controls'] as $controlName => $controlData) {
                    if (isset($settings[ $controlName ])) {
                        $oneListSettings[ $controlName ] = $settings[ $controlName ];
                        ConfigHelper::setValue($controlName, $settings[$controlName]);
                    } else {
                        ConfigHelper::deleteValue($controlName);
                    }
                }
            }
        }

        // Save all settings in one list for a future usage
        if (!empty($oneListSettings)) {
            ConfigHelper::setValue(self::META_KEY, $oneListSettings);
        } else {
            ConfigHelper::deleteValue(self::META_KEY);
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
     * @return AbstractSettings The model object.
     */
    protected function getModelForCssFile(AbstractCss $cssFile)
    {
        return $this->getSettingModel(null);
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
    public function createModel(?BuildableContentInterface $buildableContent = null): AbstractSettings
    {
        return ObjectManagerHelper::create(
            General::class,
            [
                'data' => [
                    'id' => $buildableContent ? implode('_', $buildableContent->getIdentities()) : null,
                    'settings' => $this->getSavedSettings($buildableContent),
                ]
            ]
        );
    }
}
