<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Modules\Editor;
use Goomento\PageBuilder\Builder\Base\AbstractCss;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Builder\Base\AbstractSettingsManager;
use Goomento\PageBuilder\Builder\Base\AbstractSettings as BaseModel;
use Goomento\PageBuilder\Builder\Settings\Page;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Model\Content;

class PageSettings extends AbstractSettingsManager
{
    const NAME = 'page';

    /**
     * Get model for config.
     *
     * Retrieve the model for settings configuration.
     *
     * @return BaseModel The model object.
     */
    public function getModelForConfig()
    {
        /** @var Editor $editor */
        $editor = ObjectManagerHelper::get(Editor::class);

        if (!StateHelper::isEditorMode()) {
            return null;
        }

        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);

        $content_id = $editor->getContentId();
        $document = $documentManager->get($content_id);

        if (!$document) {
            return null;
        }

        return $this->getSettingModel($content_id);
    }

    /**
     * Ajax before saving settings.
     *
     * Validate the data before saving it and updating the data in the database.
     *
     *
     * @param array $data Content data.
     * @param int   $id   Content ID.
     *
     * @throws Exception If invalid post returned using the `$id`.
     * @throws Exception If current user don't have permissions to edit the post.
     */
    public function beforeSaveSettings(array $data, $id)
    {
        /** @var Content $model */
        $model = ContentHelper::get($id);

        if (empty($model) || ! $model->getId()) {
            throw new Exception('Invalid content.');
        }

        if (!empty($data['title'])) {
            $model->setTitle($data['title']);
        }

        if (!empty($data['status'])) {
            $model->setData('status', $data['status']);
        }

        if (isset($data['is_active'])) {
            $model->setIsActive((bool) $data['is_active']);
        }

        return $this;
    }

    /**
     * Save settings to DB.
     *
     * Save page settings to the database, as post meta data.
     *
     *
     * @param array $settings Settings.
     * @param int $id ContentCss ID.
     */
    protected function saveSettingsToDb(array $settings, $id)
    {
        $content = ContentHelper::get($id);
        if (!empty($settings)) {
            foreach ($settings as $name => $value) {
                $content->setSetting($name, $value);
            }
        } else {
            $content->setSettings([]);
        }
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
     * @param int $id ContentCss ID.
     *
     * @return array Saved settings.
     */
    protected function getSavedSettings($id)
    {
        $content = ContentHelper::get($id);
        $settings = $content->getSettings();

        if (!$settings) {
            $settings = [];
        }

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
     * @param \Goomento\PageBuilder\Builder\Base\AbstractCss $cssFile The requested CSS file.
     *
     * @return BaseModel The model object.
     */
    protected function getModelForCssFile(AbstractCss $cssFile)
    {
        if (!$cssFile instanceof ContentCss) {
            return null;
        }

        $contentId = $cssFile->getContentId();

        return $this->getSettingModel($contentId);
    }

    /**
     * Get special settings names.
     *
     * Retrieve the names of the special settings that are not saved as regular
     * settings. Those settings have a separate saving process.
     *
     *
     * @return array Special settings names.
     */
    protected function getSpecialSettingsNames()
    {
        return [
            'id',
            'title',
            'status',
            'content_id',
            'is_active',
        ];
    }

    public function createModel(?int $id): BaseModel
    {
        return ObjectManagerHelper::create(
            Page::class,
            [
                'data' => [
                    'id' => $id,
                    'settings' => $this->getSavedSettings($id),
                ]
            ]
        );
    }
}
