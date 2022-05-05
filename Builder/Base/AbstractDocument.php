<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Modules\Frontend;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Managers\PageSettings;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Builder\Css\ContentCss;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\AuthorizationHelper;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;
use Goomento\PageBuilder\Model\Content;
use Magento\Framework\Exception\LocalizedException;

abstract class AbstractDocument extends ControlsStack
{
    private static $properties = [];

    /**
     * Stores the Content ID
     *
     * @var mixed
     */
    protected $contentId;

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
     * @return string Element title.
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
        return $this->getName() . '-' . $this->contentId;
    }

    /**
     * Get model id
     */
    public function getModelId()
    {
        return $this->getModel()->getId();
    }

    /**
     *
     * @param $data
     *
     * @throws Exception If the widget was not found.
     *
     * @return string
     */
    public function renderElement($data)
    {
        // Start buffering
        ob_start();

        /** @var Elements $elementManager */
        $elementManager = ObjectManagerHelper::get(Elements::class);
        /** @var AbstractWidget $widget */
        $widget = $elementManager->createElementInstance($data);

        if (!$widget) {
            throw new Exception(
                'Widget not found.'
            );
        }

        $widget->renderContent();

        return ob_get_clean();
    }

    /**
     * @deprecated
     * @return ContentInterface
     */
    public function getMainContent()
    {
        return $this->getModel();
    }

    public function getContainerAttributes()
    {
        $attributes = [
            'data-gmt-type' => $this->getName(),
            'data-gmt-id' => $this->getId(),
            'class' => 'goomento gmt gmt-' . $this->getId(),
        ];

        if (!StateHelper::isPreviewMode()) {
            $attributes['data-gmt-settings'] = json_encode($this->getFrontendSettings());
        }

        return $attributes;
    }

    /**
     * Get view url
     */
    public function getSystemPreviewUrl()
    {
        $url = UrlBuilderHelper::getContentViewUrl($this->getMainContent());

        /**
         *
         * Filters the preview URL.
         *
         *
         * @param string   $url  Preview URL.
         * @param AbstractDocument $this The document instance.
         */
        return HooksHelper::applyFilters('pagebuilder/document/urls/system_preview', $url, $this);
    }


    protected function _getInitialConfig()
    {
        $settingManager = ObjectManagerHelper::getSettingsManager();
        $settings = $settingManager->getSettingsManagersConfig();
        return [
            'id' => $this->getModelId(),
            'type' => $this->getName(),
            'settings' => $settings['page'],
            'version' => $this->getModel()->getSetting('GOOMENTO_VER'),
            'remoteLibrary' => $this->getRemoteLibraryConfig(),
            'last_edited' => $this->getLastEdited(),
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
            $content = $this->getModel();
            $backUrl = UrlBuilderHelper::getUrl('pagebuilder/content/edit', [
                'content_id' => $content->getId(),
                'type' => $content->getType()
            ]);
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

        $this->addControl(
            'title',
            [
                'label' => __('Title'),
                'type' => Controls::TEXT,
                'default' => $this->getModel()->getTitle(),
                'label_block' => true,
                'separator' => 'none',
            ]
        );

        $this->addControl(
            'is_active',
            [
                'label' => __('Enabled'),
                'type' => Controls::SELECT,
                'default' => (int) $this->getModel()->getIsActive(),
                'options' => [
                    '1' => __('Enabled'),
                    '0' => __('Disabled'),
                ],
            ]
        );

        $this->endControlsSection();

        /**
         * Register document controls.
         *
         * Fires after SagoTheme registers the document controls.
         *
         *
         * @param AbstractDocument $this The document instance.
         */
        HooksHelper::doAction('pagebuilder/documents/register_controls', $this);
    }

    /**
     *
     * @param $data
     *
     * @return bool
     * @throws Exception
     */
    public function save($data)
    {
        if (!AuthorizationHelper::isCurrentUserCan($this->getModel()->getRoleName('save'))) {
            throw new LocalizedException(
                __('Sorry, you need permissions to view this content')
            );
        }

        /**
         * Before document save.
         *
         * Fires when document save starts on SagoTheme.
         *
         *
         * @param AbstractDocument $this The current document.
         * @param $data.
         */
        HooksHelper::doAction('pagebuilder/document/before_save', $this, $data);

        if (!empty($data['settings'])) {
            $this->saveSettings($data['settings']);
        }

        if (isset($data['elements']) && is_array($data['elements'])) {
            $this->saveElements($data['elements']);
        }

        $this->saveVersion();

        $this->saveModel();

        // Remove ContentCss CSS
        $contentCss = new ContentCss($this->getModel()->getId());

        $contentCss->update();

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
        return HooksHelper::applyFilters('pagebuilder/document/urls/edit', $url, $this);
    }


    public function getPreviewUrl()
    {
        /**
         * Use a static var - to avoid change the `ver` parameter on every call.
         */
        static $url;

        if (empty($url)) {
            $url = UrlBuilderHelper::getContentPreviewUrl($this->getModel());

            /**
             * Document preview URL.
             *
             * Filters the document preview URL.
             *
             *
             * @param string   $url  The preview URL.
             * @param AbstractDocument $this The document instance.
             */
            $url = HooksHelper::applyFilters('pagebuilder/document/urls/preview', $url, $this);
        }

        return $url;
    }

    /**
     *
     * @param null $data
     * @param bool $with_html_content
     *
     * @return array
     */
    public function getElementsRawData($data = null, $with_html_content = false)
    {
        if (is_null($data)) {
            $data = $this->getElementsData();
        }

        $editor_data = [];

        foreach ($data as $element_data) {
            /** @var Elements $elementManager */
            $elementManager = ObjectManagerHelper::get(Elements::class);
            $element = $elementManager->createElementInstance($element_data);

            if (!$element) {
                continue;
            }

            $editor_data[] = $element->getRawData($with_html_content);
        }

        return $editor_data;
    }

    /**
     *
     * @return array
     */
    public function getElementsData()
    {
        return $this->getModel()->getElements();
    }


    public function printElementsWithWrapper($elements_data = null)
    {
        if (!$elements_data) {
            $elements_data = $this->getElementsData();
        } ?>
		<div <?= DataHelper::renderHtmlAttributes($this->getContainerAttributes()); ?>>
			<div class="gmt-inner">
				<div class="gmt-section-wrap">
					<?php $this->printElements($elements_data); ?>
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


    public function getPanelPageSettings()
    {
        return [
            'title' => __('%s Settings', static::getTitle()),
        ];
    }

    /**
     * @return Content
     */
    public function getModel()
    {
        return ContentHelper::get(
            $this->contentId
        );
    }

    /**
     * Save model
     */
    public function saveModel()
    {
        ContentHelper::save(
            $this->getModel()
        );
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return $this->getModel()->getStatus() === ContentInterface::STATUS_PUBLISHED
            ? UrlBuilderHelper::getPublishedContentUrl($this->getModel())
            : UrlBuilderHelper::getContentViewUrl($this->getModel());
    }

    /**
     * @param $with_css
     * @return string
     */
    public function getContent($with_css = false)
    {
        /** @var Frontend $frontend */
        $frontend = ObjectManagerHelper::get(Frontend::class);
        return $frontend->getBuilderContent($this->getModel()->getId(), $with_css);
    }

    /**
     * @return void
     */
    public function delete()
    {
        ContentHelper::delete($this->getModel()->getId());
    }

    /**
     * Save editor elements.
     *
     * Save data from the editor to the database.
     *
     *
     * @param array $elements
     * @throws Exception
     */
    protected function saveElements($elements)
    {
        $editor_data = $this->getElementsRawData($elements);

        $this->getModel()->setElements($elements);

        /**
         * Before saving data.
         *
         * Fires before SagoTheme saves data to the database.
         *
         *
         * @param string   $status          ContentCss status.
         * @param int|bool $is_meta_updated Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        HooksHelper::doAction('pagebuilder/db/before_save', $this->getModel()->getStatus());

        /**
         * After saving data.
         *
         * Fires after SagoTheme saves data to the database.
         *
         *
         * @param int   $post_id     The ID of the post.
         * @param array $editor_data Sanitize posted data.
         */
        HooksHelper::doAction('pagebuilder/editor/after_save', $this->getModel()->getId(), $editor_data);
    }

    public function saveVersion()
    {
        // Save per revision.
        $this->updateMeta('version', Configuration::version());

        /**
         * Document version save.
         *
         * Fires when document version is saved on SagoTheme.
         * Will not fire during SagoTheme Upgrade.
         *
         *
         * @param AbstractDocument $this The current document.
         *
         */
        HooksHelper::doAction('pagebuilder/document/save_version', $this);
    }

    /**
     *
     * @param string $key Meta data key.
     *
     * @return mixed
     * @deprecated
     */
    public function getMainMeta($key)
    {
        return $this->getModel()->getData($key);
    }

    /**
     *
     * @param string $key   Meta data key.
     * @param string $value Meta data value.
     *
     * @return Content|ContentInterface
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
     *
     */
    public function getLastEdited()
    {
        $content = $this->getModel();

        $user = $content->getLastEditorUser();
        $display_name = $user ? $user->getName() : __('Automatic');

        return __('Updated %1', DataHelper::timeElapsedString($content->getUpdateTime()), $display_name);
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
            $this->contentId = $data['id'];

            if (!$this->getModel()) {
                throw new Exception(
                    'Content ID #%s does not exist.', $data['id']
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
     * @param $settings
     * @throws Exception
     */
    protected function saveSettings($settings)
    {
        $settingsManager = ObjectManagerHelper::getSettingsManager();
        $pageSettingsManager = $settingsManager->getSettingsManagers(PageSettings::NAME);
        $pageSettingsManager
            ->beforeSaveSettings($settings, $this->getModel()->getId())
            ->saveSettings($settings, $this->getModel()->getId());
    }


    protected function printElements($elements_data)
    {
        foreach ($elements_data as $element_data) {
            $element = ObjectManagerHelper::get(Elements::class)->createElementInstance($element_data);

            if (!$element) {
                continue;
            }

            $element->printElement();
        }
    }
}
