<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\PageBuilder\Helper\HooksHelper;
use Goomento\PageBuilder\PageBuilder;
use Magento\Framework\App\Action\HttpPostActionInterface;

class Json extends AbstractAjax implements HttpPostActionInterface
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        /**
         * Start possessing ajax request
         */
        HooksHelper::doAction('pagebuilder/ajax/processing');

        return $this->setResponseData(
            HooksHelper::applyFilters('pagebuilder/ajax/response', [])
        )->sendResponse();
    }
}
