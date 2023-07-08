<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Helper\ThemeHelper;
use Magento\Framework\Phrase;

abstract class AbstractElement extends ControlsStack
{
    /**
     * @inheirtDoc
     */
    const TYPE = 'element';

    /**
     * @inheirtDoc
     */
    const NAME = 'base';

    /**
     * Default cache lifetime as per element in second
     */
    const CACHE_LIFETIME = 300;

    /**
     * Child elements.
     *
     * Holds all the child elements of the element.
     *
     *
     * @var AbstractElement[]
     */
    private $children;

    /**
     * Element render attributes.
     *
     * Holds all the render attributes of the element. Used to store data like
     * the HTML class name and the class value, or HTML element ID name and value.
     *
     *
     * @var array
     */
    private $renderAttributes = [];

    /**
     * Element default arguments.
     *
     * @var array
     */
    private $defaultArgs = [];

    /**
     * Is type instance.
     *
     * Whether the element is an instance of that type or not.
     *
     *
     * @var bool
     */
    private $isTypeInstance = true;

    /**
     * Depended scripts.
     *
     * Holds all the element depended scripts to enqueue.
     *
     *
     * @var array
     */
    private $dependedScripts = [];

    /**
     * Depended styles.
     *
     * Holds all the element depended styles to enqueue.
     *
     *
     * @var array
     */
    private $dependedStyles = [];

    /**
     * @var BuildableContentInterface|null
     */
    private $buildableContent;

    /**
     * @var null|AbstractElement
     */
    private $parent;

    /**
     * Element base constructor.
     *
     * Initializing the element base class using `$data` and `$args`.
     *
     * The `$data` parameter is required for a normal instance because of the
     * way Goomento renders data when initializing elements.
     *
     *
     * @param array      $data Optional. Element data. Default is an empty array.
     * @param array|null $args Optional. Element default arguments. Default is null.
     **/
    public function __construct(array $data = [], array $args = null)
    {
        if ($data) {
            $this->isTypeInstance = false;
        } elseif ($args) {
            $this->defaultArgs = $args;
        }

        if (isset($data['parent']) && $data['parent'] instanceof AbstractElement) {
            $this->setParent($data['parent']);
            unset($data['parent']);
        }

        if (isset($data['buildable_content']) && $data['buildable_content'] instanceof BuildableContentInterface) {
            $this->setBuildableContent($data['buildable_content']);
            unset($data['buildable_content']);
        }

        parent::__construct($data);
    }

    /**
     * Add script depends.
     *
     * Register new script to enqueue by the handler.
     *
     *
     * @param string $handler Depend script handler.
     */
    public function addScriptDepends(string $handler)
    {
        $this->dependedScripts[] = $handler;
    }

    /**
     * @inheirtDoc
     */
    protected function initControls()
    {
        parent::initControls();
        HooksHelper::doAction('pagebuilder/element/' . static::NAME . '/registered_controls', $this);
    }

    /**
     * Add style depends.
     *
     * Register new style to enqueue by the handler.
     *
     *
     * @param string $handler Depend style handler.
     */
    public function addStyleDepends(string $handler)
    {
        $this->dependedStyles[] = $handler;
    }

    /**
     * Get script dependencies.
     *
     * Retrieve the list of script dependencies the element requires.
     *
     *
     * @return array Element scripts dependencies.
     */
    public function getScriptDepends()
    {
        return $this->dependedScripts;
    }

    /**
     * Enqueue scripts.
     *
     * Registers all the scripts defined as element dependencies and enqueues them.
     *
     */
    public function enqueueScripts()
    {
        foreach ($this->getScriptDepends() as $script) {
            ThemeHelper::enqueueScript($script);
        }
    }

    /**
     * Get style dependencies.
     *
     * Retrieve the list of style dependencies the element requires.
     *
     *
     * @return array Element styles dependencies.
     */
    public function getStyleDepends()
    {
        return $this->dependedStyles;
    }

    /**
     * Enqueue styles.
     *
     * Registers all the styles defined as element dependencies and enqueues
     * them. Use `get_style_depends()` method to add custom style dependencies.
     *
     */
    public function enqueueStyles()
    {
        foreach ($this->getStyleDepends() as $style) {
            ThemeHelper::enqueueStyle($style);
        }
    }

