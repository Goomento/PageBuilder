<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Base\AbstractElement;
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Helper\HooksHelper;
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
     * @param array $elementData
     * @param array $elementArgs
     * @param AbstractElement|null $elementType
     * @return AbstractElement|mixed|null
     */
    public function createElementInstance(array $elementData, array $elementArgs = [], AbstractElement $elementType = null)
    {
        if (null === $elementType) {
            /** @var Widgets $widgetManager */
            $widgetManager = ObjectManagerHelper::get(Widgets::class);
            if ('widget' === $elementData['elType']) {
                $elementType = $widgetManager->getWidgetTypes($elementData['widgetType']);
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
            $element = new $elementClass($elementData, $args);
        } catch (\Exception $e) {
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
        if (is_null($this->elementTypes)) {
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
        ];

        HooksHelper::doAction('pagebuilder/elements/categories_registered', $this);
    }
}
