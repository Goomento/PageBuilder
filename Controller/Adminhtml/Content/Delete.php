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

/**
 * Class Delete
 * @package Goomento\PageBuilder\Controller\Adminhtml\Content
 */
class Delete extends AbstractContent implements HttpPostActionInterface
{
    use TraitContent;

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            $content = $this->getContent(true);
            $this->contentRepository->delete($content);
            $this->messageManager->addSuccessMessage(__('You deleted the content.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $e) {
            $this->logger->error($e);
            $this->messageManager->addErrorMessage(
                __('Something went wrong when deleting the content')
            );
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath(
            'pagebuilder/*/grid'
        );
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
