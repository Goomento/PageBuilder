<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Managers;

use Goomento\PageBuilder\Builder\Base\Element;
use Goomento\PageBuilder\Builder\Elements\Column;
use Goomento\PageBuilder\Builder\Elements\Repeater;
use Goomento\PageBuilder\Builder\Elements\Section;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Elements
 * @package Goomento\PageBuilder\Builder\Managers
 */
class Elements
{
    /**
     * @var
     */
    private $_element_types;

    /**
     * @var
     */
    private $categories;

    /**
     * @param array $element_data
     * @param array $element_args
     * @param Element|null $element_type
     * @return Element|mixed|null
     */
    public function createElementInstance(array $element_data, array $element_args = [], Element $element_type = null)
    {
        if (null === $element_type) {
            /** @var Widgets $widgetManager */
            $widgetManager = StaticObjectManager::get(Widgets::class);
            if ('widget' === $element_data['elType']) {
                $element_type = $widgetManager->getWidgetTypes($element_data['widgetType']);
            } else {
                $element_type = $this->getElementTypes($element_data['elType']);
            }
        }

        if (! $element_type) {
            return null;
        }

        $args = array_merge($element_type->getDefaultArgs(), $element_args);

        $element_class = get_class($element_type);

        try {
            $element = new $element_class($element_data, $args);
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
     * @param $category_name
     * @param $category_properties
     */
    public function addCategory($category_name, $category_properties)
    {
        if (null === $this->categories) {
            $this->getCategories();
        }

        if (! isset($this->categories[ $category_name ])) {
            $this->categories[ $category_name ] = $category_properties;
        }
    }

    /**
     * @param Element $element
     * @return bool
     */
    public function registerElementType(Element $element)
    {
        $this->_element_types[ $element->getName() ] = $element;

        return true;
    }

    /**
     * @param $name
     * @return bool
     */
    public function unregisterElementType($name)
    {
        if (! isset($this->_element_types[ $name ])) {
            return false;
        }

        unset($this->_element_types[ $name ]);

        return true;
    }

    /**
     * @param null $element_name
     * @return mixed|null
     */
    public function getElementTypes($element_name = null)
    {
        if (is_null($this->_element_types)) {
            $this->initElements();
        }

        if (null !== $element_name) {
            return $this->_element_types[$element_name] ?? null;
        }

        return $this->_element_types;
    }

    /**
     * @return array
     */
    public function getElementTypesConfig()
    {
        $config = [];
        /** @var Element $element */
        foreach ($this->getElementTypes() as $element) {
            $config[ $element->getName() ] = $element->getConfig();
        }

        return $config;
    }

    public function renderElementsContent()
    {
        foreach ($this->getElementTypes() as $element_type) {
            $element_type->printTemplate();
        }
    }

    /**
     *
     */
    private function initElements()
    {
        $this->_element_types = [];
        $this->registerElementType(StaticObjectManager::get(Section::class));
        $this->registerElementType(StaticObjectManager::get(Column::class));
        $this->registerElementType(StaticObjectManager::get(Repeater::class));
        Hooks::doAction('pagebuilder/elements/elements_registered');
    }

    /**
     *
     */
    private function initCategories()
    {
        $this->categories = [
            'basic' => [
                'title' => __('Basic'),
                'icon' => 'fas fa-font',
            ],
            'general' => [
                'title' => __('General'),
                'icon' => 'fas fa-font',
            ],
            'extension' => [
                'title' => __('Extension'),
            ],
            'theme' => [
                'title' => __('Theme'),
            ],
            'products' => [
                'title' => __('Products'),
            ],
        ];

        Hooks::doAction('pagebuilder/elements/categories_registered', $this);
    }
}
