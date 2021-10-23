<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Goomento\PageBuilder\Core\DocumentsManager;
use Goomento\PageBuilder\Helper\StaticObjectManager;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RenderWidget
 * @package Goomento\PageBuilder\Controller\Actions
 */
class RenderWidget extends AbstractActions implements HttpPostActionInterface
{
    /**
     * Initialize the program
     * @var bool
     */
    private $init = false;

    /**
     * @throws LocalizedException
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
    protected function doAction($actionName, $actionData, $params = [])
    {
        $this->init();
        $documentManager = StaticObjectManager::get(DocumentsManager::class);
        $document = $documentManager->get($params['editor_post_id']);
        return [
            'render' => $document->renderElement($actionData['data'])
        ];
    }
}
