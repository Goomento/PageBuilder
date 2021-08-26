<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Base;

use Exception;
use Goomento\PageBuilder\Api\ContentRepositoryInterface;
use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Base\Widget;
use Goomento\PageBuilder\Builder\Data;
use Goomento\PageBuilder\Builder\Frontend;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Builder\Preview;
use Goomento\PageBuilder\Builder\Utils;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Editor\Editor;
use Goomento\PageBuilder\Core\Files\Css\ContentCss;
use Goomento\PageBuilder\Core\Settings\Manager as SettingsManager;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticAccessToken;
use Goomento\PageBuilder\Helper\StaticAuthorization;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\Helper\StaticRequest;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;
use Goomento\PageBuilder\Helper\StaticUtils;
use Goomento\PageBuilder\Model\Content;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Json_Exception;

/**
 * Class Document
 * @package Goomento\PageBuilder\Core\Base
 */
abstract class Document extends ControlsStack
{

    /**
     * Document type meta key.
     */
    const TYPE_META_KEY = '_goomento_template_type';
    const PAGE_META_KEY = '_goomento_page_settings';

    private static $properties = [];

    /**
     * Document post data.
     *
     * Holds the document post data.
     *
     * @var ContentInterface
     */
    protected $contentModel;


    protected static function getEditorPanelCategories()
    {
        $elementsManager = StaticObjectManager::get(Elements::class);
        return $elementsManager->getCategories();
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

        if (! isset(self::$properties[ $id ])) {
            self::$properties[ $id ] = static::getProperties();
        }

        return self::getItems(self::$properties[ $id ], $key);
    }

    /**
     * @return string
     */
    public function getUniqueName()
    {
        return $this->getName() . '-' . $this->contentModel->getId();
    }

    /**
     * TODO add revision here
     */
    public function getMainId()
    {
        return $this->contentModel->getId();
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
        $elementManager = StaticObjectManager::get(Elements::class);
        /** @var Widget $widget */
        $widget = $elementManager->createElementInstance($data);

        if (!$widget) {
            throw new Exception(
                'Widget not found.'
            );
        }

        $widget->renderContent();

        return ob_get_clean();
    }


    public function getMainContent()
    {
        return $this->getContentModel();
    }

    public function getContainerAttributes()
    {
        $id = $this->getMainId();

        $attributes = [
            'data-gmt-type' => $this->getName(),
            'data-gmt-id' => $id,
            'class' => 'goomento gmt gmt-' . $id,
        ];

        /** @var Preview $preview */
        $preview = StaticObjectManager::get(Preview::class);
        if (! $preview->isPreviewMode($id)) {
            $attributes['data-gmt-settings'] = json_encode($this->getFrontendSettings());
        }

        return $attributes;
    }

    /**
     * Get view url
     */
    public function getSystemPreviewUrl()
    {
        $url = StaticUrlBuilder::getContentViewUrl($this->getMainContent());

        /**
         *
         * Filters the preview URL.
         *
         *
         * @param string   $url  Preview URL.
         * @param Document $this The document instance.
         */
        return Hooks::applyFilters('pagebuilder/document/urls/system_preview', $url, $this);
    }


    public function getExitToDashboardUrl()
    {
        $url = StaticUrlBuilder::getUrl('pagebuilder/page/grid');

        /**
         * Document "exit to dashboard" URL.
         *
         * Filters the "Exit To Dashboard" URL.
         *
         *
         * @param string   $url  The exit URL
         * @param Document $this The document instance.
         */
        return Hooks::applyFilters('pagebuilder/document/urls/exit_to_dashboard', $url, $this);
    }


