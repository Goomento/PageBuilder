<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Ajax;

use Goomento\PageBuilder\Helper\Hooks;

/**
 * Class Json
 * @package Goomento\PageBuilder\Controller\Adminhtml\Ajax
 */
class Json extends AbstractAjax
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $action = $this->getRequest()->getParam('action', '');

        if (!empty($action)) {
            Hooks::doAction('pagebuilder/ajax/' . $action);
        }

        return $this->setResponseData(
            Hooks::applyFilters('pagebuilder/ajax/response', [])
        )->sendResponse();
    }
}