    /**
     * Get default child type.
     *
     * Retrieve the default child type based on element data.
     *
     * Note that not all elements support children.
     *
     *
     * @param array $elementData Element data.
     *
     * @return AbstractElement
     */
    abstract protected function _getDefaultChildType(array $elementData);

    /**
     * Before element rendering.
     *
     * Used to add stuff before the element.
     *
     */
    public function beforeRender()
    {
    }

    /**
     * After element rendering.
     *
     * Used to add stuff after the element.
     *
     */
    public function afterRender()
    {
    }

    /**
     * Get element title.
     *
     * Retrieve the element title.
     *
     *
     * @return string|Phrase Element title.
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Get element icon.
     *
     * Retrieve the element icon.
     *
     *
     * @return string Element icon.
     * @deprecated
     */
    public function getIcon()
    {
        return 'fas fa-columns';
    }

    /**
     * Whether the reload preview is required.
     *
     * Used to determine whether the reload preview is required or not.
     *
     *
     * @return bool Whether the reload preview is required.
     */
    public function isReloadPreviewRequired()
    {
        return false;
    }

    /**
     * This config use for printing empty element such as empty HTML content
     *
     * @return bool
     */
    protected function shouldPrintEmpty()
    {
        return true;
    }

    /**
     * Get child elements.
     *
     * Retrieve all the child elements of this element.
     *
     *
     * @return AbstractElement[] Child elements.
     */
    public function getChildren()
    {
        if (null === $this->children) {
            $this->initChildren();
        }

        return $this->children;
    }

    /**
     * Get default arguments.
     *
     * Retrieve the element default arguments. Used to return all the default
     * arguments or a specific default argument, if one is set.
     *
     *
     * @param array $item Optional. Default is null.
     *
     * @return array Default argument(s).
     */
    public function getDefaultArgs($item = null)
    {
        return self::getItems($this->defaultArgs, $item);
    }

    /**
     * Add new child element.
     *
     * Register new child element to allow hierarchy.
     *
     * @param array $childData Child element data.
     * @param array $childArgs Child element arguments.
     *
     * @return AbstractElement|false Child element instance, or false if failed.
     */
    public function addChild(array $childData, array $childArgs = [])
    {
        if (null === $this->children) {
            $this->initChildren();
        }

        $childType = $this->getChildType($childData);

        if (!$childType) {
            return false;
        }

        $child = ObjectManagerHelper::getElementsManager()
            ->createElementInstance($childData, $childArgs, $childType);

        if ($child) {
            $this->children[$child->getId()] = $child;
        }

        return $child;
    }

    /**
     * Add render attribute.
     *
     * Used to add attributes to a specific HTML element.
     *
     * The HTML tag is represented by the element parameter, then you need to
     * define the attribute key and the attribute key. The final result will be:
     * `<element attribute_key="attribute_value">`.
     *
     * Example usage:
     *
     * `$this->addRenderAttribute( 'wrapper', 'class', 'custom-widget-wrapper-class' );`
     * `$this->addRenderAttribute( 'widget', 'id', 'custom-widget-id' );`
     * `$this->addRenderAttribute( 'button', [ 'class' => 'custom-button-class', 'id' => 'custom-button-id' ] );`
     *
     *
     * @param array|string $element   The HTML element.
     * @param array|string $key       Optional. Attribute key. Default is null.
     * @param array|string $value     Optional. Attribute value. Default is null.
     * @param bool         $overwrite Optional. Whether to overwrite existing
     *                                attribute. Default is false, not to overwrite.
     *
     * @return AbstractElement Current instance of the element.
     */
    public function addRenderAttribute($element, $key = null, $value = null, $overwrite = false)
    {
        if (is_array($element)) {
            foreach ($element as $elementKey => $attributes) {
                $this->addRenderAttribute($elementKey, $attributes, null, $overwrite);
            }

            return $this;
        }

        if (is_array($key)) {
            foreach ($key as $attributeKey => $attributes) {
                $this->addRenderAttribute($element, $attributeKey, $attributes, $overwrite);
            }

            return $this;
        }

        if (empty($this->renderAttributes[ $element ][ $key ])) {
            $this->renderAttributes[ $element ][ $key ] = [];
        }

        $value = (array) $value;

        if ($overwrite) {
            $this->renderAttributes[ $element ][ $key ] = $value;
        } else {
            $this->renderAttributes[ $element ][ $key ] = array_merge($this->renderAttributes[ $element ][ $key ], $value);
        }

        return $this;
    }

