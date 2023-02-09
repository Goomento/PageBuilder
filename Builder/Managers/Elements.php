<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Exception;
use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\LoggerHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Elements
{
    /**
     * @var
     */
    private $elementTypes;

    /**
     * @var
     */
    private $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        HooksHelper::addFilter('pagebuilder/elements/parse_settings_for_display', [$this, 'parseSettingsForDisplay']);
    }

    /**
     * Get settings for display
     *
     * @param array $elementData
     * @return array
     * @throws Exception
     */
    public function parseSettingsForDisplay(array $elementData): array
    {
        if (!isset($elementData['elType'])) {
            throw new BuilderException('Invalid Element Type for getting settings');
        }

        $instance = $this->createElementInstance($elementData);

        $settings = $instance->getSettingsForDisplay();

        $elementData['settings'] = $settings;

        return $elementData;
    }

    /**
     * @param array $elementData
     * @param array $elementArgs
     * @param AbstractElement|null $elementType
     * @return AbstractElement|null
     */
    public function createElementInstance(array $elementData, array $elementArgs = [], AbstractElement $elementType = null)
    {
        if (null === $elementType) {
            if ('widget' === $elementData['elType']) {
                $elementType = ObjectManagerHelper::getWidgetsManager()
                    ->getWidgetTypes($elementData['widgetType']);
            } else {
                $elementType = $this->getElementTypes($elementData['elType']);
            }
        }

        if (!$elementType) {
            return null;
        }

        $args = array_merge($elementType->getDefaultArgs(), $elementArgs);

        $elementClass = get_class($elementType);

        try {
            $element = ObjectManagerHelper::create($elementClass, [
                'data' => $elementData,
                'args' => $args
            ]);
        } catch (Exception $e) {
            LoggerHelper::error($e);
            return null;
        }

        return $element;
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        if (null === $this->categories) {
            $this->initCategories();
        }

        return $this->categories;
    }

    /**
     * @param array $categories
     * @return $this
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
        return $this;
    }

    /**
     * @param $categoryName
     * @param $categoryProperties
     */
    public function addCategory($categoryName, $categoryProperties)
    {
        if (null === $this->categories) {
            $this->getCategories();
        }

        if (!isset($this->categories[ $categoryName ])) {
            $this->categories[ $categoryName ] = $categoryProperties;
        }
    }

    /**
     * @param AbstractElement $element
     * @return bool
     */
    public function registerElementType(AbstractElement $element)
    {
        $this->elementTypes[ $element->getName() ] = $element;

        return true;
    }

    /**
     * @param $name
     * @return bool
     */
    public function unregisterElementType($name)
    {
        if (!isset($this->elementTypes[ $name ])) {
            return false;
        }

        unset($this->elementTypes[ $name ]);

        return true;
    }

    /**
     * @param null $elementName
     * @return mixed|null
     */
    public function getElementTypes($elementName = null)
    {
        if (null === $this->elementTypes) {
            $this->initElements();
        }

        if (null !== $elementName) {
            return $this->elementTypes[$elementName] ?? null;
        }

        return $this->elementTypes;
    }

    /**
     * @return array
     */
    public function getElementTypesConfig()
    {
        $config = [];
        /** @var AbstractElement $element */
        foreach ($this->getElementTypes() as $element) {
            $config[ $element->getName() ] = $element->getConfig();
        }

        return $config;
    }

    /**
     * Print out the child content HTML
     *
     * @return void
     */
    public function renderElementsContent()
    {
        foreach ($this->getElementTypes() as $elementType) {
            $elementType->printTemplate();
        }
    }

    /**
     * Init elements
     */
    private function initElements()
    {
        $this->elementTypes = [];
        $this->registerElementType(ObjectManagerHelper::get(Section::class));
        $this->registerElementType(ObjectManagerHelper::get(Column::class));
        $this->registerElementType(ObjectManagerHelper::get(Repeater::class));
        HooksHelper::doAction('pagebuilder/elements/elements_registered');
    }

    /**
     * Init categories
     */
    private function initCategories()
    {
        $this->categories = [
            'basic' => [
                'title' => __('Basic'),
            ],
            'general' => [
                'title' => __('General'),
            ],
            'products' => [
                'title' => __('Products'),
            ],
            'magento' => [
                'title' => __('Magento'),
            ],
            'form' => [
                'title' => __('Form'),
            ],
            'external' => [
                'title' => __('External'),
            ],
        ];

        HooksHelper::doAction('pagebuilder/elements/categories_registered', $this);
    }
}
