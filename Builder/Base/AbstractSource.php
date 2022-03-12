<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Base;

use Goomento\PageBuilder\Builder\Managers\Controls;
use Goomento\PageBuilder\Builder\Managers\Elements;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

abstract class AbstractSource extends AbstractEntity
{
    const NAME = 'base';

    const TYPE = 'source';

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
    abstract public function exportTemplate(int $template_id);

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
        return ContentHelper::iterateData($content, function ($element) {
            $element['id'] = DataHelper::generateRandomString();

            return $element;
        });
    }

    /**
     * Process content for export.
     *
     * Process the content and all the inner elements, and prepare all the
     * elements data for export/import.
     *
     *
     * @param array $content A set of elements.
     * @return mixed Processed content data.
     */
    protected function processExportContent($content)
    {
        return ContentHelper::iterateData(
            $content,
            function ($element_data) {
                /** @var Elements $elementsManager */
                $elementsManager = ObjectManagerHelper::get(Elements::class);
                $element = $elementsManager->createElementInstance($element_data);

                if (!$element) {
                    return null;
                }

                return $this->processExportElement($element);
            }
        );
    }


    /**
     * Process content for import.
     *
     * Process the content and all the inner elements, and prepare all the
     * elements data for export/import.
     *
     *
     * @param array $content A set of elements.
     * @return mixed Processed content data.
     */
    protected function processImportContent($content)
    {
        return ContentHelper::iterateData(
            $content,
            function ($element_data) {
                /** @var Elements $elementsManager */
                $elementsManager = ObjectManagerHelper::get(Elements::class);
                $element = $elementsManager->createElementInstance($element_data);

                // If the widget/element isn't exist, like a plugin that creates a widget but deactivated
                if (!$element) {
                    return null;
                }

                return $this->processImportElement($element);
            }
        );
    }

    /**
     * Process single element content for export.
     *
     * Process any given element and prepare the element data for export/import.
     *
     *
     * @param ControlsStack $element
     * @return array Processed element data.
     */
    protected function processExportElement(ControlsStack $element)
    {
        $elementData = $element->getData();

        if ($element instanceof ExportInterface) {
            $elementData = $element->onExport($elementData);
        }

        foreach ($element->getControls() as $control) {
            /** @var Controls $managersControls */
            $managersControls = ObjectManagerHelper::get(Controls::class);
            $controlClass = $managersControls->getControl($control['type']);

            if ($controlClass instanceof ExportInterface) {
                $setting = $element->getSettings($control['name']);
                $newSetting = $controlClass->onExport($element->getSettings($setting, $control));
                if ($setting !== $newSetting) {
                    $elementData['settings'][ $control['name'] ] = $setting;
                }
            }
        }

        return $elementData;
    }


    /**
     * Process single element content for export/import.
     *
     * Process any given element and prepare the element data for export/import.
     *
     *
     * @param ControlsStack $element
     * @return array Processed element data.
     */
    protected function processImportElement(ControlsStack $element)
    {
        $elementData = $element->getData();

        if ($element instanceof ImportInterface) {
            $elementData = $element->onImport($elementData);
        }

        foreach ($element->getControls() as $control) {
            /** @var Controls $managersControls */
            $managersControls = ObjectManagerHelper::get(Controls::class);
            $controlClass = $managersControls->getControl($control['type']);

            if ($controlClass instanceof ImportInterface) {
                $setting = $element->getSettings($control['name']);
                $newSetting = $controlClass->onImport($setting, $control);
                if ($setting !== $newSetting) {
                    $elementData['settings'][ $control['name'] ] = $newSetting;
                }
            }
        }

        return $elementData;
    }
}
