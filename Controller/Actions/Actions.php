<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Exception;
use Goomento\Core\Model\Registry;
use Goomento\PageBuilder\Controller\AbstractAction;
use Goomento\PageBuilder\Helper\Data;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Logger\Logger;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;

class Actions extends AbstractAction implements HttpPostActionInterface
{
    use TraitHttpPage;
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;
    /**
     * @var array
     */
    private $actions;

    /**
     * @param Context $context
     * @param Logger $logger
     * @param Registry $registry
     * @param Data $dataHelper
     * @param ObjectManagerInterface $objectManager
     * @param array $actions
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Registry $registry,
        Data $dataHelper,
        ObjectManagerInterface $objectManager,
        array $actions = []
    ) {
        $this->objectManager = $objectManager;
        $this->actions = $actions;
        parent::__construct($context, $logger, $registry, $dataHelper);
    }

    /**
     * @param $actionName
     * @return AbstractActions
     * @throws LocalizedException
     */
    private function getAction($actionName)
    {
        $action = $this->actions[$actionName] ?? null;
        if (is_string($action)) {
            $action = $this->objectManager->get(
                $action
            );
        }

        if ($action instanceof AbstractActions) {
            return $action;
        }

        throw new LocalizedException(
            __('Invalid action model: %1', $actionName)
        );
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $postData = [];
        try {
            $postData = $this->getRequest()->getParams();
            $postData = EscaperHelper::filter($postData);
        } catch (Exception $e) {
            $this->redirect404Page();
        }

        try {
            $elementData = $postData['actions'];
            $elementData = (array) DataHelper::decode($elementData);
        } catch (Exception $e) {
            $elementData = [];
        }

        $results = [];
        foreach ($elementData as $actionId => $elementAction) {
            $results[$actionId] = [
                'code' => 200,
                'success' => true,
                'data' => []
            ];
            try {
                $actionName = $elementAction['action'];
                $action = $this->getAction($actionName);
                $results[$actionId]['data'] = $action->doAction($elementAction['data'], $postData);
            } catch (LocalizedException $e) {
                $results[$actionId]['message'] = $e->getMessage();
            } catch (Exception $e) {
                if (DataHelper::isDebugMode()) {
                    $results[$actionId]['message'] = $e->getMessage();
                } else {
                    $results[$actionId]['message'] = (string) __('Something went wrong when render your action.');
                }
            } finally {
                if (isset($e)) {
                    $this->logger->error($e);
                    $results[$actionId]['success'] = false;
                    $results[$actionId]['code'] = 520;
                }
            }
        }

        return $this->setResponseData(['data' => [
            'success' => true,
            'data' => [
                'responses' => $results
            ],
        ]])->sendResponse();
    }

    /**
     * @inheirtDoc
     */
    protected function getPageConfig()
    {
        return [];
    }
}
