<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

class Documents extends \Magento\Backend\App\Action
{
    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl('https://github.com/Goomento/PageBuilder/wiki');
        return $resultRedirect;
    }
}