    /**
     * Get Render Attributes
     *
     * Used to retrieve render attribute.
     *
     * The returned array is either all elements and their attributes if no `$element` is specified, an array of all
     * attributes of a specific element or a specific attribute properties if `$key` is specified.
     *
     * Returns null if one of the requested parameters isn't set.
     *
     * @param string $element
     * @param string $key
     *
     * @return array
     */
    public function getRenderAttributes($element = '', $key = '')
    {
        $attributes = $this->renderAttributes;

        if ($element) {
            if (!isset($attributes[ $element ])) {
                return null;
            }

            $attributes = $attributes[ $element ];

            if ($key) {
                if (!isset($attributes[ $key ])) {
                    return null;
                }

                $attributes = $attributes[ $key ];
            }
        }

        return $attributes;
    }

    /**
     * Set render attribute.
     *
     * Used to set the value of the HTML element render attribute or to update
     * an existing render attribute.
     *
     *
     * @param array|string $element The HTML element.
     * @param array|string $key     Optional. Attribute key. Default is null.
     * @param array|string $value   Optional. Attribute value. Default is null.
     *
     * @return AbstractElement Current instance of the element.
     */
    public function setRenderAttribute($element, $key = null, $value = null)
    {
        return $this->addRenderAttribute($element, $key, $value, true);
    }

    /**
     * Remove render attribute.
     *
     * Used to remove an element (with its keys and their values), key (with its values),
     * or value/s from an HTML element's render attribute.
     *
     *
     * @param string $element The HTML element.
     * @param string|null $key Optional. Attribute key. Default is null.
     * @param null $values Optional. Attribute value/s. Default is null.
     */
    public function removeRenderAttribute(string $element, string $key = null, $values = null)
    {
        if ($key && ! isset($this->renderAttributes[ $element ][ $key ])) {
            return;
        }

        if ($values) {
            $values = (array) $values;

            $this->renderAttributes[ $element ][ $key ] = array_diff($this->renderAttributes[ $element ][ $key ], $values);

            return;
        }

        if ($key) {
            unset($this->renderAttributes[ $element ][ $key ]);

            return;
        }

        if (isset($this->renderAttributes[ $element ])) {
            unset($this->renderAttributes[ $element ]);
        }
    }

    /**
     * Get render attribute string.
     *
     * Used to retrieve the value of the render attribute.
     *
     *
     * @param string $element The element.
     *
     * @return string Render attribute string, or an empty string if the attribute
     *                is empty or not exist.
     */
    public function getRenderAttributeString($element)
    {
        if (empty($this->renderAttributes[ $element ])) {
            return '';
        }

        return DataHelper::renderHtmlAttributes($this->renderAttributes[$element]);
    }

    /**
     * Print render attribute string.
     *
     * Used to output the rendered attribute.
     *
     *
     * @param array|string $element The element.
     */
    public function printRenderAttributeString($element)
    {
        // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
        echo $this->getRenderAttributeString($element); // XSS ok.
    }

