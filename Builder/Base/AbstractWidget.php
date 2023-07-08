<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Exception;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Widgets\Common;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\CacheHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\StateHelper;
use Goomento\PageBuilder\Helper\TemplateHelper;

// phpcs:disable Magento2.Security.LanguageConstruct.DirectOutput
abstract class AbstractWidget extends AbstractElement
{
    /**
     * @inheriDoc
     */
    const TYPE = 'widget';

    /**
     * @inheriDoc
     */
    const NAME = 'base';

    /**
     * Template for widget rendered
     *
     * @var string
     */
    protected $template = '';

    /**
     * Name of class for widget rendering
     *
     * @var string
     */
    protected $renderer = '';

    /**
     * @var null|string
     */
    private $prefixSettingKey;

    /**
     * Widget base constructor.
     *
     * Initializing the widget base class.
     *
     *
     * @param array      $data Widget data. Default is an empty array.
     * @param array|null $args Optional. Widget default arguments. Default is null.
     * @throws Exception If arguments are missing when initializing a full widget
     *                   instance.
     *
     */
    public function __construct(array $data = [], $args = null)
    {
        parent::__construct($data, $args);

        if (isset($data['prefix_setting_key'])) {
            $this->setPrefixSettingKey($data['prefix_setting_key']);
            unset($data['prefix_setting_key']);
        }

        $isTypeInstance = $this->isTypeInstance();

        if (!$isTypeInstance && null === $args) {
            throw new BuilderException(
                'arguments is required when initializing a full widget instance.'
            );
        }
    }

    /**
     * @inheirtDoc
     */
    protected function initControls()
    {
        parent::initControls();
        HooksHelper::doAction('pagebuilder/widget/' . static::NAME . '/registered_controls', $this);
    }


    /**
     * Set template render for this element
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get template render for this element
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }


    /**
     * Set renderer class for this element
     *
     * @param string|object $renderer
     * @return $this
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Get renderer class for this element
     *
     * @return string
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @inheriDoc
     */
    protected function render()
    {
        if ($this->getTemplate() || $this->getRenderer()) {
            return TemplateHelper::getWidgetHtml($this);
        }
    }


    /**
     * Get widget icon.
     *
     * Retrieve the widget icon.
     *
     * @link https://fontawesome.com/
     *
     * @return string Widget icon.
     */
    public function getIcon()
    {
        return 'fas fa-windows';
    }

    /**
     * Get widget keywords.
     *
     * Retrieve the widget keywords.
     *
     *
     * @return array Widget keywords.
     */
    public function getKeywords()
    {
        return [];
    }

    /**
     * Get widget categories.
     *
     * Retrieve the widget categories.
     *
     *
     * @return array Widget categories.
     */
    public function getCategories()
    {
        return [ 'general' ];
    }

    /**
     * Render preview in widget sidebar
     * Example data: [
     *      'image' => 'https://example.com/image.png',
     *      'link'  => 'https://example.com',
     *      'title' => 'Click Here',
     * ]
     *
     * @return array
     */
    public function getPreviewHelper() : array
    {
        return [];
    }

    /**
     * Allow edit drag/drop in editor
     *
     * @return bool
     */
    public function isBuildable() : bool
    {
        return true;
    }

    /**
     * Get stack.
     *
     * Retrieve the widget stack of controls.
     *
     *
     * @param bool $withCommonControls Optional. Whether to include the common controls. Default is true.
     *
     * @return array Widget stack of controls.
     */
    public function getStack(bool $withCommonControls = true)
    {
        $stack = parent::getStack();
        if ($withCommonControls && Common::NAME !== $this->getName()) {
            $widgetManager = ObjectManagerHelper::getWidgetsManager();
            $commonWidget = $widgetManager->getWidgetTypes(Common::NAME);

            $stack['controls'] = array_merge($stack['controls'], $commonWidget->getControls());

            $stack['tabs'] = array_merge($stack['tabs'], $commonWidget->getTabsControls());
        }

        return $stack;
    }

