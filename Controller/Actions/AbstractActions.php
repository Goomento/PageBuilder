<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Actions;

use Goomento\PageBuilder\Controller\AbstractAction;
use Goomento\PageBuilder\Helper\StaticEncryptor;
use Goomento\PageBuilder\Traits\TraitHttpPage;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AbstractActions
 * @package Goomento\PageBuilder\Controller\Actions
 */
abstract class AbstractActions extends AbstractAction
{
    use TraitHttpPage;

    /**
     * @inheirtDoc
     */
    public function execute()
    {
        try {
            $this->validateToken();
            $postData = $this->getRequest()->getParams();
            $postData = (new \Zend_Filter_Input([], [], $postData))->getUnescaped();
        } catch (\Exception $e) {
            $this->redirect404Page();
        }

        try {
            $elementData = $postData['actions'];
            $elementData = (array) \Zend_Json::decode($elementData);
        } catch (\Exception $e) {
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
                $results[$actionId]['data'] = $this->doAction($elementAction['action'], $elementAction['data'], $postData);
            } catch (\Exception $e) {
                $results[$actionId]['success'] = false;
                $results[$actionId]['code'] = 520;
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
     * Go through each action then pass the data into
     *
     * @param $actionName
     * @param $actionData
     * @param array $params
     * @return array
     */
    protected abstract function doAction($actionName, $actionData, $params = []);

    /**
     * @return bool
     * @throws LocalizedException
     */
    protected function validateToken()
    {
        $token = $this->getRequest()->getParam(StaticEncryptor::ACCESS_TOKEN_PARAM);
        $isValid = StaticEncryptor::isAllowed($token, '');

        if ($isValid !== true) {
            throw new LocalizedException(
                __('Invalid access token.')
            );
        }
        return false;
    }

    /**
     * @inheirtDoc
     */
    protected function getPageConfig()
    {
        return [];
    }
}
