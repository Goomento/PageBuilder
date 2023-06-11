<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Builder\Modules;

use Exception;
use Goomento\PageBuilder\Api\Data\BuildableContentInterface;
use Goomento\PageBuilder\Builder\Base\AbstractModule;
use Goomento\PageBuilder\Exception\BuilderException;
use Goomento\PageBuilder\Helper\DataHelper;
use Goomento\PageBuilder\Helper\EscaperHelper;
use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Helper\LoggerHelper;
use Goomento\PageBuilder\Helper\UrlBuilderHelper;

class Ajax extends AbstractModule
{
    const NAME = 'ajax';

    /**
     * Ajax actions.
     *
     * Holds all the register ajax action.
     *
     *
     * @var array
     */
    private $registeredAjaxActions = [];

    /**
     * Ajax requests.
     *
     * Holds all the register ajax requests.
     *
     *
     * @var array
     */
    private $ajaxRequestingActions = [];

    /**
     * Ajax response data.
     *
     * Holds all the response data for all the ajax requests.
     *
     *
     * @var array
     */
    private $responseData = [];

    /**
     * Ajax response code.
     *
     *
     * @var int
     */
    private $responseCode = 200;

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
     * Initializing Goomento ajax manager.
     *
     */
    public function __construct()
    {
        HooksHelper::addAction('pagebuilder/ajax/processing', [ $this,'handleAjaxRequest' ]);
        HooksHelper::addAction('pagebuilder/ajax/return_data', [ $this,'handleAjaxResponse' ]);
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
     * @throws Exception
     */
    public function registerAjaxAction($tag, $callback)
    {
        if (! HooksHelper::didAction('pagebuilder/ajax/register_actions')) {
            throw new BuilderException(sprintf('Use `%s` hook to register ajax action', 'pagebuilder/ajax/register_actions'));
        }

        $this->registeredAjaxActions[ $tag ] = compact('tag', 'callback');
    }

    /**
     * Handle ajax request.
     *
     * Verify ajax nonce, and run all the registered actions for this request.
     *
     *
     */
    public function handleAjaxRequest(BuildableContentInterface $buildableContent, array $requestParams) : void
    {
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

        try {
            $this->ajaxRequestingActions = DataHelper::decode($requestParams['actions'] ?? '');

            $this->ajaxRequestingActions = EscaperHelper::filter($this->ajaxRequestingActions);
        } catch (Exception $e) {
            $this->addToCurrentResponseData(false, __('Invalid format.'), 400);
            $this->ajaxRequestingActions = [];
        }

        foreach ($this->ajaxRequestingActions as $actionId => $actionData) {

            $this->currentActionId = $actionId;

            if (!isset($actionData['action']) || !isset($this->registeredAjaxActions[  $actionData['action'] ])) {

                continue;
            }

            if ($buildableContent->getId()) {
                $actionData['data']['content_id'] = $buildableContent->getId();
            }

            try {
                // phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                $results = call_user_func(
                    $this->registeredAjaxActions[ $actionData['action'] ]['callback'],
                    $actionData['data'],
                    $buildableContent
                );

                if (false === $results) {
                    $this->addToCurrentResponseData(false);
                } else {
                    $this->addToCurrentResponseData(true, $results);
                }
            } catch (Exception $e) {
                LoggerHelper::error($e);
                if (DataHelper::isDebugMode()) {
                    $this->addToCurrentResponseData(false, $e->getMessage(), $e->getCode());
                } else {
                    $this->addToCurrentResponseData(false, __('Something went wrong. Please try again later.'), 502);
                }
            }
        }

        $this->currentActionId = null;

        $this->markAsSuccess();
    }

    /**
     * @param array $data
     * @param BuildableContentInterface $buildableContent
     * @return array
     */
    public function handleAjaxResponse(array $data, BuildableContentInterface $buildableContent)
    {
        $response = [
            'success' => true,
            'status_code' => $this->responseCode,
            'data' => [
                'responses' => $this->responseData,
            ],
        ];

        return array_merge($data, ['data' => $response]);
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

        return $this->ajaxRequestingActions[ $this->currentActionId ];
    }

    /**
     * @inheritDoc
     */
    protected function getInitSettings()
    {
        return [
            'url' => UrlBuilderHelper::getUrl('pagebuilder/ajax/json'),
            'actions' => [],
        ];
    }

    /**
     * Ajax success response.
     *
     * Send a JSON response data back to the ajax request, indicating success.
     */
    private function markAsSuccess()
    {
        while (ob_get_status()) {
            ob_end_clean();
        }

        $this->responseCode = 200;
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
    private function addToCurrentResponseData($success, $data = null, $code = 200)
    {
        $this->responseData[ $this->currentActionId ] = [
            'success' => $success,
            'code' => $code,
            'data' => $data,
        ];

        return $this;
    }
}