    /**
     * Get widget controls pointer index.
     *
     * Retrieve widget pointer index where the next control should be added.
     *
     * While using injection point, it will return the injection point index. Otherwise index of the last control of the
     * current widget itself without the common controls, plus one.
     *
     *
     * @return int Widget controls pointer index.
     */
    public function getPointerIndex()
    {
        $injectionPoint = $this->getInjectionPoint();

        if (null !== $injectionPoint) {
            return $injectionPoint['index'];
        }

        return count($this->getStack(false)['controls']);
    }

    /**
     * Show in panel.
     *
     * Whether to show the widget in the panel or not. By default returns true.
     *
     *
     * @return bool Whether to show the widget in the panel or not.
     */
    public function showInPanel()
    {
        return true;
    }

    /**
     * Get initial config.
     *
     * Retrieve the current widget initial configuration.
     *
     * Adds more configuration on top of the controls list, the tabs assigned to
     * the control, element name, type, icon and more. This method also adds
     * widget type, keywords and categories.
     *
     *
     * @return array The initial widget config.
     */
    protected function _getInitialConfig()
    {
        $config = [
            'widget_type' => $this->getName(),
            'keywords' => $this->getKeywords(),
            'categories' => $this->getCategories(),
            'html_wrapper_class' => $this->getHtmlWrapperClass(),
            'show_in_panel' => $this->showInPanel(),
            'render_preview' => $this->renderPreview(),
            'preview_helper' => $this->getPreviewHelper(),
            'is_buildable' => $this->isBuildable(),
        ];

        $stack = ObjectManagerHelper::getControlsManager()->getElementStack($this);

        if ($stack) {
            $config['controls'] = $this->getStack(false)['controls'];
            $config['tabs_controls'] = $this->getTabsControls();
        }

        return array_merge(parent::_getInitialConfig(), $config);
    }

    /**
     * @return bool
     */
    protected function shouldPrintEmpty()
    {
        return false;
    }

    /**
     * Print widget content template.
     *
     * Used to generate the widget content template on the editor, using a
     * Backbone JavaScript template.
     *
     *
     * @param string $templateContent Template content.
     */
    protected function printTemplateContent($templateContent)
    {
        ?>
        <div class="gmt-widget-container">
            <?php parent::printTemplateContent($templateContent) ?>
        </div>
        <?php
    }

    /**
     * Parse text editor.
     *
     * Parses the content from rich text editor with shortcodes, oEmbed and
     * filtered data.
     *
     * @param string $content Text editor content.
     *
     * @return string Parsed content.
     * @deplacated
     */
    protected function parseTextEditor($content)
    {
        return HooksHelper::applyFilters('widget_text', $content, $this->getSettings())->getResult();
    }

    /**
     * Get HTML wrapper class.
     *
     * Retrieve the widget container class. Can be used to override the
     * container class for specific widgets.
     *
     */
    protected function getHtmlWrapperClass()
    {
        return 'gmt-widget-' . $this->getName();
    }

    /**
     * Add widget render attributes.
     *
     * Used to add attributes to the current widget wrapper HTML tag.
     *
     */
    protected function _addRenderAttributes()
    {
        parent::_addRenderAttributes();

        $this->addRenderAttribute(
            '_wrapper',
            'class',
            [
                'gmt-widget',
                $this->getHtmlWrapperClass(),
            ]
        );

        $this->addRenderAttribute('_wrapper', 'data-widget_type', $this->getName());
    }