    /**
     * Print element.
     *
     * Used to generate the element final HTML on the frontend and the editor.
     *
     */
    public function printElement()
    {
        $elementType = $this->getName();

        /**
         * Before frontend element render.
         *
         * Fires before Goomento element is rendered in the frontend.
         *
         *
         * @param AbstractElement $this The element.
         */
        HooksHelper::doAction('pagebuilder/frontend/before_render', $this);

        /**
         * Before frontend element render.
         *
         * Fires before Goomento element is rendered in the frontend.
         *
         * The dynamic portion of the hook name, `$elementType`, refers to the element type.
         *
         *
         * @param AbstractElement $this The element.
         */
        HooksHelper::doAction("pagebuilder/frontend/{$elementType}/before_render", $this);

        ob_start();
        $this->printContent();
        $content = ob_get_clean();

        $shouldRender = (!empty($content) || $this->shouldPrintEmpty());

        /**
         * Should the element be rendered for frontend
         *
         * Filters if the element should be rendered on frontend.
         *
         *
         * @param bool true The element.
         * @param AbstractElement $this The element.
         */
        $shouldRender = HooksHelper::applyFilters("pagebuilder/frontend/{$elementType}/should_render", $shouldRender, $this)->getResult();

        if ($shouldRender) {
            $this->_addRenderAttributes();

            $this->beforeRender();
            // phpcs:ignore Magento2.Security.LanguageConstruct.DirectOutput
            echo $content;
            $this->afterRender();

            HooksHelper::addAction('pagebuilder/frontend/enqueue_scripts', [$this, 'enqueue']);
        }


        /**
         * After frontend element render.
         *
         * Fires after Goomento element is rendered in the frontend.
         *
         * The dynamic portion of the hook name, `$elementType`, refers to the element type.
         *
         *
         * @param AbstractElement $this The element.
         */
        HooksHelper::doAction("pagebuilder/frontend/{$elementType}/after_render", $this);

        /**
         * After frontend element render.
         *
         * Fires after Goomento element is rendered in the frontend.
         *
         *
         * @param AbstractElement $this The element.
         */
        HooksHelper::doAction('pagebuilder/frontend/after_render', $this);
    }

    /**
     * Adding JS/CSS onto the HTML output
     * This will be running under hook
     *
     * @return void
     */
    public function enqueue()
    {
        // Add the styles to HTML, the script will be added directly in HTML
        // $this->enqueueScripts();
        $this->enqueueStyles();
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
        $data = $this->getData();

        $elements = [];

        foreach ($this->getChildren() as $child) {
            $elements[] = $child->getRawData($withHtmlContent);
        }

        return [
            'id' => $this->getId(),
            'elType' => $data['elType'],
            'settings' => $data['settings'],
            'elements' => $elements,
            'isInner' => $data['isInner'],
        ];
    }

    /**
     * Get unique selector.
     *
     * Retrieve the unique selector of the element. Used to set a unique HTML
     * class for each HTML element. This way Goomento can set custom styles for
     * each element.
     *
     *
     * @return string Unique selector.
     */
    public function getUniqueSelector()
    {
        return '.gmt-element-' . $this->getId();
    }

    /**
     * Is type instance.
     *
     * Used to determine whether the element is an instance of that type or not.
     *
     *
     * @return bool Whether the element is an instance of that type.
     */
    public function isTypeInstance()
    {
        return $this->isTypeInstance;
    }

    /**
     * Add render attributes.
     *
     * Used to add attributes to the current element wrapper HTML tag.
     *
     */
    protected function _addRenderAttributes()
    {
        $id = $this->getId();

        $settings = $this->getSettingsForDisplay();
        $frontendSettings = $this->getFrontendSettings();
        $controls = $this->getControls();

        $this->addRenderAttribute('_wrapper', [
            'class' => [
                'gmt-element',
                'gmt-element-' . $id,
            ],
            'data-id' => $id,
            'data-element_type' => $this->getType(),
        ]);

        $classSettings = [];

        foreach ($settings as $settingKey => $setting) {
            if (isset($controls[ $settingKey ]['prefix_class'])) {
                $classSettings[ $settingKey ] = $setting;
            }
        }

        foreach ($classSettings as $settingKey => $setting) {
            if (empty($setting) && '0' !== $setting) {
                continue;
            }

            $this->addRenderAttribute('_wrapper', 'class', $controls[ $settingKey ]['prefix_class'] . $setting);
        }

        if (!empty($settings['animation']) || ! empty($settings['_animation'])) {
            // Hide the element until the animation begins
            $this->addRenderAttribute('_wrapper', 'class', 'gmt-invisible');
        }

        if (!empty($settings['_element_id'])) {
            $this->addRenderAttribute('_wrapper', 'id', trim($settings['_element_id']));
        }

        if ($frontendSettings) {
            // This FE config use for system handlers, such as background video player at section ...
            $this->addRenderAttribute('_wrapper', 'data-settings', DataHelper::encode($frontendSettings));
        }

        /**
         * After element attribute rendered.
         *
         * Fires after the attributes of the element HTML tag are rendered.
         *
         *
         * @param AbstractElement $this The element.
         */
        HooksHelper::doAction('pagebuilder/element/after_add_attributes', $this);
    }

