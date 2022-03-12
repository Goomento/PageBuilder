<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Goomento\PageBuilder\Builder\Base\AbstractModule;
use Goomento\PageBuilder\Configuration;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\EncryptorHelper;
use Goomento\PageBuilder\Helper\LoggerHelper;
use Goomento\PageBuilder\Helper\RequestHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

class Ajax extends AbstractModule
{

    /**
     * Ajax actions.
     *
     * Holds all the register ajax action.
     *
     *
     * @var array
     */
    private $ajaxCctions = [];

    /**
     * Ajax requests.
     *
     * Holds all the register ajax requests.
     *
     *
     * @var array
     */
    private $requests = [];

    /**
     * Ajax response data.
     *
     * Holds all the response data for all the ajax requests.
     *
     *
     * @var array
     */
    private $response_data = [];

    /**
     * Current ajax action ID.
     *
     * Holds all the ID for the current ajax action.
     *
     *
     * @var string|null
     */
    private $currentActionId = null;

    /**
     * Ajax manager constructor.
     *
     * Initializing SagoTheme ajax manager.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/processing', [ $this,'handleAjaxRequest' ]);
    }

    /**
     * Get module name.
     *
     * Retrieve the module name.
     *
     *
     * @return string AbstractModule name.
     */
    public function getName()
    {
        return 'ajax';
    }

    /**
     * Register ajax action.
     *
     * Add new actions for a specific ajax request and the callback function to
     * be handle the response.
     *
     *
     * @param string $tag Ajax request name/tag.
     * @param callable $callback The callback function.
     * @throws \Exception
     */
    public function registerAjaxAction($tag, $callback)
    {
        if (! HooksHelper::didAction('pagebuilder/ajax/register_actions')) {
            throw new \Exception(sprintf('Use `%s` hook to register ajax action', 'pagebuilder/ajax/register_actions'));
        }

        $this->ajaxCctions[ $tag ] = compact('tag', 'callback');
    }

    /**
     * Handle ajax request.
     *
     * Verify ajax nonce, and run all the registered actions for this request.
     *
     *
     */
    public function handleAjaxRequest()
    {
        $contentId = 0;

        if (!empty($_REQUEST['content_id'])) {
            $contentId = (int) $_REQUEST['content_id'];
        }

        /**
         * Register ajax actions.
         *
         * Fires when an ajax request is received and verified.
         *
         * Used to register new ajax action handles.
         *
         *
         * @param self $this An instance of ajax manager.
         */
        HooksHelper::doAction('pagebuilder/ajax/register_actions', $this);

        $actions = RequestHelper::getParam('actions');
        try {
            $this->requests = \Zend_Json::decode($actions);
        } catch (\Exception $e) {
            $this->addResponseData(false, __('Invalid format.'), 400);
            $this->requests = [];
        }
        $this->requests = EscaperHelper::filter($this->requests);
        foreach ($this->requests as $id => $actionData) {
            $this->currentActionId = $id;

            if (!isset($this->ajaxCctions[ $actionData['action'] ?? '' ])) {
                $this->addResponseData(false, __('Action not found.'), 400);

                continue;
            }

            if ($contentId) {
                $actionData['data']['content_id'] = $contentId;
            }

            try {
                $results = call_user_func($this->ajaxCctions[ $actionData['action'] ]['callback'], $actionData['data'], $this);

                if (false === $results) {
                    $this->addResponseData(false);
                } else {
                    $this->addResponseData(true, $results);
                }
            } catch (\Exception $e) {
                LoggerHelper::error($e);
                if (Configuration::DEBUG) {
                    $this->addResponseData(false, $e->getMessage(), $e->getCode());
                } else {
                    $this->addResponseData(false, __('Something went wrong. Please try again later.'), 502);
                }
            }
        }

        $this->currentActionId = null;

        $this->sendSuccess();
    }

    /**
     * Get current action data.
     *
     * Retrieve the data for the current ajax request.
     *
     *
     * @return bool|mixed Ajax request data if action exist, False otherwise.
     */
    public function getCurrentActionData()
    {
        if (!$this->currentActionId) {
            return false;
        }

        return $this->requests[ $this->currentActionId ];
    }

    /**
     * @inheritDoc
     */
    protected function getInitSettings()
    {
        $params = [
            '_query' => [
                EncryptorHelper::ACCESS_TOKEN => EncryptorHelper::createAccessToken()
            ]
        ];

        return [
            'url' => UrlBuilderHelper::getUrl('pagebuilder/ajax/json'),
            'actions' => [
                'render_widget' => UrlBuilderHelper::getFrontendUrl(
                    'pagebuilder/actions/actions',
                    $params
                ),
                'heartbeat' => UrlBuilderHelper::getUrl('pagebuilder/ajax/heartbeat')
            ],
        ];
    }

    /**
     * Ajax success response.
     *
     * Send a JSON response data back to the ajax request, indicating success.
     *
     */
    private function sendSuccess()
    {
        $response = [
            'success' => true,
            'data' => [
                'responses' => $this->response_data,
            ],
        ];

        while (ob_get_status()) {
            ob_end_clean();
        }

        HooksHelper::addFilter('pagebuilder/ajax/response', function ($r) use ($response) {
            return array_merge($r, [
                'status_code' => $response['data']['code'] ?? 200,
                'data' => $response,
            ]);
        });
    }

    /**
     * Ajax failure response.
     *
     * Send a JSON response data back to the ajax request, indicating failure.
     *
     *
     * @param null $code
     */
    private function sendError($code = null)
    {
        $response = [
            'success' => true,
            'data' => [
                'responses' => $this->response_data,
            ],
        ];
        HooksHelper::addFilter('pagebuilder/ajax/response', function ($r) use ($response, $code) {
            return array_merge($r, [
                'status_code' => $code,
                'data' => $response,
            ]);
        });
    }

    /**
     * Add response data.
     *
     * Add new response data to the array of all the ajax requests.
     *
     *
     * @param bool  $success True if the requests returned successfully, False
     *                       otherwise.
     * @param mixed $data    Optional. Response data. Default is null.
     *
     * @param int   $code    Optional. Response code. Default is 200.
     *
     * @return Ajax An instance of ajax manager.
     */
    private function addResponseData($success, $data = null, $code = 200)
    {
        $this->response_data[ $this->currentActionId ] = [
            'success' => $success,
            'code' => $code,
            'data' => $data,
        ];

        return $this;
    }
}
