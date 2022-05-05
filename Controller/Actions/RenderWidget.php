<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\PageBuilder;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Result\PageFactory;

class RenderWidget extends AbstractActions
{
    /**
     * Initialize the program
     * @var bool
     */
    private $init = false;

    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var FilterProvider
     */
    private $filterProvider;

    /**
     * @param PageFactory $pageFactory
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        PageFactory $pageFactory,
        FilterProvider $filterProvider
    )
    {
        $this->pageFactory = $pageFactory;
        $this->filterProvider = $filterProvider;
    }

    /**
     * Init the Goomento Page Builder
     */
    private function init()
    {
        if (false === $this->init) {
            $this->init = true;
            PageBuilder::initialize();
            // Fixed issue when render widget that requires loaded element from XML
            $this->pageFactory->create();
        }
    }


    /**
     * @inheirtDoc
     */
    public function doAction($actionData, $params = [])
    {
        $this->init();
        HooksHelper::doAction('pagebuilder/editor/render_widget');
        $documentManager = ObjectManagerHelper::getDocumentsManager();
        $document = $documentManager->get($params['content_id']);
        $rendered = $document->renderElement($actionData['data']);
        if (!empty($rendered)) {
            $rendered = $this->filterProvider->getBlockFilter()->filter($rendered);
        }
        return [
            'render' => $rendered
        ];
    }
}
