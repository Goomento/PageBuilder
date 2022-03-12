<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Settings;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Builder\Base\AbstractSettings;
use Goomento\PageBuilder\Helper\ContentHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;

class Page extends AbstractSettings
{
    /**
     * Content Model
     *
     * @var ContentInterface
     */
    private $pageModel;

    /**
     * @var int
     */
    private $contentId;

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
        if (!empty($data['id'])) {
            $this->contentId = (int) $data['id'];
            $this->pageModel = ContentHelper::get($this->contentId);
        }

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
        return $this->getName() . '-' . $this->contentId;
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
        /** @var Documents $documentManagers */
        $documentManagers = ObjectManagerHelper::get(Documents::class);
        $document = $documentManagers->get($this->contentId);
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
     * Register model controls.
     *
     * Used to add new controls to the page settings model.
     *
     */
    protected function registerControls()
    {
        // Check if it's a real model, or abstract (for example - on import )
        if ($this->contentId) {
            /** @var Documents $documentManager */
            $documentManager = ObjectManagerHelper::get(Documents::class);
            $document = $documentManager->get($this->contentId);

            if ($document) {
                $controls = $document->getControls();

                foreach ($controls as $control_id => $args) {
                    $this->addControl($control_id, $args);
                }
            }
        }
    }
}
