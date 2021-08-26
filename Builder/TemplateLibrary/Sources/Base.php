<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\TemplateLibrary\Sources;

use Goomento\PageBuilder\Builder\Base\ControlsStack;
use Goomento\PageBuilder\Builder\Data;
use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Base
 * @package Goomento\PageBuilder\Builder\TemplateLibrary\Sources
 */
abstract class Base
{
    /**
     * User meta.
     *
     * Holds the current user meta data.
     *
     *
     * @var array
     */
    private $user_meta;

    /**
     * Get template ID.
     *
     * Retrieve the template ID.
     *
     * @abstract
     */
    abstract public function getId();

    /**
     * Get template title.
     *
     * Retrieve the template title.
     *
     * @abstract
     */
    abstract public function getTitle();

    /**
     * Register template data.
     *
     * Used to register custom template data like a post type, a taxonomy or any
     * other data.
     *
     * @abstract
     */
    abstract public function registerData();

    /**
     * Get templates.
     *
     * Retrieve templates from the template library.
     *
     * @abstract
     *
     * @param array $args Optional. Filter templates list based on a set of
     *                    arguments. Default is an empty array.
     */
    abstract public function getItems($args = []);

    /**
     * Get template.
     *
     * Retrieve a single template from the template library.
     *
     * @abstract
     *
     * @param int $template_id The template ID.
     */
    abstract public function getItem($template_id);

    /**
     * Get template data.
     *
     * Retrieve a single template data from the template library.
     *
     * @abstract
     *
     * @param array $args Custom template arguments.
     */
    abstract public function getData(array $args);

    /**
     * Delete template.
     *
     * Delete template from the database.
     *
     * @abstract
     *
     * @param int $template_id The template ID.
     */
    abstract public function deleteTemplate($template_id);

    /**
     * Save template.
     *
     * Save new or update existing template on the database.
     *
     * @abstract
     *
     * @param array $template_data The template data.
     */
    abstract public function saveItem($template_data);

    /**
     * Update template.
     *
     * Update template on the database.
     *
     * @abstract
     *
     * @param array $new_data New template data.
     */
    abstract public function updateItem($new_data);

    /**
     * Export template.
     *
     * Export template to a file.
     *
     * @abstract
     *
     * @param int $template_id The template ID.
     */
    abstract public function exportTemplate($template_id);

    /**
     * Template library source base constructor.
     *
     * Initializing the template library source base by registering custom
     * template data.
     *
     */
    public function __construct()
    {
        $this->registerData();
    }

    /**
     * Replace elements IDs.
     *
     * For any given SagoTheme content/data, replace the IDs with new randomly
     * generated IDs.
     *
     *
     * @param array $content Any type of SagoTheme data.
     *
     * @return mixed Iterated data.
     */
    protected function replaceElementsIds($content)
    {
        return StaticObjectManager::get(Data::class)->iterateData($content, function ($element) {
            $element['id'] = \Goomento\PageBuilder\Builder\Utils::generateRandomString();

            return $element;
        });
    }

    /**
     * Process content for export/import.
     *
     * Process the content and all the inner elements, and prepare all the
     * elements data for export/import.
     *
     *
     * @param array  $content A set of elements.
     * @param string $method  Accepts either `on_export` to export data or
     *                        `on_import` to import data.
     *
     * @return mixed Processed content data.
     */
    protected function processExportImportContent($content, $method)
    {
        /** @var Data $dataObject */
        $dataObject = StaticObjectManager::get(Data::class);
        return $dataObject->iterateData(
            $content,
            function ($element_data) use ($method) {
                /** @var Elements $elementsManager */
                $elementsManager = StaticObjectManager::get(Elements::class);
                $element = $elementsManager->createElementInstance($element_data);

                // If the widget/element isn't exist, like a plugin that creates a widget but deactivated
                if (! $element) {
                    return null;
                }

                return $this->processElementExportImportContent($element, $method);
            }
        );
    }

    /**
     * Process single element content for export/import.
     *
     * Process any given element and prepare the element data for export/import.
     *
     *
     * @param ControlsStack $element
     * @param string         $method
     *
     * @return array Processed element data.
     */
    protected function processElementExportImportContent(ControlsStack $element, $method)
    {
        $element_data = $element->getData();

        if (method_exists($element, $method)) {
            // TODO: Use the internal element data without parameters.
            $element_data = $element->{$method}($element_data);
        }

        foreach ($element->getControls() as $control) {
            $control_class = StaticObjectManager::get(Controls::class)->getControl($control['type']);

            // If the control isn't exist, like a plugin that creates the control but deactivated.
            if (! $control_class) {
                return $element_data;
            }

            if (method_exists($control_class, $method)) {
                $element_data['settings'][ $control['name'] ] = $control_class->{$method}($element->getSettings($control['name']), $control);
            }

            // On Export, check if the control has an argument 'export' => false.
            if ('onExport' === $method && isset($control['export']) && false === $control['export']) {
                unset($element_data['settings'][ $control['name'] ]);
            }
        }

        return $element_data;
    }
}
