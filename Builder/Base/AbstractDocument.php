<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Developer;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\BuildableContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

abstract class AbstractDocument extends ControlsStack
{
    private static $properties = [];

    /**
     * Stores the Content ID
     *
     * @var mixed
     * @deprecated
     */
    protected $contentId;

    /**
     * @var BuildableContentInterface
     */
    protected $model;

    /**
     * @return mixed
     */
    protected static function getEditorPanelCategories()
    {
        return ObjectManagerHelper::getElementsManager()->getCategories();
    }

    /**
     * Get properties.
     *
     * Retrieve the document properties.
     *
     *
     * @return array Document properties.
     */
    public static function getProperties()
    {
        return [
            'is_editable' => true,
        ];
    }


    public static function getEditorPanelConfig()
    {
        return [
            'widgets_settings' => [],
            'elements_categories' => static::getEditorPanelCategories(),
            'messages' => [
                'publish_notification' => __('Hurray! Your %1 is live.', static::getTitle()),
            ],
        ];
    }

    /**
     * Get element title.
     *
     * Retrieve the element title.
     *
     *
     * @return string|mixed Element title.
     */
    public static function getTitle()
    {
        return __('Document');
    }

    /**
     * Get property.
     *
     * Retrieve the document property.
     *
     *
     * @param string $key The property key.
     *
     * @return mixed The property value.
     */
    public static function getProperty($key)
    {
        $id = __CLASS__;

        if (!isset(self::$properties[ $id ])) {
            self::$properties[ $id ] = static::getProperties();
        }

        return self::getItems(self::$properties[ $id ], $key);
    }

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->getName() . '-' . $this->getModel()->getOriginContent()->getId();
    }

    /**
     * Get model id
     */
    public function getMainModelId()
    {
        return $this->getMainModel()->getId();
    }

    /**
     * @return BuildableContentInterface
     */
    public function getMainModel()
    {
        return $this->getModel()->getOriginContent();
    }

    /**
     *
     * @param $data
     *
     * @throws BuilderException If the widget was not found.
     *
     * @return string
     */
    public function renderElement($data)
    {
        // Start buffering
        // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        ob_start();

        $widget = ObjectManagerHelper::getElementsManager()
            ->createElementInstance($data);

        if (!$widget) {
            throw new BuilderException(
                'Widget not found.'
            );
        }

        $widget
            ->setBuildableContent($this->getModel()->getOriginContent())
            ->printContent();

        return ob_get_clean();
    }

    /**
     * @return array
     */
    public function getContainerAttributes()
    {
        $attributes = [
            'data-gmt-type' => $this->getName(),
            'data-gmt-id' => $this->getModel()->getOriginContent()->getId(),
            'class' => 'goomento gmt gmt-' . $this->getModel()->getUniqueIdentity(),
        ];

        if (!StateHelper::isCanvasMode()) {
            $attributes['data-gmt-settings'] = DataHelper::encode($this->getFrontendSettings());
        }

        return $attributes;
    }

    /**
     * Get view url, which is related to revision view for content view
     */
    public function getSystemPreviewUrl()
    {
        static $url;
        if ($url === null) {
            $url = UrlBuilderHelper::getContentViewUrl($this->getModel());

            /**
             *
             * Filters the preview URL.
             *
             *
             * @param string   $url  Preview URL.
             * @param AbstractDocument $this The document instance.
             */
            $url = HooksHelper::applyFilters('pagebuilder/document/urls/system_preview', $url, $this)->getResult();
        }

        return $url;
    }

    /**
     * Init document data
     *
     * @return array
     */
    protected function _getInitialConfig()
    {
        $settingManager = ObjectManagerHelper::getSettingsManager();
        $settings = $settingManager->getSettingsManagersConfig();
        return [
            'id' => $this->getMainModelId(),
            'type' => $this->getName(),
            'settings' => $settings['page'],
            'version' => $this->getModel()->getSetting('version'),
            'remoteLibrary' => $this->getRemoteLibraryConfig(),
            'last_edited' => $this->getLastEdited(),
            'date' => date(DATE_ATOM, strtotime($this->getModel()->getUpdateTime())),
            'panel' => static::getEditorPanelConfig(),
            'container' => 'body',
            'urls' => [
                'exit_to_dashboard' => $this->getExitUrl(),
                'preview' => $this->getPreviewUrl(),
                'system_preview' => $this->getSystemPreviewUrl(),
                'permalink' => $this->getPermalink(),
            ],
        ];
    }

    /**
     * @return string
     */
    private function getExitUrl()
    {
        $backUrl = RequestHelper::getParam('back_url');
        if (!empty($backUrl)) {
            try {
                $backUrl = EncryptorHelper::decrypt($backUrl);
            } catch (Exception $e) {
                $backUrl = '';
            }
        }
        if (!$backUrl) {
            $backUrl = UrlBuilderHelper::getContentEditUrl($this->getModel()->getOriginContent());
        }

        return $backUrl;
    }

    /**
     * Register controls
     *
     * @throws Exception
     */
    protected function registerControls()
    {
        $this->startControlsSection(
            'document_settings',
            [
                'label' => __('General Settings'),
                'tab' => Controls::TAB_SETTINGS,
            ]
        );

        $this->endControlsSection();

        /**
         * Register document controls.
         *
         * Fires after Goomento registers the document controls.
         *
         *
         * @param AbstractDocument $this The document instance.
         */
        HooksHelper::doAction('pagebuilder/documents/register_controls', $this);
    }

    /**
     * Default save data
     *
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function save(array $data)
    {
        if (!AuthorizationHelper::isCurrentUserCan($this->getModel()->getRoleName('save'))) {
            throw new BuilderException(
                'Sorry, you need permissions to save this content'
            );
        }

        /**
         * Before document save.
         *
         * Fires when document save starts on Goomento.
         *
         *
         * @param AbstractDocument $this The current document.
         * @param $data.
         */
        HooksHelper::doAction('pagebuilder/document/before_save', $this, $data);

        if (!empty($data['settings']) && is_array($data['settings'])) {
            // Validate state
            if (BuildableContentHelper::isContentStatus($this->getModel()) && !AuthorizationHelper::isCurrentUserCan($this->getModel()->getRoleName('publish'))) {
                $originModel = $this->getModel()->getOriginContent();
                if ((isset($data['settings']['status']) && $data['settings']['status'] !== $originModel->getStatus()) ||
                    (isset($data['settings']['is_active']) && $data['settings']['is_active'] !== $originModel->getIsActive())) {
                    throw new BuilderException(
                        'Sorry, you need permissions to save this content'
                    );
                }
            }

            $this->setModelSettings($data['settings']);
        }

        if (isset($data['elements']) && is_array($data['elements'])) {
            $this->setModelElements($data['elements']);
        }

        if (isset($data['label']) && $data['label']) {
            $this->getModel()->setFlag('save_message', (string) $data['label']);
        }

        $this->setModelVersion();

        $this->saveModel();

        /**
         * After document save.
         *
         * Fires when document save is complete.
         *
         *
         * @param AbstractDocument $this The current document.
         * @param $data.
         */
        HooksHelper::doAction('pagebuilder/document/after_save', $this, $data);

        return true;
    }

    /**
     *
     * @return mixed
     */
    public function getEditUrl()
    {
        $url = UrlBuilderHelper::getUrl('gmtpagebuilder/editor/index/action/editor');
        /**
         * Document edit url.
         *
         * Filters the document edit url.
         *
         *
         * @param string   $url  The edit url.
         * @param AbstractDocument $this The document instance.
         */
        return HooksHelper::applyFilters('pagebuilder/document/urls/edit', $url, $this)->getResult();
    }

    /**
     * Get preview URL, which is the URL of origin content
     *
     * @return mixed|null
     */
    public function getPreviewUrl()
    {
        /**
         * Use a static var - to avoid change the `ver` parameter on every call.
         */
        static $url;

        if (null === $url) {

            $url = UrlBuilderHelper::getCanvasUrl(
                $this->getModel()->getOriginContent()
            );

            /**
             * Document preview URL.
             *
             * Filters the document preview URL.
             *
             *
             * @param string   $url  The preview URL.
             * @param AbstractDocument $this The document instance.
             */
            $url = HooksHelper::applyFilters('pagebuilder/document/urls/preview', $url, $this)->getResult();
        }

        return $url;
    }

    /**
     *
     * @param null $data
     * @param bool $withHtmlContent
     *
     * @return array
     */
    public function getElementsRawData($data = null, $withHtmlContent = false)
    {
        if (null === $data) {
            $data = $this->getElementsData();
        }

        $editorData = [];

        $elementManager = ObjectManagerHelper::getElementsManager();

        foreach ($data as $elementData) {
            $element = $elementManager->createElementInstance($elementData);

            if (!$element) {
                continue;
            }

            $editorData[] = $element->getRawData($withHtmlContent);
        }

        return $editorData;
    }

    /**
     *
     * @return array
     */
    public function getElementsData()
    {
        return $this->getModel()->getElements();
    }

    /**
     * @param $elementsData
     * @return void
     */
    public function printElementsWithWrapper($elementsData = null)
    {
        if (!$elementsData) {
            $elementsData = $this->getElementsData();
        }
        ?>
        <div <?= DataHelper::renderHtmlAttributes($this->getContainerAttributes()); ?>>
            <div class="gmt-inner">
                <div class="gmt-section-wrap">
                    <?php $this->printElements($elementsData); ?>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * @return string
     */
    public function getCssWrapperSelector()
    {
        return '';
    }

    /**
     * @return array
     */
    public function getPanelPageSettings()
    {
        return [
            'title' => __('%s Settings', static::getTitle()),
        ];
    }

    /**
     * @return BuildableContentInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return BuildableContentInterface
     */
    public function setModel(BuildableContentInterface $model)
    {
        return $this->model = $model;
    }

    /**
     * @return BuildableContentInterface
     */
    public function saveModel()
    {
        $model = $this->getModel();

        return BuildableContentHelper::saveBuildableContent(
            $model,
            $model->hasFlag('save_message') ? $model->getFlag('save_message') : ''
        );
    }

    /**
     * Get permanent link, which is related to the Published or Draft content
     *
     * @return string
     */
    public function getPermalink()
    {
        return $this->getModel()->getStatus() === BuildableContentInterface::STATUS_PUBLISHED
            ? UrlBuilderHelper::getPublishedContentUrl($this->getModel()->getOriginContent())
            : UrlBuilderHelper::getContentViewUrl($this->getModel()->getOriginContent());
    }

    /**
     * @return string
     */
    public function getContent()
    {
        $frontend = ObjectManagerHelper::getFrontend();
        return $frontend->getBuilderContent($this->getModel());
    }

    /**
     * Delete content
     *
     * @return void
     */
    public function delete()
    {
        BuildableContentHelper::deleteBuildableContent($this->getModel());
    }

    /**
     * Save editor elements.
     *
     * Save data from the editor to the database.
     *
     *
     * @param array $elements
     */
    protected function setModelElements(array $elements)
    {
        $editorData = $this->getElementsRawData($elements);

        $this->getModel()->setElements($elements);

        /**
         * Before saving data.
         *
         * Fires before Goomento saves data to the database.
         *
         *
         * @param string   $status          ContentCss status.
         * @param int|bool $isMetaUpdated Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        HooksHelper::doAction('pagebuilder/db/before_save', $this->getModel()->getStatus());

        /**
         * After saving data.
         *
         * Fires after Goomento saves data to the database.
         *
         *
         * @param int   $postId     The ID of the post.
         * @param array $editorData Sanitize posted data.
         */
        HooksHelper::doAction('pagebuilder/editor/after_save', $this->getModel()->getId(), $editorData);
    }

    public function setModelVersion()
    {
        // Save per revision.
        $this->updateMeta('version', Developer::version());

        /**
         * Document version save.
         *
         * Fires when document version is saved on Goomento.
         * Will not fire during Goomento Upgrade.
         *
         *
         * @param AbstractDocument $this The current document.
         *
         */
        HooksHelper::doAction('pagebuilder/document/save_version', $this);
    }

    /**
     *
     * @param string $key   Meta data key.
     * @param string $value Meta data value.
     *
     * @return BuildableContentInterface
     */
    public function updateContentData($key, $value)
    {
        return $this->getModel()->setSetting($key, $value);
    }

    /**
     *
     * @param string $key   Meta data key.
     * @param mixed  $value Meta data value.
     *
     * @return void
     */
    public function updateMeta($key, $value)
    {
        $this->getModel()->setSetting($key, $value);
    }

    /**
     *
     * @param string $key   Meta data key.
     * @param string $value Meta data value.
     *
     * @return void
     */
    public function deleteMeta($key, $value = '')
    {
        $this->getModel()->deleteSetting($key);
    }

    /**
     * @return string
     */
    public function getLastEdited()
    {
        $content = $this->getModel();

        $user = $content->getLastEditorUser();
        $displayName = $user ? $user->getName() : __('Automatic');

        return __('Updated by %1', $displayName)->__toString();
    }

    /**
     *
     * @param array $data
     *
     * @throws Exception If the post does not exist.
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->model = $data['model'] ?? [];

            if (! $this->model) {
                throw new BuilderException(
                    'Content ID #%s does not exist.'
                );
            }

            if (!isset($data['settings'])) {
                $data['settings'] = [];
            }
        }

        parent::__construct($data);
    }

    protected function getRemoteLibraryConfig()
    {
        return [
            'type' => 'block',
            'category' => $this->getName(),
            'autoImportSettings' => false,
        ];
    }

    /**
     * @param array $settings
     */
    protected function setModelSettings(array $settings)
    {
        $this->getModel()->setSettings($settings);
    }

    /**
     * @param $elementsData
     * @return void
     */
    protected function printElements($elementsData)
    {
        $elementManager = ObjectManagerHelper::getElementsManager();
        foreach ($elementsData as $elementData) {
            $element = $elementManager->createElementInstance($elementData);

            if (!$element) {
                continue;
            }

            $element
                ->setBuildableContent($this->getModel()->getOriginContent())
                ->printElement();
        }
    }
}