    /**
     * Render widget output on the frontend.
     *
     * Used to generate the final HTML displayed on the frontend.
     *
     *
     */
    public function renderContent()
    {
        /**
         * Before widget render content.
         *
         * Fires before Goomento widget is being rendered.
         *
         *
         * @param AbstractWidget $this The current widget.
         */
        HooksHelper::doAction('pagebuilder/widget/before_render_content', $this);

        try {
            // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
            ob_start();

            $widgetReturn = $this->render();
        } catch (Exception $e) {
            if (DataHelper::isDebugMode() && StateHelper::isBuildable()) {
                $this->addRenderAttribute('_container', 'class', 'gmt-widget-debug');
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                printf('<pre class="gmt-debugging">%s</pre>', $e->__toString());
            }
        } finally {
            $widgetContent = ob_get_clean();
        }

        if (empty($widgetContent) && !empty($widgetReturn)) {
            $widgetContent = $widgetReturn;
        }

        if (empty($widgetContent)) {
            return;
        }

        $settings = $this->getSettingsForDisplay();

        $this->addRenderAttribute('_container', 'class', 'gmt-widget-container');

        if (!empty($settings['_hover_animation'])) {
            $this->addRenderAttribute('_container', 'class', 'gmt-animation-' . trim($settings['_hover_animation']));
        }

        if ($this->getScriptDepends()) {
            $frontendSettings = $this->getFrontendSettings();
            $initScripts = [];

            foreach ($this->getScriptDepends() as $script) {
                $initScripts[$script] = $frontendSettings ?: [];
            }
            // Use `_container` for render via AJAX
            $this->addRenderAttribute('_container', 'data-mage-init', DataHelper::encode($initScripts));
        }

        ?>
        <div <?= /** @noEscape */ $this->getRenderAttributeString('_container') ?>>
            <?php

            /**
             * Render widget content.
             *
             * Filters the widget content before it's rendered.
             *
             *
             * @param string      $widgetContent The content of the widget.
             * @param AbstractWidget $this           The widget.
             */
            $widgetContent = HooksHelper::applyFilters('pagebuilder/widget/render_content', $widgetContent, $this)->getResult();

            echo $widgetContent; // XSS ok.
            ?>
        </div>
        <?php
    }

    /**
     * Render widget plain content.
     *
     */
    public function renderPlainContent()
    {
        $this->printContent();
    }

    /**
     * Before widget rendering.
     *
     * Used to add stuff before the widget `_wrapper` element.
     *
     */
    public function beforeRender()
    {
        ?>
        <div <?php $this->printRenderAttributeString('_wrapper'); ?>>
        <?php
    }

    /**
     * After widget rendering.
     *
     * Used to add stuff after the widget `_wrapper` element.
     *
     */
    public function afterRender()
    {
        ?>
        </div>
        <?php
    }

    /**
     * Get the element raw data.
     *
     * Retrieve the raw element data, including the id, type, settings, child
     * elements and whether it is an inner element.
     *
     * The data with the HTML used always to display the data, but the Goomento
     * editor uses the raw data without the HTML in order not to render the data
     * again.
     *
     *
     * @param bool $withHtmlContent Optional. Whether to return the data with
     *                                HTML content or without. Used for caching.
     *                                Default is false, without HTML.
     *
     * @return array Element raw data.
     */
    public function getRawData($withHtmlContent = false)
    {
        $data = parent::getRawData($withHtmlContent);

        unset($data['isInner']);

        $data['widgetType'] = $this->getData('widgetType');

        if ($withHtmlContent) {
            if (StateHelper::isFrontend()) {
                ob_start();

                $this->printContent();

                $data['htmlCache'] = ob_get_clean();
            } else {
                $data['htmlCache'] = '';
            }
        }

        return $data;
    }

    /**
     * Print widget content.
     *
     * Output the widget final HTML on the frontend.
     *
     */
    public function printContent()
    {
        $cacheLifetime = $this->getCacheLifetime();
        $collect = function () {
            ob_start();
            $this->renderContent();
            return ob_get_clean();
        };

        if ($cacheLifetime > 0) {
            $cacheKey = $this->getCacheKey();
            echo CacheHelper::resolve($cacheKey, $collect, CacheHelper::FRONTEND_CACHE_TAG, $cacheLifetime);
        } else {
            echo $collect();
        }
    }

    /**
     * Get default data.
     *
     * Retrieve the default widget data. Used to reset the data on initialization.
     *
     *
     * @return array Default data.
     */
    protected function getDefaultData()
    {
        $data = parent::getDefaultData();

        $data['widgetType'] = '';

        return $data;
    }

    /**
     * Get default child type.
     *
     * Retrieve the widget child type based on element data.
     *
     *
     * @param array $elementData Widget ID.
     *
     * @return array|false Child type or false if it's not a valid widget.
     */
    protected function _getDefaultChildType(array $elementData)
    {
        return ObjectManagerHelper::getElementsManager()->getElementTypes('section');
    }