    /**
     * Get default data.
     *
     * Retrieve the default element data. Used to reset the data on initialization.
     *
     *
     * @return array Default data.
     */
    protected function getDefaultData()
    {
        $data = parent::getDefaultData();

        return array_merge(
            $data,
            [
                'elements' => [],
                'isInner' => false,
            ]
        );
    }

    /**
     * Print element content.
     *
     * Output the element final HTML on the frontend.
     *
     */
    public function printContent()
    {
        foreach ($this->getChildren() as $child) {
            $child
                ->setBuildableContent($this->getBuildableContent())
                ->setParent($this);

            $child->printElement();
        }
    }

    /**
     * Get initial config.
     *
     * Retrieve the current element initial configuration.
     *
     * Adds more configuration on top of the controls list and the tabs assigned
     * to the control. This method also adds element name, type, icon and more.
     *
     *
     * @return array The initial config.
     */
    protected function _getInitialConfig()
    {
        return [
            'name' => $this->getName(),
            'elType' => $this->getType(),
            'title' => $this->getTitle(),
            'icon' => $this->getIcon(),
            'reload_preview' => $this->isReloadPreviewRequired(),
        ];
    }

    /**
     * Get child type.
     *
     * Retrieve the element child type based on element data.
     *
     *
     * @param array $elementData Element ID.
     *
     * @return AbstractElement|false Child type or false if type not found.
     */
    private function getChildType($elementData)
    {
        $childType = $this->_getDefaultChildType($elementData);

        // If it's not a valid widget ( like a deactivated plugin )
        if (!$childType) {
            return false;
        }

        /**
         * Element child type.
         *
         * Filters the child type of the element.
         *
         *
         * @param AbstractElement $childType   The child element.
         * @param array        $elementData The original element ID.
         * @param AbstractElement $this         The original element.
         */
        return HooksHelper::applyFilters('pagebuilder/element/get_child_type', $childType, $elementData, $this)->getResult();
    }

    /**
     * Initialize children.
     *
     * Initializing the element child elements.
     *
     */
    private function initChildren()
    {
        $this->children = [];

        $childrenData = $this->getData('elements');

        if (!$childrenData) {
            return;
        }

        foreach ($childrenData as $childData) {
            if (!$childData) {
                continue;
            }

            $this->addChild($childData);
        }
    }

    /**
     * @return BuildableContentInterface|null
     */
    public function getBuildableContent() : ?BuildableContentInterface
    {
        return $this->buildableContent;
    }

    /**
     * Set buildable content, which available in template only
     *
     * @param BuildableContentInterface|null $buildableContent
     * @return AbstractElement
     */
    public function setBuildableContent(?BuildableContentInterface $buildableContent = null)
    {
        $this->buildableContent = $buildableContent;
        return $this;
    }

    /**
     * @param AbstractElement $element
     * @return $this
     */
    public function setParent(AbstractElement $element) : AbstractElement
    {
        $this->parent = $element;
        return $this;
    }

    /**
     * @return AbstractElement|null
     */
    public function getParent() : ?AbstractElement
    {
        return $this->parent;
    }

    /**
     * @return string
     */
    public function getCacheKey() : string
    {
        $settings = $this->getSettings();
        $key = $settings['_cache_key'] ?? null;
        if (null === $key) {
            $key = EncryptorHelper::uniqueContextId($settings, 12);
        }
        return $key;
    }

    /**
     * @return int|null
     */
    public function getCacheLifetime() : ?int
    {
        return $this->getSettingsForDisplay('_cache_lifetime') ?: static::CACHE_LIFETIME;
    }

    /**
     * @param string ...$keys
     * @return string
     */
    public static function buildPrefixKey(...$keys) : string
    {
        if (count($keys) === 1 && $keys[0] === null) {
            $keys = [];
        } else {
            $keys = (array) $keys;
        }
        array_unshift($keys, static::NAME);
        return implode('_', $keys) . '_';
    }
}
