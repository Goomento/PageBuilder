<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Modules\Ajax;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;

abstract class AbstractSettingsManager extends AbstractEntity
{
    const NAME = 'base';

    const TYPE = 'settings_manager';

    /**
     * Models cache.
     *
     * Holds all the models.
     *
     *
     * @var AbstractSettings[]
     */
    private $modelsCache = [];

    /**
     * Settings base manager constructor.
     *
     * Initializing Goomento settings base manager.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/editor/footer', [ $this,'addTemplate' ]);

        HooksHelper::addAction('pagebuilder/ajax/register_actions', [ $this,'registerAjaxActions' ]);

        $name = $this->getCssFileName();

        HooksHelper::addAction("pagebuilder/css-file/{$name}/parse", [ $this, 'addSettingsCssRules' ]);
    }

    /**
     * Register ajax actions.
     *
     * Add new actions to handle data after an ajax requests returned.
     *
     *
     *
     * @param Ajax $ajaxManager
     * @throws Exception
     */
    public function registerAjaxActions(Ajax $ajaxManager)
    {
        $ajaxManager->registerAjaxAction('save_' . static::NAME . '_settings', [ $this, 'ajaxSaveSettings' ]);
    }

    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     * @return AbstractSettings The model object.
     */
    abstract public function getModelForConfig();

    /**
     * Get model.
     *
     * Retrieve the model for any given model ID.
     *
     *
     * @param BuildableContentInterface|null $buildableContent
     * @return AbstractSettings
     */
    public function getSettingModel(?BuildableContentInterface $buildableContent = null)
    {
        if ($buildableContent) {
            $key = $buildableContent->getUniqueIdentity();
        } else {
            $key = 0;
        }
        if (!isset($this->modelsCache[$key])) {
            $this->modelsCache[$key] = $this->createModel($buildableContent);
        }

        return $this->modelsCache[ $key ];
    }

    /**
     * Ajax request to save settings.
     *
     * Save settings using an ajax request.
     *
     *
     * @param array $request Ajax request.
     *
     * @return array Ajax response data.
     */
    public function ajaxSaveSettings(array $request, BuildableContentInterface $buildableContent)
    {
        $data = $request['data'];

        $this->beforeSaveSettings($data, $buildableContent);

        $this->saveSettings($data, $buildableContent);

        $settingsName = static::NAME;

        $successResponseData = [];

        /**
         * Settings success response data.
         *
         * Filters the success response data when saving settings using ajax.
         *
         * The dynamic portion of the hook name, `$settingsName`, refers to the settings name.
         *
         *
         * @param array $successResponseData Success response data.
         * @param int   $id                    Settings ID.
         * @param array $data                  Settings data.
         */
        return HooksHelper::applyFilters("pagebuilder/settings/{$settingsName}/success_response_data", $successResponseData, $buildableContent, $data)->getResult();
    }

    /**
     * Save settings.
     *
     * Save settings to the database and update the CSS file.
     *
     *
     * @param array $settings Settings.
     * @param BuildableContentInterface|null $buildableContent
     * @return AbstractSettingsManager
     */
    public function saveSettings(array $settings, ?BuildableContentInterface $buildableContent)
    {
        $this->saveSettingsToDb($settings, $buildableContent);

        return $this;
    }

    /**
     * Add settings CSS rules.
     *
     * Add new CSS rules to the settings manager.
     *
     * @param AbstractCss $cssFile The requested CSS file.
     */
    public function addSettingsCssRules(AbstractCss $cssFile)
    {
        /** @var AbstractSettings $model */
        $model = $this->getModelForCssFile($cssFile);

        $cssFile->addControlsStackStyleRules(
            $model,
            $model->getStyleControls(),
            $model->getSettings(),
            [ '{{WRAPPER}}' ],
            [ $model->getCssWrapperSelector() ]
        );
    }

    /**
     * On Goomento init.
     *
     * Add editor template for the settings
     *
     */
    public function addTemplate()
    {
        $name = static::NAME;
        ?>
        <script type="text/template" id="tmpl-gmt-panel-<?= EscaperHelper::escapeHtml($name); ?>-settings">
            <?php $this->printEditorTemplateContent($name); ?>
        </script>
        <?php
    }

    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the database.
     *
     * @param BuildableContentInterface|null $buildableContent.
     */
    abstract protected function getSavedSettings(?BuildableContentInterface $buildableContent = null);

    /**
     * Get CSS file name.
     *
     * Retrieve CSS file name for the settings base manager.
     *
     */
    abstract protected function getCssFileName();

    /**
     * Save settings to DB.
     *
     * Save settings to the database.
     *
     *
     * @param array $settings Settings.
     * @param BuildableContentInterface|null $buildableContent
     */
    abstract protected function saveSettingsToDb(array $settings, ?BuildableContentInterface $buildableContent = null);

    /**
     * Get model for CSS file.
     *
     * Retrieve the model for the CSS file.
     *
     *
     * @param AbstractCss $cssFile The requested CSS file.
     */
    abstract protected function getModelForCssFile(AbstractCss $cssFile);

    /**
     * Get CSS file for update.
     *
     * Retrieve the CSS file before updating it.
     *
     * @param int $id ContentCss ID.
     */
    abstract protected function getCssFileForUpdate($id);

    /**
     * Ajax before saving settings.
     *
     * Validate the data before saving it and updating the data in the database.
     *
     *
     * @param array $settings ContentCss data.
     * @param BuildableContentInterface|null $buildableContent
     * @return AbstractSettingsManager
     */
    public function beforeSaveSettings(array $settings, ?BuildableContentInterface $buildableContent = null)
    {
        return $this;
    }

    /**
     * Print the setting template content in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     *
     * @param string $name Settings panel name.
     */
    protected function printEditorTemplateContent(string $name)
    {
        ?>
        <div class="gmt-panel-navigation">
            <div class="gmt-component-tab gmt-panel-navigation-tab gmt-tab-control-close" data-tab="close">
                <a href="#"><?= __('Close') ?></a>
            </div>
            <# _.each( goomento.config.settings.<?= EscaperHelper::escapeHtml($name); ?>.tabs, function( tabTitle, tabSlug ) {
                $e.bc.ensureTab( 'panel/<?= EscaperHelper::escapeHtml($name); ?>-settings', tabSlug );
            #>
                <div class="gmt-component-tab gmt-panel-navigation-tab gmt-tab-control-{{ tabSlug }}" data-tab="{{ tabSlug }}">
                    <a href="#">{{{ tabTitle }}}</a>
                </div>
                <# } ); #>
        </div>
        <div id="gmt-panel-<?= EscaperHelper::escapeHtml($name); ?>-settings-controls"></div>
        <?php
    }

    /**
     * Create model.
     *
     * Create a new model object for any given model ID and store the object in
     * models cache property for later use.
     */
    abstract public function createModel(?BuildableContentInterface $buildableContent = null) : AbstractSettings;
}
