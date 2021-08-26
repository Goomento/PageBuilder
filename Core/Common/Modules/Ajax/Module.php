<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Core\Common\Modules\Ajax;

use Goomento\PageBuilder\Core\Base\Module as BaseModule;
use Goomento\PageBuilder\Helper\Hooks;
use Goomento\PageBuilder\Helper\StaticUrlBuilder;

/**
 * Class Module
 * @package Goomento\PageBuilder\Core\Common\Modules\Ajax
 */
class Module extends BaseModule
{
    const NONCE_KEY = 'goomento_ajax';

    /**
     * Ajax actions.
     *
     * Holds all the register ajax action.
     *
     *
     * @var array
     */
    private $ajax_actions = [];

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
    private $current_action_id = null;

    /**
     * Ajax manager constructor.
     *
     * Initializing SagoTheme ajax manager.
     *
     */
    public function __construct()
    {
        Hooks::addAction('pagebuilder/ajax/goomento_ajax', [ $this,'handleAjaxRequest' ]);
    }

    /**
     * Get module name.
     *
     * Retrieve the module name.
     *
     * @since  1.7.0
     *
     * @return string Module name.
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
        if (! Hooks::didAction('pagebuilder/ajax/register_actions')) {
            throw new \Exception(__('Use `%1` hook to register ajax action', 'pagebuilder/ajax/register_actions'));
        }

        $this->ajax_actions[ $tag ] = compact('tag', 'callback');
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
        $editor_post_id = 0;

        if (! empty($_REQUEST['editor_post_id'])) {
            $editor_post_id = (int) $_REQUEST['editor_post_id'];
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
        Hooks::doAction('pagebuilder/ajax/register_actions', $this);

        $this->requests = json_decode($_REQUEST['actions'], true);

        foreach ($this->requests as $id => $action_data) {
            $this->current_action_id = $id;

            if (! isset($this->ajax_actions[ $action_data['action'] ])) {
                $this->addResponseData(false, __('Action not found.'), 400);

                continue;
            }

            if ($editor_post_id) {
                $action_data['data']['editor_post_id'] = $editor_post_id;
            }

            try {
                $results = call_user_func($this->ajax_actions[ $action_data['action'] ]['callback'], $action_data['data'], $this);

                if (false === $results) {
                    $this->addResponseData(false);
                } else {
                    $this->addResponseData(true, $results);
                }
            } catch (\Exception $e) {
                $this->addResponseData(false, $e->getMessage(), $e->getCode());
            }
        }

        $this->current_action_id = null;

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
        if (! $this->current_action_id) {
            return false;
        }

        return $this->requests[ $this->current_action_id ];
    }

    protected function getInitSettings()
    {
        return [
            'url' => StaticUrlBuilder::getAjaxUrl(),
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

        Hooks::addFilter('pagebuilder/ajax/response', function ($r) use ($response) {
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
        Hooks::addFilter('pagebuilder/ajax/response', function ($r) use ($response, $code) {
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
     * @return Module An instance of ajax manager.
     */
    private function addResponseData($success, $data = null, $code = 200)
    {
        $this->response_data[ $this->current_action_id ] = [
            'success' => $success,
            'code' => $code,
            'data' => $data,
        ];

        return $this;
    }
}
