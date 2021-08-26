<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Settings\Page;

use Goomento\Core\Helper\ObjectManager;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Core\Files\Css\Base;
use Goomento\PageBuilder\Core\Files\Css\ContentCss;
use Goomento\PageBuilder\Core\Settings\Base\Manager as BaseManager;
use Goomento\PageBuilder\Core\Settings\Base\Model as BaseModel;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Model\Content;

/**
 * Class Manager
 * @package Goomento\PageBuilder\Core\Settings\Page
 */
class Manager extends BaseManager
{

    /**
     * Meta key for the page settings.
     */
    const META_KEY = 'page_settings';

    /**
     * Get manager name.
     *
     * Retrieve page settings manager name.
     *
     *
     * @return string Manager name.
     */
    public function getName()
    {
        return 'page';
    }

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
        $editor = StaticObjectManager::get(Editor::class);
        if (! $editor->isEditMode()) {
            return null;
        }

        /** @var DocumentsManager $documentManager */
        $documentManager = StaticObjectManager::get(DocumentsManager::class);

        $content_id = $editor->getContentId();
        $document = $documentManager->get($content_id);

        if (! $document) {
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
     * @throws \Exception If invalid post returned using the `$id`.
     * @throws \Exception If current user don't have permissions to edit the post.
     */
    public function ajaxBeforeSaveSettings(array $data, $id)
    {
        /** @var Content $model */
        $model = StaticContent::get($id);

        if (empty($model) || ! $model->getId()) {
            throw new \Exception('Invalid content.');
        }

        // Avoid save empty post title.
        if (! empty($data['title'])) {
            $model->setTitle($data['title']);
        }

        if (!empty($data['status'])) {
            // To avoid exception for `autosave` status
            $model->setData('status', $data['status']);
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
        $content = StaticContent::get($id);
        if (!empty($settings)) {
            $content->setSettings($settings);
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
        return ObjectManager::create(ContentCss::class, ['id' => $id]);
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
        $content = StaticContent::get($id);
        $settings = $content->getSettings();

        if (!$settings) {
            $settings = [];
        }

        $settings['template'] = 'default';
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
     * @param Base $css_file The requested CSS file.
     *
     * @return BaseModel The model object.
     */
    protected function getModelForCssFile(Base $css_file)
    {
        if (! $css_file instanceof ContentCss) {
            return null;
        }

        $post_id = $css_file->getContentId();

        return $this->getSettingModel($post_id);
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
        ];
    }
}
