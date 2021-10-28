<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Goomento\PageBuilder\Builder\Managers\Documents;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\View\Result\PageFactory;

class RenderWidget extends AbstractActions
{
    /**
     * Initialize the program
     * @var bool
     */
    private $init = false;

    /**
     * Make sure the layout is loaded, thus we can render design
     */
    private function renderPage()
    {
        ObjectManagerHelper::get(
            PageFactory::class
        )->create();
    }

    /**
     * Init the Goomento Page Builder
     */
    private function init()
    {
        if (false === $this->init) {
            $this->init = true;
            PageBuilder::initialize();
            $this->renderPage();
        }
    }


    /**
     * @inheirtDoc
     */
    public function doAction($actionData, $params = [])
    {
        $this->init();
        /** @var Documents $documentManager */
        $documentManager = ObjectManagerHelper::get(Documents::class);
        $document = $documentManager->get($params['content_id']);
        return [
            'render' => $document->renderElement($actionData['data'])
        ];
    }
}
