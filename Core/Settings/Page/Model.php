<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Settings\Page;

use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Core\Settings\Base\Model as BaseModel;
use Goomento\PageBuilder\Helper\StaticContent;
use Goomento\PageBuilder\Helper\StaticObjectManager;

/**
 * Class Model
 * @package Goomento\PageBuilder\Core\Settings\Page
 */
class Model extends BaseModel
{

    /**
     * Content Model
     *
     * @var \Goomento\PageBuilder\Api\Data\ContentInterface
     */
    private $pageModel;

    /**
     * Model constructor.
     *
     * Initializing SagoTheme page settings model.
     *
     *
     * @param array $data Optional. Model data. Default is an empty array.
     */
    public function __construct(array $data = [])
    {
        $this->pageModel = StaticContent::get($data['id']);

        parent::__construct($data);
    }

    /**
     * Get model name.
     *
     * Retrieve page settings model name.
     *
     *
     * @return string Model name.
     */
    public function getName()
    {
        return 'page-settings';
    }

    /**
     * Get model unique name.
     *
     * Retrieve page settings model unique name.
     *
     *
     * @return string Model unique name.
     */
    public function getUniqueName()
    {
        return $this->getName() . '-' . $this->pageModel->getId();
    }

    /**
     * Get CSS wrapper selector.
     *
     * Retrieve the wrapper selector for the page settings model.
     *
     *
     * @return string CSS wrapper selector.
     */
    public function getCssWrapperSelector()
    {
        $document = StaticObjectManager::get(DocumentsManager::class)->get($this->pageModel->getId());
        return $document->getCssWrapperSelector();
    }

    /**
     * Get panel page settings.
     *
     * Retrieve the panel setting for the page settings model.
     *
     *
     * @return array {
     *    Panel settings.
     *
     *    @type string $title The panel title.
     * }
     */
    public function getPanelPageSettings()
    {

        return [
            'title' => __('%1 Settings', $this->pageModel->getTitle()),
        ];
    }

    /**
     * On export post meta.
     *
     * When exporting data, check if the post is not using page template and
     * exclude it from the exported SagoTheme data.
     *
     *
     * @param array $element_data Element data.
     *
     * @return array Element data to be exported.
     */
    public function onExport($element_data)
    {
        return $element_data;
    }

    /**
     * Register model controls.
     *
     * Used to add new controls to the page settings model.
     *
     */
    protected function registerControls()
    {
        // Check if it's a real model, or abstract (for example - on import )
        if ($this->pageModel->getId()) {
            /** @var DocumentsManager $documentManager */
            $documentManager = StaticObjectManager::get(DocumentsManager::class);
            $document = $documentManager->get($this->pageModel->getId());

            if ($document) {
                $controls = $document->getControls();

                foreach ($controls as $control_id => $args) {
                    $this->addControl($control_id, $args);
                }
            }
        }
    }
}
