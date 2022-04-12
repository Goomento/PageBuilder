<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Manage;

use Goomento\PageBuilder\Helper\EscaperHelper;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Exception;

class ReplaceUrls extends AbstractManage
{
    /**
     * @inheritdoc
     */
    const ADMIN_RESOURCE = 'Goomento_PageBuilder::manage_replace_urls';

    /**
     * @inheritdoc
     */
    protected function executePost()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $data = EscaperHelper::filter($data);
            $this->contentManagement->replaceUrls($data['from'], $data['to']);
            $this->messageManager->addSuccessMessage(__('Replaced URLs successfully.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(__('Something went wrong when replacing URLs'));
        }

        /** @var Redirect $resultRedirect */
        $result = $this->resultRedirectFactory->create();

        return $result->setRefererUrl();
    }
}
