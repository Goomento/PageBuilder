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

class Json extends AbstractAjax implements HttpPostActionInterface
{
    use TraitHttpContentAction;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /**
         * Start possessing ajax request
         */
        HooksHelper::doAction('pagebuilder/ajax/processing', $this->getContent(true), $this->getRequest()->getParams());

        /**
         * Get the responsed data
         */
        $response = (array) HooksHelper::applyFilters('pagebuilder/ajax/return_data', [], $this->getContent(true));

        return $this->setResponseData($response)->sendResponse();
    }
}
