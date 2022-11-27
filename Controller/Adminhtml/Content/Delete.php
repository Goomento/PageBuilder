<?php
/**
 * @package Goomento_PageBuilder
 * @link https://github.com/Goomento/PageBuilder
 */

declare(strict_types=1);

namespace Goomento\PageBuilder\Controller\Adminhtml\Content;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Exception;

class Delete extends AbstractContent implements HttpPostActionInterface
{
    use TraitContent;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $content = $this->getContent(true);
            $type = $content->getType();
            $this->buildableContentManagement->deleteBuildableContent($content);
            $this->messageManager->addSuccessMessage(__('You deleted the content.'));
            return $resultRedirect->setPath(
                'pagebuilder/*/grid',
                [
                    'type' => $type
                ]
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when deleting the content')
            );
        }

        return $resultRedirect->setRefererUrl();
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        try {
            return $this->_authorization->isAllowed(
                $this->getContentResourceName('delete')
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                $e->getMessage()
            );
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong when deleting content(s)')
            );
        }

        return false;
    }
}