    protected function _getInitialConfig()
    {
        $settings = SettingsManager::getSettingsManagersConfig();
        return [
            'id' => $this->getMainId(),
            'type' => $this->getName(),
            'settings' => $settings['page'],
            'version' => $this->getContentModel()->getSetting('GOOMENTO_VER'),
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
        $backUrl = StaticRequest::getParam('back_url');
        if (!empty($backUrl)) {
            try {
                $backUrl = StaticAccessToken::decrypt($backUrl);
            } catch (\Exception $e) {
                $backUrl = '';
            }
        }
        if (!$backUrl) {
            $content = $this->getContentModel();
            $backUrl = StaticUrlBuilder::getUrl('pagebuilder/content/edit', [
                'content_id' => $content->getId(),
                'type' => $content->getType()
            ]);
        }

        return $backUrl;
    }


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
                'default' => $this->contentModel->getTitle(),
                'label_block' => true,
                'separator' => 'none',
            ]
        );

        $can_publish = true;
        $is_published = true;
        if ($is_published || $can_publish || ! StaticObjectManager::get(Editor::class)->isEditMode()) {
            $this->addControl(
                'status',
                [
                    'label' => __('Status'),
                    'type' => Controls::SELECT,
                    'default' => $this->getMainContent()->getStatus(),
                    'options' => Content::getAvailableStatuses(),
                ]
            );
        }

        $this->endControlsSection();

        /**
         * Register document controls.
         *
         * Fires after SagoTheme registers the document controls.
         *
         *
         * @param Document $this The document instance.
         */
        Hooks::doAction('pagebuilder/documents/register_controls', $this);
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
        if (!StaticAuthorization::isCurrentUserCan($this->getContentModel()->getType() . '_save')) {
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
         * @param Document $this The current document.
         * @param $data.
         */
        Hooks::doAction('pagebuilder/document/before_save', $this, $data);

        if (! empty($data['settings'])) {
            $this->saveSettings($data['settings']);
        }

        // Don't check is_empty, because an empty array should be saved.
        if (isset($data['elements']) && is_array($data['elements'])) {
            $this->saveElements($data['elements']);
        }

        $this->saveVersion();

        /** @var ContentRepositoryInterface $contentRepository */
        $contentRepository = StaticObjectManager::get(ContentRepositoryInterface::class);

        $content = $this->getContentModel();
        // Save content
        $contentRepository->save(
            $content
        );

        // Remove ContentCss CSS
        /** @var ContentCss $content_css */
        $content_css = StaticObjectManager::create(ContentCss::class, [
            'contentId' => $this->getContentModel()->getId()
        ]);

        $content_css->update();

        /**
         * After document save.
         *
         * Fires when document save is complete.
         *
         *
         * @param Document $this The current document.
         * @param $data.
         */
        Hooks::doAction('pagebuilder/document/after_save', $this, $data);

        return true;
    }

    /**
     *
     * @return mixed
     */
    public function getEditUrl()
    {
        $url = StaticUrlBuilder::getUrl('gmtpagebuilder/editor/index/action/editor');
        /**
         * Document edit url.
         *
         * Filters the document edit url.
         *
         *
         * @param string   $url  The edit url.
         * @param Document $this The document instance.
         */
        return Hooks::applyFilters('pagebuilder/document/urls/edit', $url, $this);
    }


    public function getPreviewUrl()
    {
        /**
         * Use a static var - to avoid change the `ver` parameter on every call.
         */
        static $url;

        if (empty($url)) {
            $url = StaticUrlBuilder::getContentPreviewUrl($this->getContentModel());

            /**
             * Document preview URL.
             *
             * Filters the document preview URL.
             *
             *
             * @param string   $url  The preview URL.
             * @param Document $this The document instance.
             */
            $url = Hooks::applyFilters('pagebuilder/document/urls/preview', $url, $this);
        }

        return $url;
    }

    /**
     *
     * @param string $key
     *
     * @return array
     */
    public function getJsonMeta($key)
    {
        $meta = $this->contentModel->getData($key);

        if (is_string($meta) && ! empty($meta)) {
            $meta = json_decode($meta);
        }

        if (empty($meta)) {
            $meta = [];
        }

        return $meta;
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

        // Change the current documents, so widgets can use `documents->get_current` and other post data
        StaticObjectManager::get(DocumentsManager::class)->switchToDocument($this);

        $editor_data = [];

        foreach ($data as $element_data) {
            /** @var Elements $elementManager */
            $elementManager = StaticObjectManager::get(Elements::class);
            $element = $elementManager->createElementInstance($element_data);

            if (! $element) {
                continue;
            }

            $editor_data[] = $element->getRawData($with_html_content);
        }

        StaticObjectManager::get(DocumentsManager::class)->restoreDocument();

        return $editor_data;
    }

    /**
     *
     * @param string $status
     *
     * @return array
     */
    public function getElementsData($status = Data::STATUS_PUBLISH)
    {
        $elements = $this->getContentModel()->getElements();

        if (! empty($autosave_elements)) {
            $elements = $autosave_elements;
        }

        return $elements;
    }


    public function printElementsWithWrapper($elements_data = null)
    {
        if (! $elements_data) {
            $elements_data = $this->getElementsData();
        } ?>
		<div <?= Utils::renderHtmlAttributes($this->getContainerAttributes()); ?>>
			<div class="gmt-inner">
				<div class="gmt-section-wrap">
					<?php $this->printElements($elements_data); ?>
				</div>
			</div>
		</div>
		<?php
    }


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
     * @return ContentInterface
     */
    public function getContentModel()
    {
        return $this->contentModel;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return StaticUrlBuilder::getContentViewUrl($this->getContentModel());
    }


    public function getContent($with_css = false)
    {
        /** @var Frontend $frontend */
        $frontend = StaticObjectManager::get(Frontend::class);
        return $frontend->getBuilderContent($this->getMainContent()->getId(), $with_css);
    }


    public function delete()
    {
        StaticContent::delete($this->getMainContent()->getId());
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

        $this->getContentModel()->setElements($elements);

        /**
         * Before saving data.
         *
         * Fires before SagoTheme saves data to the database.
         *
         *
         * @param string   $status          ContentCss status.
         * @param int|bool $is_meta_updated Meta ID if the key didn't exist, true on successful update, false on failure.
         */
        Hooks::doAction('pagebuilder/db/before_save', $this->getContentModel()->getStatus());

        /** @var Data $data */
        $data = StaticObjectManager::get(Data::class);
        $data->savePlainText($this->getContentModel()->getId());

        /**
         * After saving data.
         *
         * Fires after SagoTheme saves data to the database.
         *
         *
         * @param int   $post_id     The ID of the post.
         * @param array $editor_data Sanitize posted data.
         */
        Hooks::doAction('pagebuilder/editor/after_save', $this->getContentModel()->getId(), $editor_data);
    }

    public function saveVersion()
    {
        // Save per revision.
        $this->updateMeta('GOOMENTO_VER', Configuration::VERSION);

        /**
         * Document version save.
         *
         * Fires when document version is saved on SagoTheme.
         * Will not fire during SagoTheme Upgrade.
         *
         *
         * @param Document $this The current document.
         *
         */
        Hooks::doAction('pagebuilder/document/save_version', $this);
    }

    /**
     * @deprecated
     */
    public function getTemplateType()
    {
        return $this->getMainMeta(self::TYPE_META_KEY);
    }

    /**
     *
     * @param string $key Meta data key.
     *
     * @return mixed
     */
    public function getMainMeta($key)
    {
        return $this->contentModel->getData($key);
    }

    /**
     *
     * @param string $key   Meta data key.
     * @param string $value Meta data value.
     *
     * @return bool|int
     */
    public function updateContentData($key, $value)
    {
        return $this->getContentModel()->setSetting($key, $value);
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
        $this->getContentModel()->setSetting($key, $value);
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
        $this->getContentModel()->deleteSetting($key);
    }

    /**
     *
     */
    public function getLastEdited()
    {
        $content = $this->getContentModel();

        $user = $content->getLastEditorUser();
        $display_name = $user ? $user->getName() : 'Automatic';

        return __('Updated %1', StaticUtils::timeElapsedString($content->getUpdateTime()), $display_name);
    }

    /**
     *
     * @param array $data
     *
     * @throws Exception If the post does not exist.
     */
    public function __construct(array $data = [])
    {
        if ($data) {
            $this->contentModel = StaticContent::get($data['id']);

            if (!$this->contentModel) {
                throw new Exception(
                    'Content ID #%s does not exist.', $data['post_id']
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
     *
     * @param $settings
     */
    protected function saveSettings($settings)
    {
        $pageSettingsManager = SettingsManager::getSettingsManagers('page');
        $pageSettingsManager
            ->ajaxBeforeSaveSettings($settings, $this->getContentModel()->getId())
            ->saveSettings($settings, $this->getContentModel()->getId());
    }


    protected function printElements($elements_data)
    {
        foreach ($elements_data as $element_data) {
            $element = StaticObjectManager::get(Elements::class)->createElementInstance($element_data);

            if (! $element) {
                continue;
            }

            $element->printElement();
        }
    }
}
