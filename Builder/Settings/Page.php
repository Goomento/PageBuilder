<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Settings;

use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractSettings;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Page extends AbstractSettings
{
    /**
     * Content Model
     *
     * @var BuildableContentInterface
     */
    private $model;

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
        $this->model = $data['model'] ?? null;

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
        return $this->getName() . '-' . $this->model->getId();
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
        $documentManagers = ObjectManagerHelper::getDocumentsManager();
        $document = $documentManagers->getByContent( $this->model );
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
            'title' => __('%1 Settings', $this->model->getTitle()),
        ];
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
        if ($this->model) {

            $documentManager = ObjectManagerHelper::getDocumentsManager();
            $document = $documentManager->getByContent( $this->model );

            if ($document) {
                $controls = $document->getControls();

                foreach ($controls as $control_id => $args) {
                    $this->addControl($control_id, $args);
                }
            }
        }
    }
}