    /**
     * Add inline editing attributes.
     *
     * Define specific area in the element to be editable inline. The element can have several areas, with this method
     * you can set the area inside the element that can be edited inline. You can also define the type of toolbar the
     * user will see, whether it will be a basic toolbar or an advanced one.
     *
     * Note: When you use wysiwyg control use the advanced toolbar, with textarea control use the basic toolbar. Text
     * control should not have toolbar.
     *
     * @param string|array $key Element key.
     * @param string $toolbar Optional. Toolbar type. Accepted values are `advanced`, `basic` or `none`. Default is
     *                        `basic`.
     */
    public function addInlineEditingAttributes($key, string $toolbar = 'none')
    {
        if (is_string($key)) {
            $key = [$key => $key];
        }

        $prefixSettingKey = $this->getPrefixSettingKey();

        foreach ($key as $elementName => $backendKey) {
            if ($prefixSettingKey) {
                $backendKey = $prefixSettingKey . $backendKey;
            }

            $this->addRenderAttribute($elementName, [
                'class' => 'gmt-inline-editing',
                'data-gmt-setting-key' => $backendKey,
            ]);

            $this->addRenderAttribute($elementName, [
                'data-gmt-inline-editing-toolbar' => $toolbar,
            ]);
        }
    }

    /**
     * Get content of inline text
     *
     * @param string $settingKey
     * @param string $toolbar
     * @param string $wrapperTag
     * @return void
     */
    public function printInlineEditingContent(string $settingKey, string $toolbar = 'none', string $wrapperTag = 'div') : void
    {
        static $count;

        $content = $this->getSettingsForDisplay($settingKey);

        $wrapperElementKey = implode('_', [
            static::TYPE,
            static::NAME,
            $settingKey,
            ++$count,
        ]);

        $keys = [
            $wrapperElementKey => $settingKey
        ];

        $this->addInlineEditingAttributes($keys, $toolbar);

        if (is_scalar($content)) {
            $contentHtml = $content;
        } elseif (is_object($content) && method_exists($content, '__toString')) {
            $contentHtml = $content->__toString();
        } else {
            $contentHtml = print_r($content, true);
        }

        ?>
        <!-- gmt_state:is_editor -->
            <<?= /** @noEscape */ $wrapperTag ?> <?= /** @noEscape */ $this->getRenderAttributeString($wrapperElementKey) ?>>
        <!-- /gmt_state:is_editor -->

            <?= /** @noEscape */ $contentHtml ?>

        <!-- gmt_state:is_editor -->
            </<?= /** @noEscape */ $wrapperTag ?>>
        <!-- /gmt_state:is_editor -->
        <?php
    }


    /**
     * @param string $pluginTitle Plugin's title
     * @param string $since Plugin version widget was deprecated
     * @param string $last Plugin version in which the widget will be removed
     * @param string $replacement Widget replacement
     * @throws Exception
     */
    protected function deprecatedNotice(string $pluginTitle, string $since, string $last = '', string $replacement = '')
    {
        $this->startControlsSection(
            'Deprecated',
            [
                'label' => __('Deprecated'),
            ]
        );

        $this->addControl(
            'deprecated_notice',
            [
                'type' => Controls::DEPRECATED_NOTICE,
                'widget' => $this->getTitle(),
                'since' => $since,
                'last' => $last,
                'plugin' => $pluginTitle,
                'replacement' => $replacement,
            ]
        );

        $this->endControlsSection();
    }

    /**
     * Decide to render preview in Editor or not
     *
     * @return bool
     */
    protected function renderPreview() : bool
    {
        return true;
    }

    /**
     * Use for stores the parent-child mapping key, for example maps parent setting which rendered in child - inline editing.
     *
     * @return null|string
     */
    public function getPrefixSettingKey() : ?string
    {
        return $this->prefixSettingKey;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function setPrefixSettingKey(string $key) : AbstractWidget
    {
        $this->prefixSettingKey = $key;
        return $this;
    }
}
