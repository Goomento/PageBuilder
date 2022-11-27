<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Builder\Base\AbstractSettingsManager;
use Goomento\PageBuilder\Builder\Base\AbstractSettings;
use Goomento\PageBuilder\Builder\Settings\Page;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;

class PageSettings extends AbstractSettingsManager
{
    const NAME = 'page';

    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     * @return AbstractSettings|null The model object.
     */
    public function getModelForConfig()
    {
        if (!StateHelper::isEditorMode()) {
            return null;
        }

        $editor = ObjectManagerHelper::getEditor();

        return $editor->getBuildableContent() ? $this->getSettingModel($editor->getBuildableContent()) : null;
    }

    /**
     * Save settings to DB.
     *
     * Save page settings to the database, as post meta data.
     *
     *
     * @param array $settings Settings.
     * @param BuildableContentInterface|null $buildableContent
     */
    protected function saveSettingsToDb(array $settings, ?BuildableContentInterface $buildableContent = null)
    {
    }

    /**
     * Get CSS file for update.
     *
     * Retrieve the CSS file before updating it.
     *
     * This method overrides the parent method to disallow updating CSS files for pages.
     *
     *
     * @param int $id ContentCss ID.
     *
     * @return mixed Disallow The updating CSS files for pages.
     */
    protected function getCssFileForUpdate($id)
    {
        return ObjectManagerHelper::create(ContentCss::class, ['id' => $id]);
    }

    /**
     * Get saved settings.
     *
     * Retrieve the saved settings from the post meta.
     *
     *
     * @param BuildableContentInterface|null $buildableContent
     * @return array Saved settings.
     */
    protected function getSavedSettings(?BuildableContentInterface $buildableContent = null)
    {
        $settings = $buildableContent->getSettings();

        if (!$settings) {
            $settings = [];
        }

        $settings['origin_status'] = $buildableContent->getOriginContent()->getStatus();

        return $settings;
    }

    /**
     * Get CSS file name.
     *
     * Retrieve CSS file name for the page settings manager.
     *
     *
     * @return string CSS file name.
     */
    protected function getCssFileName()
    {
        return 'content';
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
        if (!$cssFile instanceof ContentCss) {
            return null;
        }

        return $this->getSettingModel($cssFile->getModel());
    }

    /**
     * @inheritDoc
     */
    public function createModel(?BuildableContentInterface $buildableContent = null): AbstractSettings
    {
        return ObjectManagerHelper::create(
            Page::class,
            [
                'data' => [
                    'id' => $buildableContent ? $buildableContent->getId() : 0,
                    'model' => $buildableContent,
                    'settings' => $this->getSavedSettings($buildableContent),
                ]
            ]
        );
    }
}
