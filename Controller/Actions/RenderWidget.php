<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\ObjectManagerHelper;
use Goomento\PageBuilder\Model\BetterCaching;
use Goomento\PageBuilder\Model\ContentRegistry;
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
     * @var BetterCaching
     */
    private $betterCaching;
    /**
     * @var ContentRegistry
     */
    private $contentRegistry;

    /**
     * @param PageFactory $pageFactory
     * @param FilterProvider $filterProvider
     * @param BetterCaching $betterCaching
     * @param ContentRegistry $contentRegistry
     */
    public function __construct(
        PageFactory $pageFactory,
        FilterProvider $filterProvider,
        BetterCaching $betterCaching,
        ContentRegistry $contentRegistry
    )
    {
        $this->pageFactory = $pageFactory;
        $this->filterProvider = $filterProvider;
        $this->betterCaching = $betterCaching;
        $this->contentRegistry = $contentRegistry;
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
        $collect = function () use ($actionData, $params) {

            $this->init();

            HooksHelper::doAction('pagebuilder/editor/render_widget');

            $contentId = (int) $params['content_id'];

            $result = '';

            if ($contentId && $content = $this->contentRegistry->getById($contentId)) {
                $documentManager = ObjectManagerHelper::getDocumentsManager();

                $document = $documentManager->getByContent( $content );
                $result = $document->renderElement($actionData['data']);

                if (!empty($result)) {
                    $result = $this->filterProvider->getBlockFilter()->filter($result);
                }
            }

            return $result;
        };

        $rendered = $this->betterCaching->resolve($actionData, $collect);

        return [
            'render' => $rendered
        ];
    }
}
