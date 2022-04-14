<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller;

use Goomento\PageBuilder\Api\Data\ContentInterface;
use Goomento\PageBuilder\Model\ContentRegistry;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\Action\Redirect;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\State;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    protected $actionFactory;

    /**
     * Event manager
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Config primary
     *
     * @var State
     */
    protected $appState;

    /**
     * Url
     *
     * @var UrlInterface
     */
    protected $url;

    /**
     * Response
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var ContentRegistry
     */
    private $contentRegistry;

    /**
     * @param ActionFactory $actionFactory
     * @param ManagerInterface $eventManager
     * @param UrlInterface $url
     * @param ContentRegistry $contentRegistry
     * @param StoreManagerInterface $storeManager
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        ManagerInterface $eventManager,
        UrlInterface $url,
        ContentRegistry $contentRegistry,
        StoreManagerInterface $storeManager,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->eventManager = $eventManager;
        $this->url = $url;
        $this->contentRegistry = $contentRegistry;
        $this->storeManager = $storeManager;
        $this->response = $response;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = urldecode($identifier);
        $condition = new DataObject(['identifier' => $identifier, 'continue' => true]);
        $this->eventManager->dispatch(
            'pagebuilder_controller_router_match_before',
            ['router' => $this, 'condition' => $condition]
        );

        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            $this->response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->actionFactory->create(Redirect::class);
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $content = $this->contentRegistry->getByIdentifier($identifier);
        // Validate existed
        if (!$content || !$content->getId()) {
            return null;
        }
        // Validate status
        if (!$content->getIsActive() || $content->getStatus() !== ContentInterface::STATUS_PUBLISHED) {
            return null;
        }

        $storeIds = $content->getStoreIds();
        // Validate stores
        if (!in_array(0, $storeIds) && !in_array($this->storeManager->getStore()->getId(), $storeIds)) {
            return null;
        }

        $request
            ->setModuleName('pagebuilder')
            ->setControllerName('content')
            ->setActionName('published')
            ->setParam('content_id', $content->getId());

        $request->setAlias(UrlInterface::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->actionFactory->create(Forward::class);
    }
}
