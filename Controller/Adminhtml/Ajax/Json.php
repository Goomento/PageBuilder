<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\Traits\TraitHttpContentAction;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Json extends AbstractAjax implements HttpPostActionInterface
{
    use TraitHttpContentAction;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $response = [
            'success' => false,
            'data' => [],
        ];

        try {
            /**
             * Start possessing ajax request
             */
            HooksHelper::doAction('pagebuilder/ajax/processing', $this->getContent(true), $this->getRequest()->getParams());

            /**
             * Get the responded data
             */
            $response = (array) HooksHelper::applyFilters(
                'pagebuilder/ajax/return_data',
                [],
                $this->getContent(true)
            )->getResult();
        } catch (LocalizedException $e) {
            $response['data'] = $e->getMessage();
        } catch (\Exception $e) {
            $response['data'] = (string) __('Something went wrong when render your action.');
        }

        return $this->setResponseData($response)->sendResponse();
    }
}
